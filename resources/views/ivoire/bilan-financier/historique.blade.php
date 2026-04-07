@extends('agent.layouts.template')

@section('content')
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="bilan-financier-container">
        <!-- Header -->
        <div class="bilan-header">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="{{ route($route_prefix . '.index') }}" class="btn-back">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <div>
                    <h1 class="header-title">Journal des Encaissements</h1>
                    <p class="header-subtitle text-muted">Historique complet des transactions financières de votre site (Côte d'Ivoire)</p>
                </div>
            </div>
            <div class="header-right d-none d-md-block">
                <div class="date-pill">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Table Historique -->
        <div class="history-section">
            <div class="analytics-card border-0 shadow-lg">
                <div class="card-header-modern mb-4">
                    <div class="d-flex align-items-center">
                        <div class="header-icon-small"><i class="fas fa-history"></i></div>
                        <h5 class="mb-0 ms-3 font-weight-bold text-secondary">Journal des flux financiers</h5>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover modern-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Date & Heure</th>
                                <th>Référence Colis</th>
                                <th>Montant</th>
                                <th>Méthode</th>
                                <th>Agent</th>
                                <th class="text-end pe-4">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paiements as $paiement)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">{{ $paiement->created_at->format('d M Y') }}</span>
                                            <small class="text-muted">{{ $paiement->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ref-badge">#{{ $paiement->colis->reference_colis ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="amount-text">
                                            {{ number_format($paiement->montant, 0, ',', ' ') }}
                                            <small class="text-muted">{{ $paiement->devise ?? ($paiement->colis->agenceExpedition->devise ?? 'XOF') }}</small>
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $methodClass = strtolower(str_replace(' ', '-', $paiement->methode_paiement));
                                            $icon = 'fas fa-money-bill-wave';
                                            if(str_contains($methodClass, 'virement')) $icon = 'fas fa-university';
                                            if(str_contains($methodClass, 'mobile')) $icon = 'fas fa-mobile-alt';
                                            if(str_contains($methodClass, 'cheque')) $icon = 'fas fa-money-check';
                                            if(str_contains($methodClass, 'livraison')) $icon = 'fas fa-truck-loading';
                                        @endphp
                                        <div class="method-badge {{ $methodClass }}">
                                            <i class="{{ $icon }} me-2"></i>
                                            {{ ucfirst(str_replace('_', ' ', $paiement->methode_paiement)) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="agent-info">
                                            <div class="agent-avatar">{{ substr($paiement->agent_name ?? 'A', 0, 1) }}</div>
                                            <span class="agent-name">{{ $paiement->agent_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="notes-text" title="{{ $paiement->notes }}">
                                            {{ Str::limit($paiement->notes, 30) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
                                            <p class="mt-3 text-muted fw-bold">Aucune transaction trouvée pour cette agence.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination-wrapper mt-4 ps-4 pe-4">
                    {{ $paiements->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        :root { --primary: #fea219; --secondary: #1e293b; --bg: #f8fafc; }
        .bilan-financier-container { padding: 30px; background: var(--bg); min-height: 100vh; font-family: 'Inter', sans-serif; }
        .bilan-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; background: white; padding: 30px; border-radius: 24px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        .header-title { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 800; color: var(--secondary); margin: 0; letter-spacing: -0.02em; }
        .btn-back { width: 50px; height: 50px; background: #f1f5f9; color: var(--secondary); border-radius: 15px; display: flex; align-items: center; justify-content: center; transition: 0.3s; text-decoration: none !important; }
        .btn-back:hover { background: var(--primary); color: white; transform: translateX(-5px); }
        .date-pill { background: #f1f5f9; color: #64748b; padding: 10px 20px; border-radius: 50px; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; }
        .analytics-card { background: white; border-radius: 28px; padding: 35px 0; }
        .header-icon-small { width: 40px; height: 40px; background: #fff7ed; color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .modern-table { border-collapse: separate; border-spacing: 0 10px; margin-top: -10px; }
        .modern-table thead th { border: none; padding: 15px; color: #94a3b8; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
        .modern-table tbody tr { background: white; transition: 0.2s; cursor: pointer; }
        .modern-table tbody tr:hover { background: #f8fafc; transform: scale(1.005); }
        .modern-table tbody td { vertical-align: middle; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; padding: 20px 15px; }
        .modern-table tbody td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
        .modern-table tbody td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 16px; border-bottom-right-radius: 16px; }
        .ref-badge { background: #f1f5f9; color: var(--secondary); padding: 6px 12px; border-radius: 8px; font-family: 'Outfit'; font-weight: 700; font-size: 13px; }
        .amount-text { font-family: 'Outfit'; font-size: 16px; font-weight: 800; color: #10b981; }
        .method-badge { display: inline-flex; align-items: center; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; background: #f1f5f9; color: #64748b; }
        .method-badge.espece { background: #ecfdf5; color: #10b981; }
        .method-badge.virement_bancaire { background: #eff6ff; color: #3b82f6; }
        .method-badge.mobile_money { background: #f5f3ff; color: #8b5cf6; }
        .agent-info { display: flex; align-items: center; gap: 10px; }
        .agent-avatar { width: 30px; height: 30px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; }
        .agent-name { font-size: 13px; font-weight: 600; color: #475569; }
        .notes-text { font-size: 12px; color: #94a3b8; font-style: italic; }
        .empty-state { padding: 40px; }
        .empty-icon { font-size: 48px; color: #e2e8f0; }
        .pagination-wrapper .pagination { gap: 5px; }
        .pagination-wrapper .page-link { border: none; padding: 10px 18px; border-radius: 12px; color: #64748b; font-weight: 700; background: #f1f5f9; }
        .pagination-wrapper .page-item.active .page-link { background: var(--primary); color: white; }
    </style>
@endsection