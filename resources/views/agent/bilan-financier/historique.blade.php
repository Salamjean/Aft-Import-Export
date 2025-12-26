@extends('agent.layouts.template')

@section('content')
    <div class="bilan-financier-container">
        <!-- Header -->
        <div class="bilan-header">
            <div class="header-content">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('agent.bilan_financier.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="header-title">Mes Encaissements Personnel</h1>
                        <p class="header-subtitle">Historique des paiements que vous avez enregistré</p>
                    </div>
                </div>
            </div>
            <div class="header-actions">
                <div class="date-display">
                    <i class="fas fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
                </div>
            </div>
        </div>

        <!-- Table Historique -->
        <div class="history-section">
            <div class="analytics-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-list"></i> Liste de mes transactions</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover CustomTable">
                        <thead>
                            <tr>
                                <th class="text-center">Date & Heure</th>
                                <th class="text-center">Référence Colis</th>
                                <th class="text-center">Montant</th>
                                <th class="text-center">Méthode</th>
                                <th class="text-center">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paiements as $paiement)
                                <tr>
                                    <td class="text-center">{{ $paiement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center"><span
                                            class="badge badge-outline-dark">#{{ $paiement->colis->reference_colis ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-success font-weight-bold">
                                            {{ number_format($paiement->montant, 0, ',', ' ') }}
                                            {{ $paiement->colis->agenceExpedition->devise ?? 'FCFA' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="payment-method">
                                            {{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement)) }}
                                        </span>
                                    </td>
                                    <td class="text-center"><small
                                            class="text-muted">{{ Str::limit($paiement->notes, 50) }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Vous n'avez pas encore enregistré de paiements.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $paiements->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .bilan-financier-container {
            padding: 20px;
            background: #f8fafc;
            min-height: 100vh;
        }

        .bilan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        .header-subtitle {
            color: #718096;
            margin: 5px 0 0 0;
            font-size: 16px;
        }

        .date-display {
            background: #fea219;
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            width: 45px;
            height: 45px;
            background: #f1f5f9;
            color: #1a202c;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            text-decoration: none !important;
        }

        .btn-back:hover {
            background: #fea219;
            color: white;
        }

        .analytics-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .CustomTable thead th {
            background: #f8fafc;
            border: none;
            padding: 15px;
            font-size: 13px;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .CustomTable tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f1f5f9;
            font-size: 14px;
        }

        .badge-outline-dark {
            border: 1px solid #1a202c;
            color: #1a202c;
            background: transparent;
            font-weight: 600;
        }

        .gap-3 {
            gap: 1rem;
        }
    </style>
@endsection