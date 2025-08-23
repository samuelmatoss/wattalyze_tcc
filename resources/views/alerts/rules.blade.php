@include('components.headerDash')

<style>
    /* Variáveis CSS para consistência */
    :root {
        --primary-dark: #2c3e50;
        --primary-green: #27ae60;
        --primary-red: #e74c3c;
        --primary-orange: #e67e22;
        --primary-blue: #3498db;
        --primary-purple: #9b59b6;
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

    /* Cards modernos */
    .modern-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: var(--transition);
        position: relative;
    }

    .modern-card:hover {
        box-shadow: var(--shadow-medium);
        transform: translateY(-2px);
    }

    .modern-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), #2ecc71);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-dark), #34495e);
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
        border: none;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .card-body {
        padding: 2rem;
    }

    /* Formulários modernos */
    .form-label {
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label::before {
        content: '';
        width: 3px;
        height: 16px;
        background: var(--primary-green);
        border-radius: 2px;
        margin-right: 8px;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 500;
        transition: var(--transition);
        background: rgba(255, 255, 255, 0.8);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
        background: white;
    }

    /* Checkboxes modernos */
    .form-check {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0.2rem;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        transition: var(--transition);
    }

    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
        box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
    }

    .form-check-label {
        color: var(--primary-dark);
        font-weight: 600;
        margin-left: 0.5rem;
    }

    /* Botões modernos */
    .modern-btn {
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 12px;
        border: none;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .modern-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0%, transparent 100%);
        pointer-events: none;
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
    }

    .btn-primary.modern-btn {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
    }

    .btn-success.modern-btn {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
    }

    .btn-warning.modern-btn {
        background: linear-gradient(135deg, var(--primary-orange), #f39c12);
        color: white;
    }

    .btn-danger.modern-btn {
        background: linear-gradient(135deg, var(--primary-red), #ec7063);
        color: white;
    }

    .btn-secondary.modern-btn {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        color: white;
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

    /* Estado vazio moderno */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--primary-green);
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
        .card-body {
            padding: 1.5rem;
        }
        
        .modern-btn {
            padding: 10px 20px;
            font-size: 0.85rem;
        }
        
        table.table tbody tr:hover {
            transform: none;
        }
    }
</style>

<div class="container col-xl-7" style="margin-left: 30vw;">

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
        Regras de Alerta
    </h2>

    {{-- Formulário de criação --}}
    <div class="modern-card animate-fade-in" style="animation-delay: 0.2s;">
        <div class="card-header">
            <i class="bi bi-plus-circle me-2"></i>Nova Regra
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('alerts.rules.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" class="form-control" required placeholder="Digite o nome da regra">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="type" class="form-select" required>
                                <option value="">Selecione o tipo</option>
                                <option value="consumption_threshold">Limite de Consumo</option>
                                <option value="cost_threshold">Limite de Custo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Valor do Limite</label>
                            <input type="number" name="threshold_value" class="form-control" step="any" min="0" placeholder="0.00">
                            <small class="form-text text-muted">Opcional - deixe vazio se não aplicável</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Dispositivo</label>
                            <select name="device_id" class="form-select">
                                <option value="">Todos os dispositivos</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}">{{ $device->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Ambiente</label>
                            <select name="environment_id" class="form-select">
                                <option value="">Todos os ambientes</option>
                                @foreach($environments as $env)
                                    <option value="{{ $env->id }}">{{ $env->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Notificação</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="notification_channels[]" value="email" id="notifyEmail">
                                <label class="form-check-label" for="notifyEmail">
                                    <i class="bi bi-envelope me-2"></i>Email
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="modern-btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Criar Regra
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lista de regras existentes --}}
    <div class="modern-card animate-fade-in" style="animation-delay: 0.3s;">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>Regras Existentes
        </div>
        <div class="card-body p-0">
            @if($rules->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <h4>Nenhuma regra cadastrada</h4>
                    <p class="text-muted">Crie sua primeira regra de alerta usando o formulário acima.</p>
                </div>
            @else
                <div class="modern-table-container">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-tag me-2"></i>Nome</th>
                                    <th><i class="bi bi-gear me-2"></i>Tipo</th>
                                    <th><i class="bi bi-toggle-on me-2"></i>Status</th>
                                    <th class="text-center"><i class="bi bi-tools me-2"></i>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rules as $rule)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-check text-success me-2"></i>
                                            <strong>{{ $rule->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark px-3 py-2" style="font-weight: 600;">
                                            {{ ucwords(str_replace('_', ' ', $rule->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('alerts.rules.toggle', $rule->id) }}">
                                            @csrf
                                            <button type="submit" class="modern-btn btn-sm {{ $rule->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                <i class="bi bi-{{ $rule->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ $rule->is_active ? 'Ativa' : 'Inativa' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('alerts.rules.edit', $rule->id) }}" 
                                               class="modern-btn btn-warning btn-sm" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('alerts.rules.destroy', $rule->id) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" 
                                                        class="modern-btn btn-danger btn-sm" 
                                                        onclick="return confirm('Tem certeza que deseja excluir esta regra?')"
                                                        title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">