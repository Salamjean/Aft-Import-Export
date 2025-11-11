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

class UserAuthenticate extends Controller
{
    public function login(){
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

    
    public function register(){
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
            $user->save();

            return redirect()->route('login')->with('success', 'Votre compte a été créé avec succès. Vous pouvez vous connecter.');

        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.'])->withInput();
        }
    }
}
