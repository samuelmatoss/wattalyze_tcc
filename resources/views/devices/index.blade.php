@include('components.headerDash')

<style>
    .sidebar {
        height: 110vh;
    }

    /* Variáveis CSS para consistência */
    :root {
        --primary-dark: #2c3e50;
        --primary-green: #27ae60;
        --primary-red: #e74c3c;
        --primary-orange: #e67e22;
        --primary-blue: #3498db;
        --primary-yellow: #f1c40f;
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

    /* Container principal moderno */
    .main-container {
        margin-left: 20vw;
        padding: 2rem;
    }

    /* Header moderno */
    .modern-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        border-bottom: none;
    }

    .modern-header h1 {
        color: var(--primary-dark);
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }

    /* Botões modernos */
    .modern-btn {
        border-radius: 12px;
        font-weight: 500;
        padding: 8px 16px;
        border: none;
        transition: var(--transition);
        backdrop-filter: blur(10px);
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
    }

    .modern-btn-primary {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
    }

    .modern-btn-outline {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid #e9ecef;
        color: var(--text-muted);
    }

    .modern-btn-outline.active {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        color: white;
    }

    /* Filtros modernos */
    .filters-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .modern-input-group {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: var(--transition);
    }

    .modern-input-group:focus-within {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    .modern-input-group .input-group-text {
        background: transparent;
        border: none;
        color: var(--text-muted);
    }

    .modern-input-group .form-control {
        border: none;
        background: transparent;
        font-weight: 500;
    }

    .modern-input-group .form-control:focus {
        box-shadow: none;
    }

    .modern-select {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 500;
        transition: var(--transition);
    }

    .modern-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    /* Cards de estatísticas modernos */
    .stats-container {
        margin-bottom: 2rem;
    }

    .modern-stat-card {
        background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-dark) 100%);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .modern-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .modern-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    .modern-stat-card.dark {
        --card-color: #2c3e50;
        --card-color-dark: #34495e;
    }

    .modern-stat-card.green {
        --card-color: #27ae60;
        --card-color-dark: #229954;
    }

    .modern-stat-card.red {
        --card-color: #e74c3c;
        --card-color-dark: #c0392b;
    }

    .modern-stat-card.orange {
        --card-color: #e67e22;
        --card-color-dark: #d35400;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .stat-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 500;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Controles de visualização */
    .view-controls {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .modern-radio-group .btn-check:checked + .btn {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: white;
    }

    /* Cards de dispositivos modernos */
    .devices-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 2rem;
        min-height: 400px;
    }

    .device-card {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .device-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .device-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: var(--shadow-medium);
        border-color: var(--primary-green);
    }

    /* Badges modernos */
    .modern-badge {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
    }

    .modern-badge.success {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
        border-color: rgba(39, 174, 96, 0.3);
    }

    .modern-badge.danger {
        background: rgba(231, 76, 60, 0.1);
        color: var(--primary-red);
        border-color: rgba(231, 76, 60, 0.3);
    }

    .modern-badge.warning {
        background: rgba(230, 126, 34, 0.1);
        color: var(--primary-orange);
        border-color: rgba(230, 126, 34, 0.3);
    }

    /* Tabela moderna */
    .modern-table {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-light);
    }

    .modern-table thead th {
        background: var(--primary-dark);
        color: white;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .modern-table tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #f8f9fa;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(39, 174, 96, 0.05), rgba(39, 174, 96, 0.1));
        transform: scale(1.01);
    }

    /* Estado vazio moderno */
    .empty-state {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state i {
        color: rgba(44, 62, 80, 0.3);
        margin-bottom: 2rem;
    }

    .empty-state h4 {
        color: var(--primary-dark);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: var(--text-muted);
        margin-bottom: 2rem;
    }

    /* Modal moderno */
    .modal-content {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-medium);
        backdrop-filter: blur(10px);
    }

    .modal-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .modal-header h5 {
        color: var(--primary-dark);
        font-weight: 600;
    }

    /* Status indicators */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        box-shadow: 0 0 8px rgba(255,255,255,0.5);
    }

    .status-online {
        background-color: var(--primary-green);
    }

    .status-offline {
        background-color: var(--primary-red);
    }

    .status-maintenance {
        background-color: var(--primary-orange);
    }

    /* Loading moderno */
    .modern-loading {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 2rem;
        text-align: center;
    }

    .spinner-border {
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

    /* Chart container moderno */
    .chart-container {
        position: relative;
        height: 60px;
        width: 100%;
        background: rgba(248, 249, 250, 0.5);
        border-radius: 8px;
        overflow: hidden;
    }

    .chart-canvas {
        width: 100% !important;
        height: 60px !important;
    }

    .chart-loading {
        height: 60px;
        background: linear-gradient(90deg, rgba(39, 174, 96, 0.1) 25%, rgba(39, 174, 96, 0.2) 50%, rgba(39, 174, 96, 0.1) 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
        border-radius: 8px;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .main-container {
            margin-left: 0;
            padding: 1rem;
        }
        
        .modern-header {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.8rem;
        }
        
        .filters-card,
        .view-controls,
        .devices-container {
            padding: 1rem;
        }
    }

    /* Utilitários */
    .device-card-hidden {
        display: none !important;
    }

    .device-row-hidden {
        display: none !important;
    }
</style>

<body>
    <div class="col-xl-9 main-container">
        <!-- Header Moderno -->
        <div class="modern-header animate-fade-in">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <h1>Dispositivos</h1>
                <div class="btn-toolbar">
                    <div class="btn-group me-3">
                        <button type="button" class="btn modern-btn modern-btn-outline filter-btn" data-filter="online">
                            <span class="status-indicator status-online"></span> Online
                            <span class="modern-badge success ms-1" id="count-online">{{ $devices->where('status', 'online')->count() }}</span>
                        </button>
                        <button type="button" class="btn modern-btn modern-btn-outline filter-btn" data-filter="offline">
                            <span class="status-indicator status-offline"></span> Offline
                            <span class="modern-badge danger ms-1" id="count-offline">{{ $devices->where('status', 'offline')->count() }}</span>
                        </button>
                        <button type="button" class="btn modern-btn modern-btn-outline filter-btn active" data-filter="all">
                            Todos
                            <span class="modern-badge ms-1" style="background: rgba(108, 117, 125, 0.1); color: #495057;" id="count-all">{{ $devices->count() }}</span>
                        </button>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('devices.create') }}" class="btn modern-btn modern-btn-primary">
                            <i class="bi bi-plus"></i> Novo Dispositivo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros Modernos -->
        <div class="filters-card animate-fade-in" style="animation-delay: 0.1s;">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="modern-input-group input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchDevices" placeholder="Pesquisar dispositivos..." autocomplete="off">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select modern-select" id="filterEnvironment">
                        <option value="">Todos os ambientes</option>
                        @foreach($environments as $environment)
                        <option value="{{ $environment->id }}">{{ $environment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select modern-select" id="filterType">
                        <option value="">Todos os tipos</option>
                        @foreach($deviceTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn modern-btn modern-btn-outline w-100" id="resetFilters">
                        <i class="bi bi-arrow-clockwise"></i> Limpar
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Indicator Moderno -->
        <div id="loadingIndicator" class="modern-loading animate-fade-in" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>

        <!-- Cards de Estatísticas Modernos -->
        @php
        $stats = [
            'total' => $devices->count(),
            'online' => $devices->where('status', 'online')->count(),
            'offline' => $devices->where('status', 'offline')->count(),
            'maintenance' => $devices->where('status', 'maintenance')->count()
        ];
        @endphp

        <div class="row g-4 stats-container animate-fade-in" id="statsCards" style="animation-delay: 0.2s;">
            <div class="col-lg-3 col-md-6">
                <div class="card modern-stat-card dark text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stat-subtitle mb-2">Total</h6>
                                <h2 class="stat-number" id="stat-total">{{ $stats['total'] }}</h2>
                            </div>
                            <i class="bi bi-cpu-fill fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card modern-stat-card green text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stat-subtitle mb-2">Online</h6>
                                <h2 class="stat-number" id="stat-online">{{ $stats['online'] }}</h2>
                            </div>
                            <i class="bi bi-check-circle-fill fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card modern-stat-card red text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stat-subtitle mb-2">Offline</h6>
                                <h2 class="stat-number" id="stat-offline">{{ $stats['offline'] }}</h2>
                            </div>
                            <i class="bi bi-x-circle-fill fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card modern-stat-card orange text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="stat-subtitle mb-2">Manutenção</h6>
                                <h2 class="stat-number" id="stat-maintenance">{{ $stats['maintenance'] }}</h2>
                            </div>
                            <i class="bi bi-wrench fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['total'] > 0)
        <!-- Controles de Visualização Modernos -->
        <div class="view-controls animate-fade-in" style="animation-delay: 0.3s;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group modern-radio-group" role="group">
                        <input type="radio" class="btn-check" name="viewMode" id="cardView" autocomplete="off" checked>
                        <label class="btn modern-btn modern-btn-outline" for="cardView">
                            <i class="bi bi-grid-3x3-gap"></i> Cards
                        </label>

                        <input type="radio" class="btn-check" name="viewMode" id="listView" autocomplete="off">
                        <label class="btn modern-btn modern-btn-outline" for="listView">
                            <i class="bi bi-list"></i> Lista
                        </label>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted" id="resultsCount">Exibindo {{ $stats['total'] }} dispositivo(s)</small>
                </div>
            </div>
        </div>

        <!-- Container dos Dispositivos Moderno -->
        <div class="devices-container animate-fade-in" style="animation-delay: 0.4s;" id="devicesContainer">
            <!-- Visualização em Cards -->
            <div class="row" id="cardContainer">
                @foreach($devices as $device)
                @include('devices.partials.device-card', ['device' => $device])
                @endforeach
            </div>

            <!-- Visualização em Lista -->
            <div id="listContainer" style="display: none;">
                <div class="table-responsive">
                    <table class="table modern-table">
                        <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>Status</th>
                                <th>Tipo</th>
                                <th>Ambiente</th>
                                <th>Potência Atual</th>
                                <th>Consumo Hoje</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="listTableBody">
                            @foreach($devices as $device)
                            @include('devices.partials.device-row', ['device' => $device])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        @if(method_exists($devices, 'links'))
        <div class="d-flex justify-content-center mt-4 animate-fade-in" style="animation-delay: 0.5s;">
            {{ $devices->links() }}
        </div>
        @endif
        @else
        <!-- Estado Vazio Moderno -->
        <div class="empty-state animate-fade-in" style="animation-delay: 0.3s;">
            <i class="bi bi-cpu display-1"></i>
            <h4>Nenhum dispositivo cadastrado</h4>
            <p>
                Adicione dispositivos para começar a monitorar o consumo de energia em tempo real.
            </p>
            <a href="{{ route('devices.create') }}" class="btn modern-btn modern-btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Adicionar Primeiro Dispositivo
            </a>
        </div>
        @endif
    </div>

    <!-- Modal de Confirmação Moderno -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o dispositivo <strong id="deviceToDelete"></strong>?</p>
                    <p class="text-muted small">Esta ação não pode ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn modern-btn modern-btn-outline" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn modern-btn" style="background: var(--primary-red); color: white;" id="confirmDelete">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>


<!-- JavaScript Otimizado -->
<!-- JavaScript Otimizado -->
<script>
class DeviceManager {
    constructor() {
        this.elements = this.cacheElements();
        this.state = {
            currentFilter: 'all',
            searchTerm: '',
            environmentFilter: '',
            typeFilter: '',
            viewMode: 'card'
        };
        this.charts = new Map();
        this.deviceData = this.cacheDeviceData();
        this.chartJsLoaded = false;
        this.init();
    }

    cacheElements() {
        return {
            searchInput: document.getElementById('searchDevices'),
            clearSearch: document.getElementById('clearSearch'),
            environmentSelect: document.getElementById('filterEnvironment'),
            typeSelect: document.getElementById('filterType'),
            resetButton: document.getElementById('resetFilters'),
            filterButtons: document.querySelectorAll('.filter-btn'),
            cardContainer: document.getElementById('cardContainer'),
            listContainer: document.getElementById('listContainer'),
            viewModeInputs: document.querySelectorAll('input[name="viewMode"]'),
            resultsCount: document.getElementById('resultsCount'),
            loadingIndicator: document.getElementById('loadingIndicator'),
            deleteModal: new bootstrap.Modal(document.getElementById('deleteModal'))
        };
    }

    cacheDeviceData() {
        const devices = [];
        document.querySelectorAll('.device-carda, .device-row').forEach(el => {
            devices.push({
                element: el,
                name: (el.dataset.name || '').toLowerCase(),
                status: el.dataset.status || '',
                environment: el.dataset.environment || '',
                type: el.dataset.type || '',
                id: el.dataset.deviceId || ''
            });
        });
        return devices;
    }

    init() {
        this.setupEventListeners();
        this.waitForChartJs().then(() => {
            this.setupCharts();
        });
        this.updateStats();
    }

    // Aguardar Chart.js carregar
    async waitForChartJs() {
        return new Promise((resolve) => {
            const checkChartJs = () => {
                if (typeof Chart !== 'undefined') {
                    this.chartJsLoaded = true;
                    console.log('Chart.js carregado com sucesso');
                    resolve();
                } else {
                    setTimeout(checkChartJs, 100);
                }
            };
            checkChartJs();
        });
    }

    setupEventListeners() {
        // Pesquisa com debounce
        this.elements.searchInput?.addEventListener('input',
            this.debounce((e) => this.handleSearch(e.target.value), 300)
        );

        this.elements.clearSearch?.addEventListener('click', () => {
            this.elements.searchInput.value = '';
            this.handleSearch('');
        });

        // Filtros
        this.elements.environmentSelect?.addEventListener('change', (e) => {
            this.state.environmentFilter = e.target.value;
            this.applyFilters();
        });

        this.elements.typeSelect?.addEventListener('change', (e) => {
            this.state.typeFilter = e.target.value;
            this.applyFilters();
        });

        // Botões de status
        this.elements.filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                this.handleStatusFilter(btn.dataset.filter);
            });
        });

        // Reset
        this.elements.resetButton?.addEventListener('click', () => this.resetFilters());

        // Modo de visualização
        this.elements.viewModeInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.switchViewMode(e.target.id === 'cardView' ? 'card' : 'list');
                }
            });
        });

        // Delete modal
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-device-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-device-btn');
                this.showDeleteModal(btn.dataset.deviceId, btn.dataset.deviceName);
            }
        });
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    handleSearch(term) {
        this.state.searchTerm = term.toLowerCase();
        this.elements.clearSearch.style.display = term ? 'block' : 'none';
        this.applyFilters();
    }

    handleStatusFilter(status) {
        this.state.currentFilter = status;

        // Atualizar botões
        this.elements.filterButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.filter === status);
        });

        this.applyFilters();
    }

    applyFilters() {
        this.showLoading(true);

        requestAnimationFrame(() => {
            let visibleCount = 0;

            this.deviceData.forEach(device => {
                const matchesSearch = !this.state.searchTerm ||
                    device.name.includes(this.state.searchTerm);

                const matchesStatus = this.state.currentFilter === 'all' ||
                    device.status === this.state.currentFilter;

                const matchesEnvironment = !this.state.environmentFilter ||
                    device.environment === this.state.environmentFilter;

                const matchesType = !this.state.typeFilter ||
                    device.type === this.state.typeFilter;

                const isVisible = matchesSearch && matchesStatus &&
                    matchesEnvironment && matchesType;

                device.element.style.display = isVisible ? '' : 'none';

                if (isVisible) {
                    visibleCount++;
                    // Carregar gráfico se necessário
                    if (this.chartJsLoaded) {
                        this.loadChartIfNeeded(device.element);
                    }
                }
            });

            this.updateResultsCount(visibleCount);
            this.showLoading(false);
        });
    }

    resetFilters() {
        this.state = {
            currentFilter: 'all',
            searchTerm: '',
            environmentFilter: '',
            typeFilter: '',
            viewMode: this.state.viewMode
        };

        this.elements.searchInput.value = '';
        this.elements.clearSearch.style.display = 'none';
        this.elements.environmentSelect.value = '';
        this.elements.typeSelect.value = '';

        this.handleStatusFilter('all');
    }

    switchViewMode(mode) {
        this.state.viewMode = mode;

        if (mode === 'card') {
            this.elements.cardContainer.style.display = '';
            this.elements.listContainer.style.display = 'none';
        } else {
            this.elements.cardContainer.style.display = 'none';
            this.elements.listContainer.style.display = '';
        }

        // Re-aplicar filtros para o novo modo
        this.applyFilters();
    }

    setupCharts() {
        if (!this.chartJsLoaded) {
            console.warn('Chart.js não está carregado ainda');
            return;
        }

        // Carregar gráficos visíveis imediatamente
        this.deviceData.forEach(device => {
            if (device.element.classList.contains('device-carda') && 
                device.element.style.display !== 'none') {
                this.loadChartIfNeeded(device.element);
            }
        });

        // Lazy loading com Intersection Observer para o resto
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.loadChartIfNeeded(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '50px'
            });

            this.deviceData.forEach(device => {
                if (device.element.classList.contains('device-carda')) {
                    observer.observe(device.element);
                }
            });
        }
    }

    loadChartIfNeeded(element) {
        const canvas = element.querySelector('.chart-canvas');
        if (!canvas || this.charts.has(canvas.id)) {
            return;
        }

        const deviceId = canvas.dataset.deviceId;
        if (!deviceId) {
            console.warn('Device ID não encontrado no canvas');
            return;
        }

        try {
            const chartData = JSON.parse(canvas.dataset.chartData || '[]');
            const status = canvas.dataset.status;
            const unit = canvas.dataset.unit;

            if (chartData.length === 0) {
                console.log(`Sem dados para device ${deviceId}`);
                return;
            }

            this.createChart(canvas, chartData, status, unit, deviceId);
            
        } catch (error) {
            console.error(`Erro ao processar dados do gráfico para device ${deviceId}:`, error);
            this.showChartError(canvas);
        }
    }

    createChart(canvas, data, status, unit, deviceId) {
        if (!this.chartJsLoaded) {
            console.warn('Chart.js não carregado');
            return;
        }

        const colors = {
            online: {
                border: '#28a745',
                background: 'rgba(40, 167, 69, 0.1)'
            },
            offline: {
                border: '#dc3545',
                background: 'rgba(220, 53, 69, 0.1)'
            },
            maintenance: {
                border: '#ffc107',
                background: 'rgba(255, 193, 7, 0.1)'
            }
        };

        const color = colors[status] || colors.offline;

        // Processar dados
        const labels = data.map(item => {
            if (!item.date) return 'N/A';
            
            const dateParts = item.date.split('-');
            if (dateParts.length !== 3) return 'N/A';
            
            const dt = new Date(
                parseInt(dateParts[0]),
                parseInt(dateParts[1]) - 1, // Mês é 0-based
                parseInt(dateParts[2]),
                12, 0, 0 // Meio-dia para evitar problemas de fuso
            );
            
            return dt.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit'
            });
        });

        const values = data.map(item => {
            const value = parseFloat(item.value);
            return isNaN(value) ? 0 : value;
        });

        try {
            const ctx = canvas.getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderColor: color.border,
                        backgroundColor: color.background,
                        pointBackgroundColor: color.border,
                        pointBorderColor: color.border,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            displayColors: false,
                            callbacks: {
                                label: (context) => {
                                    const value = context.parsed.y;
                                    const precision = unit === 'kWh' ? 2 : 1;
                                    return `${value.toFixed(precision)} ${unit}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 0,
                                font: {
                                    size: 9
                                },
                                color: '#6c757d'
                            }
                        },
                        y: {
                            display: false,
                            grid: {
                                display: false
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            this.charts.set(canvas.id, chart);
            console.log(`Gráfico criado com sucesso para device ${deviceId}`);

        } catch (error) {
            console.error(`Erro ao criar gráfico para device ${deviceId}:`, error);
            this.showChartError(canvas);
        }
    }

    showChartError(canvas) {
        const container = canvas.parentElement;
        if (container) {
            container.innerHTML = `
                <div class="text-center text-muted small py-3">
                    <i class="bi bi-exclamation-triangle opacity-50"></i>
                    <div>Erro ao carregar gráfico</div>
                </div>
            `;
        }
    }

    updateStats() {
        // Atualizar contadores em tempo real se necessário
    }

    updateResultsCount(count) {
        if (this.elements.resultsCount) {
            this.elements.resultsCount.textContent =
                `Exibindo ${count} dispositivo${count !== 1 ? 's' : ''}`;
        }
    }

    showLoading(show) {
        if (this.elements.loadingIndicator) {
            this.elements.loadingIndicator.style.display = show ? 'block' : 'none';
        }
    }

    showDeleteModal(deviceId, deviceName) {
        const deviceToDeleteElement = document.getElementById('deviceToDelete');
        if (deviceToDeleteElement) {
            deviceToDeleteElement.textContent = deviceName;
        }

        const confirmBtn = document.getElementById('confirmDelete');
        if (confirmBtn) {
            confirmBtn.onclick = () => this.deleteDevice(deviceId);
        }

        this.elements.deleteModal.show();
    }

    deleteDevice(deviceId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/devices/${deviceId}`;
        form.style.display = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}



// Inicializar quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando DeviceManager...');
    
    // Aguardar um pouco para garantir que tudo esteja carregado
    setTimeout(() => {
        try {
            window.deviceManager = new DeviceManager();
            console.log('DeviceManager inicializado com sucesso');
        } catch (error) {
            console.error('Erro ao inicializar DeviceManager:', error);
        }
    }, 500);
});

// Debug: verificar se Chart.js carregou
window.addEventListener('load', function() {
    if (typeof Chart !== 'undefined') {
        console.log('Chart.js carregado:', Chart.version);
    } else {
        console.error('Chart.js NÃO foi carregado!');
    }
});
</script>
