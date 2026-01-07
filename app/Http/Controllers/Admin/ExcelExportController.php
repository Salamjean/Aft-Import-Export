<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colis;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ColisExport;

class ExcelExportController extends Controller
{
    public function exportColis(Request $request)
    {
        $filename = "export_colis_" . date('d_m_Y') . ".xlsx";
        return Excel::download(new ColisExport($request), $filename);
    }

    public function importColis(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('excel_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== chr(239) . chr(187) . chr(191)) {
            rewind($handle);
        }

        $header = fgetcsv($handle); // Read header

        $count = 0;
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 5)
                    continue;

                // Example mapping: Reference, Mode, Expediteur, Contact Exp, Destinataire, Contact Dest...
                Colis::updateOrCreate(
                    ['reference_colis' => $row[0]],
                    [
                        'mode_transit' => $row[1],
                        'name_expediteur' => $row[2],
                        'contact_expediteur' => $row[3],
                        'name_destinataire' => $row[4],
                        'contact_destinataire' => $row[5],
                        'agence_expedition' => $row[6],
                        'agence_destination' => $row[7],
                        'montant_total' => (float) ($row[8] ?? 0),
                        'statut' => 'valide',
                        'statut_paiement' => 'non_paye'
                    ]
                );
                $count++;
            }
            DB::commit();
            return back()->with('success', "$count colis importés ou mis à jour avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Erreur lors de l'importation : " . $e->getMessage());
        } finally {
            fclose($handle);
        }
    }
}
