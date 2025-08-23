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

    /* Cards de estatísticas com gradiente */
    .stat-card {
        background: linear-gradient(135deg, var(--card-color) 0%, var(--card-color-dark) 100%);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    .stat-card.dark {
        --card-color: #2c3e50;
        --card-color-dark: #34495e;
    }

    .stat-card.green {
        --card-color: #27ae60;
        --card-color-dark: #229954;
    }

    .stat-card.red {
        --card-color: #e74c3c;
        --card-color-dark: #c0392b;
    }

    /* Números das estatísticas */
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

    /* Cards de dispositivos */
    .device-card {
        border: none;
        border-radius: var(--border-radius);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--device-color) 0%, var(--device-color-dark) 100%);
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
        transform: translateY(-3px) scale(1.02);
        box-shadow: var(--shadow-medium);
    }

    .device-card.online {
        --device-color: #27ae60;
        --device-color-dark: #229954;
    }

    .device-card.offline {
        --device-color: #2c3e50;
        --device-color-dark: #34495e;
    }

    .device-card.maintenance {
        --device-color: #e67e22;
        --device-color-dark: #d35400;
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

    /* Seletor customizado */
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

    /* Lista de alertas moderna */
    .alert-list {
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .alert-item {
        border: none;
        border-left: 4px solid transparent;
        transition: var(--transition);
        position: relative;
        padding: 16px 20px;
    }

    .alert-item:hover {
        transform: translateX(4px);
    }

    .alert-item.alert-danger {
        border-left-color: #e74c3c;
        background: linear-gradient(90deg, rgba(231, 76, 60, 0.1) 0%, rgba(231, 76, 60, 0.05) 100%);
    }

    .alert-item.alert-warning {
        border-left-color: #f39c12;
        background: linear-gradient(90deg, rgba(243, 156, 18, 0.1) 0%, rgba(243, 156, 18, 0.05) 100%);
    }

    .alert-item.alert-info {
        border-left-color: #3498db;
        background: linear-gradient(90deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%);
    }

    /* Títulos modernos */
    .modern-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .modern-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-dark));
        border-radius: 2px;
    }

    /* Gráfico container */
    .chart-container {
        position: relative;
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
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

    /* Responsividade aprimorada */
    @media (max-width: 768px) {
        .stat-number {
            font-size: 1.8rem;
        }
        
        .modern-card {
            margin-bottom: 1rem;
        }
    }

    /* Loading skeleton para melhor UX */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }
</style>

<body>
    <div class="container-fluid py-4" >
        <div class="row">
            <main class="col-md-10 ms-sm-auto px-md-4">
                {{-- Cartões de Estatísticas Modernos --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-6 col-xl-4 animate-fade-in" style="animation-delay: 0.1s;">
                        <div class="card text-white stat-card dark">
                            <div class="card-body p-4">
                                <h6 class="stat-subtitle mb-2">Consumo Total (7 dias)</h6>
                                <h2 class="stat-number">{{ number_format($totalConsumptionValue, 2, ',', '.') }} kWh</h2>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-bolt me-2 opacity-75"></i>
                                    <small class="opacity-75">Últimos 7 dias</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4 animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="card text-white stat-card green">
                            <div class="card-body p-4">
                                <h6 class="stat-subtitle mb-2">Dispositivos Ativos</h6>
                                <h2 class="stat-number">{{ $devices->count() }}</h2>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-microchip me-2 opacity-75"></i>
                                    <small class="opacity-75">Online agora</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4 animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="card text-white stat-card red">
                            <div class="card-body p-4">
                                <h6 class="stat-subtitle mb-2">Alertas Ativos</h6>
                                <h2 class="stat-number">{{ $alerts->count() }}</h2>
                                <div class="d-flex align-items-center mt-3">
                                    <i class="fas fa-exclamation-triangle me-2 opacity-75"></i>
                                    <small class="opacity-75">Requer atenção</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico de Consumo Moderno --}}
                <div class="modern-card mb-5 animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body p-4">
                        <h5 class="modern-title">Consumo Diário</h5>
                        <div class="row align-items-center mb-4">
                            <div class="col-md-6">
                                <p class="text-muted mb-0">Análise dos últimos 7 dias</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <label for="dataTypeSelector" class="form-label me-3">Tipo de dado:</label>
                                <select id="dataTypeSelector" class="form-select modern-select d-inline-block" style="width: auto;">
                                    <option value="energy" selected>⚡ Energia (kWh)</option>
                                    <option value="temperature">🌡️ Temperatura (°C)</option>
                                    <option value="humidity">💧 Umidade (%)</option>
                                </select>
                            </div>
                        </div>

                        <div class="chart-container">
                            <canvas id="dailyConsumptionChart" style="max-height: 350px;"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Dispositivos Modernos --}}
                <div class="mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <h5 class="modern-title">Dispositivos Conectados</h5>
                    @if($devices->isEmpty())
                    <div class="modern-card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-plug fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Nenhum dispositivo conectado no momento</p>
                        </div>
                    </div>
                    @else
                    <div class="row g-4">
                        @foreach($devices as $index => $device)
                        @php
                        $deviceId = $device->id;
                        $dailyData = $dailyConsumption[$deviceId]['energy']
                            ?? $dailyConsumption[$deviceId]['temperature']
                            ?? $dailyConsumption[$deviceId]['humidity']
                            ?? [];

                        $todayConsumption = 0;
                        $isEnergyDevice = true;
                        $unit = 'kWh';
                        $icon = 'fa-plug';
                        $deviceTypeName = strtolower($device->deviceType->name ?? '');

                        if (str_contains($deviceTypeName, 'temperature')) {
                            $isEnergyDevice = false;
                            $unit = '°C';
                            $icon = 'fa-thermometer-half';
                        } elseif (str_contains($deviceTypeName, 'humidity')) {
                            $isEnergyDevice = false;
                            $unit = '%';
                            $icon = 'fa-tint';
                        }

                        if (!empty($dailyData)) {
                            $lastDay = end($dailyData);
                            $todayConsumption = $lastDay['value'] ?? 0;
                        }

                        $statusClass = match($device->status) {
                            'online' => 'online',
                            'offline' => 'offline',
                            default => 'maintenance'
                        };
                        @endphp
                        <div class="col-md-4 col-sm-6 animate-fade-in" style="animation-delay: {{ 0.6 + ($index * 0.1) }}s;">
                            <div class="card text-white device-card {{ $statusClass }} h-100">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="card-title mb-1">{{ $device->name }}</h5>
                                            <p class="mb-0 opacity-75">{{ ucfirst($device->status) }}</p>
                                        </div>
                                        <i class="fas {{ $icon }} fa-2x opacity-75"></i>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <span class="modern-badge">
                                            {{ $device->deviceType->name ?? 'Tipo desconhecido' }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <small class="opacity-75">Leitura atual</small>
                                            <div class="fw-bold fs-5">
                                                @if($todayConsumption > 0 || !$isEnergyDevice)
                                                {{ number_format($todayConsumption, 2, ',', '.') }} {{ $unit }}
                                                @else
                                                <span class="opacity-50">N/A</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 12px; height: 12px; background: {{ $device->status === 'online' ? '#00ff00' : '#ff6b6b' }}; box-shadow: 0 0 8px rgba(255,255,255,0.5);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Alertas Modernos --}}
                <div class="modern-card mb-4 animate-fade-in" style="animation-delay: 0.7s;">
                    <div class="card-body p-4">
                        <h5 class="modern-title">Centro de Alertas</h5>
                        @if($alerts->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <p class="text-muted mb-0">Nenhum alerta ativo. Sistema funcionando normalmente!</p>
                        </div>
                        @else
                        <div class="alert-list">
                            @foreach($alerts as $alert)
                            @php
                            $alertClass = match($alert->severity) {
                                'high' => 'alert-danger',
                                'low' => 'alert-info',
                                default => 'alert-warning'
                            };
                            
                            $alertIcon = match($alert->severity) {
                                'high' => 'fa-exclamation-circle text-danger',
                                'low' => 'fa-info-circle text-info',
                                default => 'fa-exclamation-triangle text-warning'
                            };
                            @endphp
                            <div class="alert-item {{ $alertClass }} d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fas {{ $alertIcon }} me-3 fa-lg"></i>
                                    <div>
                                        <div class="fw-medium">{{ $alert->message }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $alert->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                <div class="modern-badge" style="background: rgba(108, 117, 125, 0.1); color: #495057; border-color: rgba(108, 117, 125, 0.2);">
                                    <i class="fas fa-microchip me-1"></i>
                                    {{ $alert->device->name ?? 'Dispositivo desconhecido' }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

<!-- FontAwesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dailyConsumptionData = @json($dailyConsumption ?? []);
        const deviceTypes = @json($devices->pluck('deviceType.name', 'id') ?? []);

        // Função para agregar valores por tipo e data
        function aggregateDataByType(type) {
            const aggregated = {};

            Object.entries(dailyConsumptionData).forEach(([deviceId, deviceData]) => {
                if (!deviceData[type]) return;

                const typeName = (deviceTypes[deviceId] || '').toLowerCase();

                if (type === 'energy' && (typeName.includes('temperature') || typeName.includes('humidity'))) {
                    return;
                }

                if ((type === 'temperature' && !typeName.includes('temperature')) ||
                    (type === 'humidity' && !typeName.includes('humidity'))) {
                    return;
                }

                deviceData[type].forEach(day => {
                    if (!aggregated[day.date]) aggregated[day.date] = 0;
                    aggregated[day.date] += day.value;
                });
            });

            return aggregated;
        }

        function formatLabels(dates) {
            return dates.map(dateStr => {
                const [year, month, day] = dateStr.split('-');
                return `${day}/${month}`;
            });
        }

        // Configurações do gráfico moderno
        const ctx = document.getElementById('dailyConsumptionChart').getContext('2d');
        let chart = null;

        function createChart(labels, values, type) {
            const colors = {
                energy: {
                    border: '#27ae60',
                    background: 'rgba(39, 174, 96, 0.1)',
                    gradient: ['#27ae60', '#2ecc71']
                },
                temperature: {
                    border: '#f39c12',
                    background: 'rgba(243, 156, 18, 0.1)',
                    gradient: ['#f39c12', '#f1c40f']
                },
                humidity: {
                    border: '#3498db',
                    background: 'rgba(52, 152, 219, 0.1)',
                    gradient: ['#3498db', '#5dade2']
                },
            };

            if (chart) {
                chart.destroy();
            }

            // Criar gradiente
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, colors[type].gradient[0] + '40');
            gradient.addColorStop(1, colors[type].gradient[1] + '10');

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: {
                            energy: 'Consumo de Energia (kWh)',
                            temperature: 'Temperatura (°C)',
                            humidity: 'Umidade (%)'
                        }[type],
                        data: values,
                        borderColor: colors[type].border,
                        backgroundColor: gradient,
                        pointBackgroundColor: colors[type].border,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: '500'
                                },
                                color: '#2c3e50',
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: colors[type].border,
                            borderWidth: 1,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: context => {
                                    let suffix = '';
                                    if (type === 'energy') suffix = ' kWh';
                                    else if (type === 'temperature') suffix = ' °C';
                                    else if (type === 'humidity') suffix = ' %';
                                    return `${context.parsed.y.toFixed(2)}${suffix}`;
                                }
                            }
                        }
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
                                color: '#6c757d'
                            },
                            title: {
                                display: true,
                                text: {
                                    energy: 'Consumo (kWh)',
                                    temperature: 'Temperatura (°C)',
                                    humidity: 'Umidade (%)'
                                }[type],
                                font: {
                                    size: 14,
                                    weight: '600'
                                },
                                color: '#2c3e50'
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
                                color: '#6c757d'
                            },
                            title: {
                                display: true,
                                text: 'Data',
                                font: {
                                    size: 14,
                                    weight: '600'
                                },
                                color: '#2c3e50'
                            }
                        }
                    }
                }
            });
        }

        // Inicialização
        let currentType = 'energy';
        let aggregated = aggregateDataByType(currentType);
        let sortedDates = Object.keys(aggregated).sort();
        let labels = formatLabels(sortedDates);
        let values = sortedDates.map(date => parseFloat(aggregated[date].toFixed(2)));

        createChart(labels, values, currentType);

        // Event listener para o seletor
        const selector = document.getElementById('dataTypeSelector');
        selector.addEventListener('change', (e) => {
            currentType = e.target.value;
            aggregated = aggregateDataByType(currentType);
            sortedDates = Object.keys(aggregated).sort();
            labels = formatLabels(sortedDates);
            values = sortedDates.map(date => parseFloat(aggregated[date].toFixed(2)));
            createChart(labels, values, currentType);
        });
    });
</script>