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

    /* Container principal moderno */
    .main-container {
        margin-left: 17vw;
        max-width: 78vw;
        min-height: 100vh;
        padding: 2rem;
    }

    /* Cards modernos */
    .modern-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        position: relative;
    }

    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    /* Header moderno */
    .modern-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .modern-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
        position: relative;
    }

    .modern-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-dark));
        border-radius: 2px;
    }

    /* Botão moderno */
    .modern-btn {
        background: linear-gradient(135deg, var(--primary-green) 0%, #229954 100%);
        border: none;
        border-radius: 12px;
        color: white;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .modern-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
        color: white;
    }

    /* Environment card moderna */
    .environment-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: visible !important;
        margin-bottom: 2rem;
        position: relative;
    
        
    }

    .environment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(39, 174, 96, 0.03) 0%, transparent 50%);
        pointer-events: none;
    }

    .environment-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    /* Environment header */
    .env-header {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #34495e 100%);
        color: white;
        padding: 1.5rem;
        position: relative;
       overflow: visible !important;
    }

    .env-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .env-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Badges modernos */
    .modern-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 500;
        backdrop-filter: blur(10px);
    }

    .modern-badge.success {
        background: rgba(39, 174, 96, 0.9);
        border-color: rgba(39, 174, 96, 1);
    }

    /* Seletores customizados */
    .modern-select {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 500;
        transition: var(--transition);
        min-width: 180px;
    }

    .modern-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    /* Info cards */
    .info-card {
        background: rgba(248, 249, 250, 0.8);
        border: 1px solid rgba(233, 236, 239, 0.5);
        border-radius: 12px;
        backdrop-filter: blur(5px);
        transition: var(--transition);
    }

    .info-stat {
        text-align: center;
        padding: 1rem;
    }

    .info-stat-label {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }

    .info-stat-value {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--primary-dark);
    }

    /* Chart container */
    .chart-container {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin: 1rem 0;
        position: relative;
    }

    .chart-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f8f9fa;
    }

    .chart-icon {
        font-size: 1.5rem;
        margin-right: 0.75rem;
        transition: var(--transition);
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--primary-dark);
        margin: 0;
    }

    /* Devices section */
    .devices-section {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #34495e 100%);
        color: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        max-height: 20vh;
    }

    .devices-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .devices-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .devices-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }

    .device-item {
        text-align: center;
        padding: 1rem;
        transition: var(--transition);
        border-radius: 8px;
        cursor: pointer;
    }

    .device-item:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.1);
    }

    .device-item i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #dee2e6;
    }

    .empty-state h4 {
        color: var(--text-muted);
        margin-bottom: 1rem;
    }

    .empty-state p {
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }
    .modern-dropdown {
        position: static !important; 
    }
    /* Dropdown moderno */
    .modern-dropdown .dropdown-toggle {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        color: var(--text-muted);
        font-weight: 500;
        transition: var(--transition);
    }

    .modern-dropdown .dropdown-toggle:hover,
    .modern-dropdown .dropdown-toggle:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 2px rgba(39, 174, 96, 0.1);
    }

    .modern-dropdown .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: var(--shadow-medium);
        padding: 0.5rem 0;
        position: absolute !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: 1060;
    }

    .modern-dropdown .dropdown-item {
        padding: 0.75rem 1.25rem;
        transition: var(--transition);
        border-radius: 0;
    }

    .modern-dropdown .dropdown-item:hover {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
    }

    .modern-dropdown .dropdown-item.text-danger:hover {
        background: rgba(231, 76, 60, 0.1);
        color: var(--primary-red);
    }

    /* Alerts modernos */
    .modern-alert {
        border: none;
        border-radius: 12px;
        border-left: 4px solid;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-light);
        padding: 1rem 1.25rem;
    }

    .modern-alert.alert-success {
        border-left-color: var(--primary-green);
        background: linear-gradient(90deg, rgba(39, 174, 96, 0.1) 0%, rgba(39, 174, 96, 0.05) 100%);
    }

    /* Loading state */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        border-radius: var(--border-radius);
        z-index: 10;
    }

    .loading-spinner {
        width: 2rem;
        height: 2rem;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--primary-green);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    .dropdown-menu{
        z-index: 1060;
    }
    .dropdown {
  position: relative; 
}

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
        .main-container {
            margin-left: 0 !important;
            max-width: 100% !important;
            padding: 1rem;
        }
        
        .modern-title {
            font-size: 2rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .modern-select {
            min-width: 120px;
        }
        
        .env-header {
            padding: 1rem;
        }
        
        .chart-container {
            padding: 1rem;
        }
    }
</style>

<body>
    <div class="main-container">
        {{-- Header moderno --}}
        <div class="modern-header animate-fade-in">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="modern-title">Ambientes</h1>
                <a href="{{ route('environments.create') }}" class="modern-btn">
                    <i class="bi bi-plus"></i> Novo Ambiente
                </a>
            </div>
        </div>

        {{-- Alert de sucesso --}}
        @if(session('success'))
            <div class="modern-alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Lista de ambientes --}}
        @if($environments->count() > 0)
            @foreach($environments as $index => $environment)
                <div class="environment-card animate-fade-in" 
                     style="animation-delay: {{ 0.1 + ($index * 0.1) }}s;" 
                     data-environment-id="{{ $environment->id }}">
                    
                    {{-- Cabeçalho do ambiente --}}
                    <div class="env-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h4 class="env-title">{{ $environment->name }}</h4>
                                @if($environment->is_default)
                                    <span class="modern-badge ms-2">Padrão</span>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2">
                                <select class="form-select modern-select type-select" data-env-id="{{ $environment->id }}">
                                    <option value="energy" selected>⚡ Energia (kWh)</option>
                                    <option value="temperature">🌡️ Temperatura (°C)</option>
                                    <option value="humidity">💧 Umidade (%)</option>
                                </select>

                                <select class="form-select modern-select">
                                    <option selected>Últimos 7 dias</option>
                                </select>

                                <div class="dropdown modern-dropdown">
                                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('environments.edit', $environment) }}">
                                            <i class="bi bi-pencil me-2"></i>Editar
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('environments.destroy', $environment) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este ambiente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>Excluir
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Conteúdo do card --}}
                    <div class="p-4">
                        {{-- Informações básicas do ambiente --}}
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="info-card">
                                    <div class="row text-center">
                                        <div class="col info-stat">
                                            <div class="info-stat-label">Tipo</div>
                                            <div class="info-stat-value">{{ ucfirst($environment->type) }}</div>
                                        </div>
                                        @if($environment->size_sqm)
                                        <div class="col info-stat">
                                            <div class="info-stat-label">Área</div>
                                            <div class="info-stat-value">{{ $environment->size_sqm }}m²</div>
                                        </div>
                                        @endif
                                        @if($environment->occupancy)
                                        <div class="col info-stat">
                                            <div class="info-stat-label">Ocupação</div>
                                            <div class="info-stat-value">{{ $environment->occupancy }} pessoas</div>
                                        </div>
                                        @endif
                                        <div class="col info-stat">
                                            <div class="info-stat-label">Dispositivos</div>
                                            <div class="info-stat-value">{{ $environment->devices->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Gráfico --}}
                        <div class="chart-container">
                            <div class="chart-header">
                                <i class="bi bi-lightning-charge-fill text-success chart-icon" id="icon-env-{{ $environment->id }}"></i>
                                <h6 class="chart-title" id="title-env-{{ $environment->id }}">Consumo de Energia</h6>
                            </div>

                            <div style="height: 40vh; position: relative;">
                                <canvas id="chart-env-{{ $environment->id }}"></canvas>
                                
                                {{-- Loading indicator --}}
                                <div id="loading-env-{{ $environment->id }}" class="loading-overlay d-none">
                                    <div class="loading-spinner"></div>
                                    <div class="text-muted">Carregando dados...</div>
                                </div>
                            </div>
                        </div>

                        {{-- Dispositivos conectados --}}
                        <div class="devices-section">
                            <div class="devices-header">
                                <h6 class="devices-title">Dispositivos Conectados</h6>
                                <span class="modern-badge success" style="margin-left:0.5vw;">{{ $environment->devices->count() }}</span>
                            </div>
                            
                            @if($environment->devices->count() > 0)
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    @foreach($environment->devices as $device)
                                        <div class="device-item" data-bs-toggle="tooltip" data-bs-title="{{ $device->deviceType->name ?? 'Dispositivo' }}">
                                            @php
                                                $deviceIcon = 'bi-cpu';
                                                $typeName = strtolower($device->deviceType->name ?? '');
                                                if (str_contains($typeName, 'temperature')) {
                                                    $deviceIcon = 'bi-thermometer';
                                                } elseif (str_contains($typeName, 'humidity')) {
                                                    $deviceIcon = 'bi-droplet';
                                                } elseif (str_contains($typeName, 'energy') || str_contains($typeName, 'power')) {
                                                    $deviceIcon = 'bi-lightning-charge';
                                                }
                                            @endphp
                                            <i class="bi {{ $deviceIcon }}"></i>
                                            <div class="small">{{ Str::limit($device->name, 15) }}</div>
                                        </div>
                                    @endforeach
                                    
                                    <a href="{{ route('devices.create', ['environment_id' => $environment->id]) }}" 
                                       class="ms-auto btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                       style="width: 32px; height: 32px;" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-title="Adicionar dispositivo">
                                        <i class="bi bi-plus-lg"></i>
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-exclamation-circle fs-4 text-warning mb-2"></i>
                                    <p class="mb-2">Nenhum dispositivo conectado</p>
                                    <a href="{{ route('devices.create', ['environment_id' => $environment->id]) }}" class="btn btn-outline-light btn-sm">
                                        <i class="bi bi-plus me-1"></i>Adicionar Primeiro Dispositivo
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Empty state moderno --}}
            <div class="modern-card animate-fade-in">
                <div class="empty-state">
                    <i class="bi bi-house"></i>
                    <h4>Nenhum ambiente cadastrado</h4>
                    <p>Comece criando seu primeiro ambiente para monitorar o consumo de energia.</p>
                    <a href="{{ route('environments.create') }}" class="modern-btn">
                        <i class="bi bi-plus me-2"></i>Criar Primeiro Ambiente
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dados diários agregados por ambiente, separado por tipo
        const environmentDailyConsumption = @json($environmentDailyConsumption);

        // Guarda instâncias dos charts
        const charts = {};

        // Configurações para cada tipo de dado
        const typeConfigs = {
            energy: {
                label: 'Consumo de Energia',
                unit: 'kWh',
                icon: 'bi-lightning-charge-fill',
                color: '#27ae60'
            },
            temperature: {
                label: 'Temperatura',
                unit: '°C',
                icon: 'bi-thermometer-half',
                color: '#e67e22'
            },
            humidity: {
                label: 'Umidade',
                unit: '%',
                icon: 'bi-droplet-fill',
                color: '#3498db'
            }
        };

        function createChart(ctx, data, type) {
            const config = typeConfigs[type];
            
            // Criar gradiente
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, config.color + '40');
            gradient.addColorStop(1, config.color + '10');
            
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => {
                        const dt = new Date(d.date + 'T12:00:00');
                        return dt.toLocaleDateString('pt-BR', { 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    }),
                    datasets: [{
                        label: config.label,
                        data: data.map(d => parseFloat(d.value)),
                        backgroundColor: gradient,
                        borderColor: config.color,
                        borderWidth: 2,
                        borderRadius: 8,
                        barThickness: 30,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false,
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d',
                                callback: function(value) {
                                    return value.toFixed(2) + ' ' + config.unit;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                },
                                color: '#6c757d',
                                maxRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: { 
                            display: false 
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return config.label + ': ' + context.parsed.y.toFixed(2) + ' ' + config.unit;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 750,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        function updateChartAppearance(envId, type) {
            const config = typeConfigs[type];
            const icon = document.getElementById(`icon-env-${envId}`);
            const title = document.getElementById(`title-env-${envId}`);
            
            if (icon) {
                icon.className = `bi ${config.icon} chart-icon`;
                icon.style.color = config.color;
            }
            
            if (title) {
                title.textContent = config.label;
            }
        }

        // Inicializa tooltips do Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Inicializa gráficos para todos os ambientes com dados padrão 'energy'
        Object.entries(environmentDailyConsumption).forEach(([envId, typesData]) => {
            const ctx = document.getElementById(`chart-env-${envId}`)?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados para energia
            const energyData = typesData.energy || [];
            charts[envId] = createChart(ctx, energyData, 'energy');
            updateChartAppearance(envId, 'energy');
        });

        // Atualiza gráfico ao mudar o select do tipo de dado
        document.querySelectorAll('.type-select').forEach(select => {
            select.addEventListener('change', function () {
                const envId = this.getAttribute('data-env-id');
                if (!envId) return;

                const dataByType = environmentDailyConsumption[envId];
                if (!dataByType) return;

                const type = this.value;
                const chartData = dataByType[type] || [];
                const config = typeConfigs[type];

                // Mostra loading
                const loadingEl = document.getElementById(`loading-env-${envId}`);
                const chartEl = document.getElementById(`chart-env-${envId}`);
                
                if (loadingEl && chartEl) {
                    loadingEl.classList.remove('d-none');
                    chartEl.style.opacity = '0.3';
                }

                                    // Simula delay de carregamento para UX
                setTimeout(() => {
                    const chart = charts[envId];
                    if (!chart) return;

                    // Cria novo gradiente para o tipo selecionado
                    const gradient = chart.canvas.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, config.color + '40');
                    gradient.addColorStop(1, config.color + '10');

                    // Atualiza dados e configurações do gráfico
                    chart.data.datasets[0].label = config.label;
                    chart.data.datasets[0].data = chartData.map(d => parseFloat(d.value));
                    chart.data.datasets[0].backgroundColor = gradient;
                    chart.data.datasets[0].borderColor = config.color;
                    
                    chart.options.scales.y.ticks.callback = function(value) {
                        return value.toFixed(2) + ' ' + config.unit;
                    };
                    
                    chart.options.plugins.tooltip.callbacks.label = function(context) {
                        return config.label + ': ' + context.parsed.y.toFixed(2) + ' ' + config.unit;
                    };

                    // Atualiza aparência
                    updateChartAppearance(envId, type);

                    // Atualiza o gráfico
                    chart.update('active');

                    // Remove loading
                    if (loadingEl && chartEl) {
                        loadingEl.classList.add('d-none');
                        chartEl.style.opacity = '1';
                    }
                }, 500);
            });
        });

        // Função para atualizar dados periodicamente (opcional)
        function refreshChartData() {
            // Implementar refresh automático se necessário
            console.log('Refreshing chart data...');
        }

        // Refresh automático a cada 5 minutos (opcional)
        setInterval(refreshChartData, 600000);
    });
    
    </script>
</body>