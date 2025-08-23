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
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white !important;
        box-shadow: var(--shadow-light);
        transform: translateY(-2px);
    }


    .nav-tabs .nav-link:hover:not(.active) {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
        transform: translateY(-1px);
    }

    /* Título moderno */
    .modern-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 2rem;
        position: relative;
        display: flex;
        align-items: center;
    }

    .modern-title::before {
        content: '';
        width: 4px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        border-radius: 2px;
        margin-right: 16px;
    }

    /* Cards de alerta modernos */
    .alert-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .alert-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-red), #ec7063);
    }

    .alert-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    .alert-card .card-body {
        flex-grow: 1;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .alert-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .alert-card-title::before {
        content: '⚠️';
        margin-right: 8px;
        font-size: 1.4rem;
    }

    .alert-meta {
        margin-bottom: 1rem;
    }

    .alert-meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .alert-meta-icon {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        color: var(--primary-green);
    }

    .alert-message {
        background: rgba(231, 76, 60, 0.1);
        border-left: 4px solid var(--primary-red);
        padding: 12px 16px;
        border-radius: 8px;
        margin: 1rem 0;
        color: var(--primary-dark);
        font-weight: 500;
    }

    /* Grupo de ações moderno */
    .modern-action-group {
        margin-top: auto;
        padding-top: 1rem;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .modern-action-btn {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        min-width: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .modern-action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
        pointer-events: none;
    }

    .modern-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
    }

    .btn-resolve {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
    }

    .btn-acknowledge {
        background: linear-gradient(135deg, var(--primary-blue), #5dade2);
        color: white;
    }

    /* Estado vazio moderno */
    .empty-state {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 3rem;
        text-align: center;
        box-shadow: var(--shadow-light);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: var(--primary-green);
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
        .container {
            margin-left: 0 !important;
            padding: 1rem;
        }
        
        .modern-action-group {
            flex-direction: column;
        }
        
        .modern-action-btn {
            min-width: auto;
        }
    }
</style>

<div class="container col-xl-7">

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

    <h2 class="modern-title animate-fade-in" style="animation-delay: 0.1s;">
        Alertas Ativos
    </h2>

    @if($alerts->isEmpty())
        <div class="empty-state animate-fade-in" style="animation-delay: 0.2s;">
            <div class="empty-state-icon">🛡️</div>
            <h3 class="empty-state-title">Tudo sob controle!</h3>
            <p class="empty-state-message">Nenhum alerta ativo no momento. Seu sistema está funcionando perfeitamente.</p>
        </div>
    @else
        <div class="row g-4 animate-fade-in" style="animation-delay: 0.2s;">
            @foreach($alerts as $index => $alert)
            <div class="col-md-6 animate-fade-in" style="animation-delay: {{ 0.3 + ($index * 0.1) }}s;">
                <div class="card alert-card">
                    <div class="card-body">
                        <h5 class="alert-card-title">{{ $alert->title }}</h5>
                        
                        <div class="alert-meta">
                            <div class="alert-meta-item">
                                <i class="bi bi-cpu alert-meta-icon"></i>
                                <strong>Dispositivo:</strong>
                                <span class="ms-2">{{ $alert->device->name ?? 'Não especificado' }}</span>
                            </div>
                            <div class="alert-meta-item">
                                <i class="bi bi-house alert-meta-icon"></i>
                                <strong>Ambiente:</strong>
                                <span class="ms-2">{{ $alert->environment->name ?? 'Não especificado' }}</span>
                            </div>
                        </div>

                        <div class="alert-message">
                            {{ $alert->message }}
                        </div>

                        <div class="modern-action-group">
                            <form action="{{ route('alerts.resolve', $alert->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn modern-action-btn btn-resolve" type="submit">
                                    <i class="bi bi-check-circle"></i>
                                    Resolver
                                </button>
                            </form>
                            <form action="{{ route('alerts.acknowledge', $alert->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn modern-action-btn btn-acknowledge" type="submit">
                                    <i class="bi bi-eye"></i>
                                    Marcar como lido
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="animate-fade-in" style="animation-delay: 0.5s;">
            {{ $alerts->links() }}
        </div>
    @endif
</div>

<!-- FontAwesome e Bootstrap Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">