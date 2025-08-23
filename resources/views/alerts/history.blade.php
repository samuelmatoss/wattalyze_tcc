@include('components.headerDash')

<style>
    /* Variáveis CSS para consistência */
    :root {
        --primary-dark: #2c3e50;
        --primary-green: #27ae60;
        --primary-red: #e74c3c;
        --primary-orange: #e67e22;
        --primary-blue: #3498db;
        --bg-light: #f8f9fa;
        --text-muted: #6c757d;
        --border-radius: 16px;
        --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.08);
        --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s ease;
    }

    /* Body e Container */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        
    }

    .container {
        margin-left: 25vw !important;
    }

    .ma {
        margin-left: 30vw !important;
    }

    /* Navegação com abas moderna */
    .nav-tabs {
        border: none;
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 8px;
        box-shadow: var(--shadow-light);
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        color: var(--text-muted);
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 12px 20px;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .nav-tabs .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        opacity: 0;
        transition: var(--transition);
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71ff);
        color: white !important;
        box-shadow: var(--shadow-light);
        transform: translateY(-2px);
    }

    .nav-tabs .nav-link:hover:not(.active) {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    /* Tabela moderna */
    .modern-table-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        overflow: hidden;
        transition: var(--transition);
    }

    .modern-table-container:hover {
        box-shadow: var(--shadow-medium);
    }

    table.table {
        margin: 0;
        border: none;
    }

    table.table thead {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
    }

    table.table thead th {
        border: none;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 1.2rem 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    table.table tbody tr {
        border: none;
        transition: var(--transition);
        position: relative;
    }

    table.table tbody tr::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 0;
        height: 100%;
        background: var(--primary-green);
        transition: var(--transition);
    }

    table.table tbody tr:hover {
        background: rgba(39, 174, 96, 0.05);
        transform: translateX(4px);
    }

    table.table tbody tr:hover::before {
        width: 4px;
    }

    table.table tbody td {
        border: none;
        padding: 1.2rem 1rem;
        vertical-align: middle;
        font-weight: 500;
        color: var(--primary-dark);
    }

    /* Badges modernos */
    .modern-status-badge {
        background: linear-gradient(135deg, var(--badge-color), var(--badge-color-dark));
        color: white;
        border: none;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        box-shadow: var(--shadow-light);
    }

    .modern-status-badge.resolved {
        --badge-color: #27ae60;
        --badge-color-dark: #229954;
    }

    .modern-status-badge.active {
        --badge-color: #f39c12;
        --badge-color-dark: #e67e22;
        color: var(--primary-dark);
    }

    .modern-status-badge i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    /* Células com ícones */
    .table-cell-with-icon {
        display: flex;
        align-items: center;
    }

    .table-icon {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        color: var(--primary-green);
    }

    /* Data formatada */
    .formatted-date {
        font-weight: 600;
        color: var(--primary-dark);
    }

    .formatted-time {
        color: var(--text-muted);
        font-size: 0.85rem;
        display: block;
        margin-top: 2px;
    }

    /* Estado vazio moderno */
    .empty-state {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 3rem;
        text-align: center;
        box-shadow: var(--shadow-light);
        margin-top: 2rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: var(--text-muted);
    }

    .empty-state-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 0.5rem;
    }

    .empty-state-message {
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    /* Paginação moderna */
    .pagination {
        justify-content: center !important;
        margin-top: 2rem;
    }

    .pagination .page-item .page-link {
        border: none;
        border-radius: 12px;
        margin: 0 4px;
        padding: 10px 16px;
        color: var(--text-muted);
        background: rgba(255, 255, 255, 0.8);
        transition: var(--transition);
    }

    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
        box-shadow: var(--shadow-light);
    }

    .pagination .page-item .page-link:hover {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    /* Animações */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .container,
        .ma {
            margin-left: 0 !important;
            padding: 1rem;
        }
        
        .modern-table-container {
            overflow-x: auto;
        }
        
        table.table tbody tr:hover {
            transform: none;
        }
    }
</style>

<div class="container col-xl-7 ma">

    {{-- Navegação com abas --}}
    <ul class="nav nav-tabs animate-fade-in">
        <li class="nav-item">
            <a href="{{ route('alerts.rules') }}" class="nav-link {{ request()->routeIs('alerts.rules') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i>Regras de Alerta
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('alerts.active') }}" class="nav-link {{ request()->routeIs('alerts.active') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle me-2"></i>Alertas Ativos
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('alerts.history') }}" class="nav-link {{ request()->routeIs('alerts.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>Histórico
            </a>
        </li>
    </ul>

    @if($alerts->isEmpty())
        <div class="empty-state animate-fade-in" style="animation-delay: 0.2s;">
            <div class="empty-state-icon">📋</div>
            <h3 class="empty-state-title">Histórico vazio</h3>
            <p class="empty-state-message">Nenhum alerta foi registrado ainda em seu sistema.</p>
        </div>
    @else
        <div class="modern-table-container animate-fade-in" style="animation-delay: 0.2s;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="bi bi-card-text me-2"></i>Título</th>
                            <th><i class="bi bi-cpu me-2"></i>Dispositivo</th>
                            <th><i class="bi bi-house me-2"></i>Ambiente</th>
                            <th><i class="bi bi-info-circle me-2"></i>Status</th>
                            <th><i class="bi bi-calendar3 me-2"></i>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                        <tr>
                            <td>
                                <div class="table-cell-with-icon">
                                    <i class="bi bi-exclamation-triangle table-icon"></i>
                                    <strong>{{ $alert->title }}</strong>
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-with-icon">
                                    <i class="bi bi-cpu table-icon"></i>
                                    {{ $alert->device->name ?? 'Não especificado' }}
                                </div>
                            </td>
                            <td>
                                <div class="table-cell-with-icon">
                                    <i class="bi bi-house table-icon"></i>
                                    {{ $alert->environment->name ?? 'Não especificado' }}
                                </div>
                            </td>
                            <td>
                                @if($alert->is_resolved)
                                    <span class="modern-status-badge resolved">
                                        <i class="bi bi-check-circle"></i>
                                        Resolvido
                                    </span>
                                @else
                                    <span class="modern-status-badge active">
                                        <i class="bi bi-exclamation-circle"></i>
                                        Ativo
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="formatted-date">{{ $alert->created_at->format('d/m/Y') }}</div>
                                <div class="formatted-time">{{ $alert->created_at->format('H:i') }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="animate-fade-in" style="animation-delay: 0.4s;">
            {{ $alerts->links() }}
        </div>
    @endif

</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">