@php
use Carbon\Carbon;

$deviceId = $device->id;
$dailyDataRaw = $dailyConsumption[$deviceId] ?? [];

// Normaliza dailyData para garantir formato esperado (date => Y-m-d, value => float, time => string|null)
$dailyData = [];
foreach ($dailyDataRaw as $d) {
if (!is_array($d)) continue;
$date = $d['date'] ?? null;
$val = $d['value'] ?? 0;
$val = is_string($val) ? str_replace(',', '.', trim($val)) : $val;
$dailyData[] = [
'date' => $date,
'value' => is_numeric($val) ? (float)$val : 0.0,
'time' => $d['time'] ?? null
];
}

$instantaneousRaw = $influxData[$deviceId]['value'] ?? null;
$instantaneousPower = null;
if ($instantaneousRaw !== null && $instantaneousRaw !== '') {
$tmp = str_replace(',', '.', trim((string)$instantaneousRaw));
$instantaneousPower = is_numeric($tmp) ? (float)$tmp : null;
}

// *** Novidade: pegar o tempo da última atualização ***
$lastUpdateTimeRaw = null;

// 1. Tente o tempo do dado instantâneo do Influx
if (!empty($influxData[$deviceId]['time'])) {
$lastUpdateTimeRaw = $influxData[$deviceId]['time'];
}

// 2. Se não existir, tente pegar o tempo do último dado agregado diário
if (!$lastUpdateTimeRaw && !empty($dailyData)) {
$lastRecord = end($dailyData);
$lastUpdateTimeRaw = $lastRecord['time'] ?? null;
}

$lastUpdateTime = null;
if ($lastUpdateTimeRaw) {
try {
$lastUpdateTime = Carbon::parse($lastUpdateTimeRaw)
->setTimezone(config('app.timezone', 'America/Sao_Paulo'))
->toDateTimeString();
} catch (\Exception $ex) {
$lastUpdateTime = (string)$lastUpdateTimeRaw;
}
}

// Definir valor de hoje
$todayValue = 0.0;
$lastRecord = !empty($dailyData) ? end($dailyData) : null;

$typeName = strtolower($device->deviceType->name ?? '');
if (str_contains($typeName, 'temperature')) {
$unit = '°C';
$title = 'Temperatura Atual';
$chartLabel = 'Temperatura (°C) - 7 dias';
$isEnergy = false;
$currentValueLabel = 'Atual';
} elseif (str_contains($typeName, 'humidity')) {
$unit = '%';
$title = 'Umidade Atual';
$chartLabel = 'Umidade (%) - 7 dias';
$isEnergy = false;
$currentValueLabel = 'Atual';
} else {
$unit = 'kWh';
$title = 'Consumo Hoje';
$chartLabel = 'Consumo Diário (7 dias)';
$isEnergy = true;
$currentValueLabel = 'Hoje';
}

if ($isEnergy) {
$todayValue = $lastRecord && isset($lastRecord['value']) ? (float)$lastRecord['value'] : ($instantaneousPower ?? 0);
} else {
$todayValue = $instantaneousPower ?? 0;
}

// Preparar dados do gráfico (array de {date, value})
$chartData = array_map(function($d) {
return [
'date' => $d['date'] ?? null,
'value' => isset($d['value']) && is_numeric($d['value']) ? (float)$d['value'] : 0.0
];
}, $dailyData);
@endphp

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

    /* Card principal moderno */
    .modern-device-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .modern-device-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        pointer-events: none;
        opacity: 0;
        transition: var(--transition);
    }

    .modern-device-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-medium);
    }

    .modern-device-card:hover::before {
        opacity: 1;
    }

    /* Header moderno com gradiente baseado no status */
    .modern-card-header {
        background: linear-gradient(135deg, var(--status-color) 0%, var(--status-color-dark) 100%);
        border: none;
        padding: 1.5rem;
        position: relative;
        overflow: visible;
    }

    .modern-card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    .modern-card-header.status-online {
        --status-color: #27ae60;
        --status-color-dark: #229954;
    }

    .modern-card-header.status-offline {
        --status-color: #2c3e50;
        --status-color-dark: #34495e;
    }

    .modern-card-header.status-maintenance {
        --status-color: #e67e22;
        --status-color-dark: #d35400;
    }

    /* Status indicator moderno */
    .modern-status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        animation: pulse 2s infinite;
    }

    .modern-status-dot.online {
        background: #00ff00;
    }

    .modern-status-dot.offline {
        background: #ff6b6b;
        animation: none;
    }

    .modern-status-dot.maintenance {
        background: #ffd93d;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        50% {
            box-shadow: 0 0 16px rgba(255, 255, 255, 0.8);
        }

        100% {
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }
    }

    /* Badge moderno */
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

    /* Dropdown moderno */
    .modern-dropdown-btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        transition: var(--transition);
    }

    .modern-dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .modern-dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 12px;
        box-shadow: var(--shadow-medium);
        padding: 0.5rem 0;
        position: absolute;
        z-index: 1000;
    }

    .dropdown {
        position: relative;
    }

    .modern-dropdown-item {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        transition: var(--transition);
    }

    .modern-dropdown-item:hover {
        background: rgba(39, 174, 96, 0.1);
        color: var(--primary-green);
    }

    /* Corpo do card */
    .modern-card-body {
        padding: 1.5rem;
        background: rgba(248, 249, 250, 0.5);
    }

    .modern-info-item {
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 8px;
        backdrop-filter: blur(5px);
        transition: var(--transition);
    }

    .modern-info-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(4px);
    }

    .modern-info-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .modern-info-value {
        font-weight: 600;
        color: var(--primary-dark);
        display: flex;
        align-items: center;
    }

    .modern-info-icon {
        margin-right: 8px;
        color: var(--primary-green);
        font-size: 1.1rem;
    }

    /* Seção de métricas */
    .modern-metrics {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 1.5rem;
        background: rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(5px);
    }

    .modern-metric-item {
        text-align: center;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 12px;
        transition: var(--transition);
        backdrop-filter: blur(10px);
    }

    .modern-metric-item:hover {
        background: rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
    }

    .modern-metric-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.2rem;
        margin: 0 auto 8px;
        background: linear-gradient(135deg, var(--icon-color), var(--icon-color-dark));
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        box-shadow: var(--shadow-light);
    }

    .modern-metric-icon.success {
        --icon-color: #27ae60;
        --icon-color-dark: #229954;
    }

    .modern-metric-icon.primary {
        --icon-color: #3498db;
        --icon-color-dark: #2980b9;
    }

    .modern-metric-icon.dark {
        --icon-color: #2c3e50;
        --icon-color-dark: #34495e;
    }

    .modern-metric-icon.warning {
        --icon-color: #f39c12;
        --icon-color-dark: #e67e22;
    }

    .modern-metric-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .modern-metric-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-dark);
        line-height: 1.2;
    }

    /* Footer com gráfico */
    .modern-card-footer {
        background: rgba(255, 255, 255, 0.95);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .modern-chart-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .modern-chart-title {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0;
    }

    .modern-total-badge {
        background: linear-gradient(135deg, var(--primary-green), #2ecc71);
        color: white;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: var(--shadow-light);
        border: none;
    }

    /* Container do gráfico */
    .modern-chart-container {
        position: relative;
        height: 80px;
        width: 100%;
        background: rgba(248, 249, 250, 0.5);
        border-radius: 12px;
        overflow: hidden;
        backdrop-filter: blur(5px);
    }

    .modern-chart-canvas {
        width: 100% !important;
        height: 80px !important;
    }

    .modern-chart-empty {
        height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        background: rgba(248, 249, 250, 0.5);
        border-radius: 12px;
    }

    .modern-chart-empty i {
        font-size: 1.5rem;
        opacity: 0.5;
        margin-bottom: 8px;
    }

    .modern-chart-empty div {
        font-size: 0.85rem;
        font-weight: 500;
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

    .fade-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .modern-device-card {
            margin-bottom: 1rem;
        }

        .modern-card-header {
            padding: 1rem;
        }

        .modern-card-body {
            padding: 1rem;
        }

        .modern-card-footer {
            padding: 1rem;
        }

        .modern-metric-icon {
            width: 40px;
            height: 40px;
        }
    }
</style>

<div class="col-lg-4 col-md-6 mb-4 device-carda fade-in"
    data-status="{{ $device->status }}"
    data-environment="{{ $device->environment_id }}"
    data-type="{{ $device->device_type_id }}"
    data-name="{{ strtolower($device->name) }}"
    data-device-id="{{ $device->id }}">

    <div class="card modern-device-card">

        {{-- Cabeçalho Moderno --}}
        <div class="card-header modern-card-header status-{{ $device->status }} text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <span class="modern-status-dot {{ $device->status }}"></span>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold" style="font-size: 1.1rem;">{{ $device->name }}</h6>
                        <small class="opacity-75">{{ $device->deviceType->name ?? 'Tipo desconhecido' }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="modern-badge">
                        {{ ucfirst($device->status) }}
                    </span>
                    <div class="dropdown">
                        <button class="btn modern-dropdown-btn" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical text-white"></i>
                        </button>
                        <ul class="dropdown-menu modern-dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item modern-dropdown-item" href="{{ route('devices.edit', $device) }}">
                                    <i class="bi bi-pencil text-warning me-2"></i> Editar
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <button class="dropdown-item modern-dropdown-item text-danger delete-device-btn"
                                    data-device-id="{{ $device->id }}"
                                    data-device-name="{{ $device->name }}">
                                    <i class="bi bi-trash me-2"></i> Excluir
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Corpo Moderno --}}
        <div class="card-body modern-card-body">
            @if($device->environment)
            <div class="modern-info-item">
                <div class="modern-info-label">Ambiente</div>
                <div class="modern-info-value">
                    <i class="bi bi-house modern-info-icon"></i>
                    {{ $device->environment->name }}
                </div>
            </div>
            @endif

            @if($device->location)
            <div class="modern-info-item">
                <div class="modern-info-label">Localização</div>
                <div class="modern-info-value">
                    <i class="bi bi-geo-alt modern-info-icon"></i>
                    {{ $device->location }}
                </div>
            </div>
            @endif

            {{-- Seção de Métricas Modernizada --}}
            <div class="modern-metrics">
                <div class="row g-2">
                    @if($isEnergy)
                    <div class="col-4">
                        <div class="modern-metric-item">
                            <div class="modern-metric-icon success">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="modern-metric-label">Potência</div>
                            <div class="modern-metric-value">
                                {{ is_numeric($instantaneousPower) ? number_format($instantaneousPower, 0, ',', '.') . ' W' : 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-{{ $isEnergy ? '4' : '6' }}">
                        <div class="modern-metric-item">
                            <div class="modern-metric-icon {{ $isEnergy ? 'primary' : ($unit==='°C' ? 'warning' : 'primary') }}">
                                <i class="fas fa-{{ $isEnergy ? 'chart-line' : ($unit==='°C' ? 'thermometer-half' : 'tint') }}"></i>
                            </div>
                            <div class="modern-metric-label">{{ $currentValueLabel }}</div>
                            <div class="modern-metric-value">
                                {{ is_numeric($todayValue) ? number_format($todayValue, $isEnergy ? 2 : 1, ',', '.') . ' ' . $unit : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-{{ $isEnergy ? '4' : '6' }}">
                        <div class="modern-metric-item">
                            <div class="modern-metric-icon dark">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="modern-metric-label">Atualização</div>
                            <div class="modern-metric-value">
                                {{ $lastUpdateTime ? \Carbon\Carbon::parse($lastUpdateTime)->diffForHumans() : 'Nunca' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Moderno com Gráfico --}}
        <div class="card-footer modern-card-footer">
            <div class="modern-chart-header">
                <div class="modern-chart-title">{{ $chartLabel }}</div>
                @if(!empty($dailyData) && $isEnergy)
                <span class="modern-total-badge" style="margin-left:6vw;">
                    Total: {{ number_format(array_sum(array_column($dailyData, 'value')), 2) }} {{ $unit }}
                </span>
                @endif
            </div>

            <div class="modern-chart-container">
                @if(!empty($dailyData))
                <canvas id="dailyChart-{{ $deviceId }}"
                    class="modern-chart-canvas"
                    data-device-id="{{ $deviceId }}"
                    data-status="{{ $device->status }}"
                    data-unit="{{ $unit }}"
                    data-chart-data="{{ json_encode(array_values($dailyData)) }}">
                </canvas>
                @else
                <div class="modern-chart-empty">
                    <i class="fas fa-chart-line"></i>
                    <div>Sem dados disponíveis</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- FontAwesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    // Aguardar Chart.js carregar antes de criar gráficos
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se Chart.js está disponível
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js não está carregado. Gráficos não serão exibidos.');
            return;
        }

        // Criar gráfico para este dispositivo
        createDeviceChart('{{ $deviceId }}');
    });

    function createDeviceChart(deviceId) {
        const canvas = document.getElementById(`dailyChart-${deviceId}`);
        if (!canvas) {
            console.warn(`Canvas para device ${deviceId} não encontrado`);
            return;
        }

        try {
            const chartData = JSON.parse(canvas.getAttribute('data-chart-data') || '[]');
            const status = canvas.getAttribute('data-status');
            const unit = canvas.getAttribute('data-unit');

            if (!chartData || chartData.length === 0) {
                console.warn(`Sem dados para device ${deviceId}`);
                return;
            }

            // Cores modernas baseadas no status
            const colors = {
                online: {
                    border: '#27ae60',
                    background: 'rgba(39, 174, 96, 0.1)',
                    gradient: ['#27ae60', '#2ecc71']
                },
                offline: {
                    border: '#e74c3c',
                    background: 'rgba(231, 76, 60, 0.1)',
                    gradient: ['#e74c3c', '#c0392b']
                },
                maintenance: {
                    border: '#e67e22',
                    background: 'rgba(230, 126, 34, 0.1)',
                    gradient: ['#e67e22', '#d35400']
                }
            };

            const color = colors[status] || colors.offline;
            const ctx = canvas.getContext('2d');

            // Criar gradiente
            const gradient = ctx.createLinearGradient(0, 0, 0, 80);
            gradient.addColorStop(0, color.gradient[0] + '40');
            gradient.addColorStop(1, color.gradient[1] + '10');

            // Processar dados
            const labels = chartData.map(item => {
                if (!item.date) return 'N/A';
                const dateParts = item.date.split('-');
                const dt = new Date(
                    parseInt(dateParts[0]),
                    parseInt(dateParts[1]) - 1,
                    parseInt(dateParts[2]),
                    12, 0, 0
                );
                return dt.toLocaleDateString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit'
                });
            });

            const values = chartData.map(item => {
                const value = parseFloat(item.value);
                return isNaN(value) ? 0 : value;
            });

            // Criar gráfico moderno
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderColor: color.border,
                        backgroundColor: gradient,
                        pointBackgroundColor: color.border,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: color.border,
                            borderWidth: 1,
                            cornerRadius: 12,
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
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxRotation: 0,
                                font: {
                                    size: 10,
                                    weight: '500'
                                },
                                color: '#6c757d'
                            }
                        },
                        y: {
                            display: false,
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

        } catch (error) {
            console.error(`Erro ao criar gráfico para device ${deviceId}:`, error);
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            const originalClass = icon.className;

            icon.className = 'fas fa-check text-success';
            setTimeout(() => {
                icon.className = originalClass;
            }, 2000);
        }).catch(function(err) {
            console.error('Erro ao copiar: ', err);
        });
    }
</script>