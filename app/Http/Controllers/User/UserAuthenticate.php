<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserAuthenticate extends Controller
{
    public function login()
    {
        // Vérifier si l'utilisateur est déjà authentifié
        if (auth('web')->check()) {
            return redirect()->route('user.dashboard');
        }
        return view('user.auth.login');
    }

    public function handleLogin(UserLoginRequest $request): RedirectResponse
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->route('login')->withErrors([
                'password' => 'Le mot de passe incorrect.',
            ]);
        }

        // Si l'authentification réussit, régénérer la session
        $request->session()->regenerate();

        // Rediriger vers la page de tableau de bord avec un message de succès
        return redirect()->intended(route('user.dashboard', absolute: false))->with('success', 'Bienvenue sur votre page!');
    }


    public function register()
    {
        return view('user.auth.register');
    }

    public function handleRegister(Request $request): RedirectResponse
    {
        try {
            // Validation des champs
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'adresse' => 'required|string|max:255',
                'pays' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'terms' => 'required|accepted',
            ], [
                'terms.required' => 'Vous devez accepter les termes et conditions.',
                'terms.accepted' => 'Vous devez accepter les termes et conditions.',
            ]);

            // Création de l'utilisateur
            $user = new User();
            $user->name = $validated['name'];
            $user->prenom = $validated['prenom'];
            $user->email = $validated['email'];
            $user->pays = $validated['pays'];
            $user->contact = $validated['contact'];
            $user->adresse = $validated['adresse'];
            $user->password = Hash::make($validated['password']);
            $user->terms_accepted = true;
            $user->terms_accepted_at = now();
            $user->save();

            return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Vous pouvez vous connecter.');
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }

    public function showLinkRequestForm()
    {
        return view('user.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = rand(1000, 9999);

        $user = User::where('email', $request->email)->first();
        $user->reset_code = $code;
        $user->reset_code_expires_at = Carbon::now()->addMinutes(60);
        $user->save();

        // Stocker l'email en session pour l'étape suivante
        $request->session()->put('reset_email', $request->email);

        Mail::send('emails.password_reset', [
            'code' => $code,
            'user' => $user
        ], function ($message) use ($request, $user) {
            $message->to($request->email, $user->name)
                ->subject('Votre code de réinitialisation');
        });

        return redirect()->route('password.reset')->with('success', 'Un code de réinitialisation a été envoyé à votre adresse e-mail.');
    }

    public function showResetForm()
    {
        return view('user.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
            'password' => 'required|min:8|confirmed'
        ]);

        $email = $request->session()->get('reset_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expirée. Veuillez recommencer la demande.']);
        }

        $user = User::where('email', $email)
            ->where('reset_code', $request->code)
            ->first();

        if (!$user) {
            return back()->withErrors(['code' => 'Le code de réinitialisation est invalide.']);
        }

        // Vérifier l'expiration
        if (Carbon::now()->isAfter($user->reset_code_expires_at)) {
            $user->update(['reset_code' => null, 'reset_code_expires_at' => null]);
            return back()->withErrors(['code' => 'Le code de réinitialisation a expiré.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_expires_at' => null
        ]);

        $request->session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé ! Vous pouvez maintenant vous connecter.');
    }
}
