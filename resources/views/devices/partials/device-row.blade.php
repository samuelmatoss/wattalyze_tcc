@php
    $deviceId = $device->id;
    $dailyData = $dailyConsumption[$deviceId] ?? [];
    $todayConsumption = 0;
    
    if (!empty($dailyData)) {
        $lastRecord = end($dailyData);
        $todayConsumption = $lastRecord['value'] ?? 0;
    }
    
    $instantaneousPower = $influxData[$deviceId]['instantaneous_power'] ?? null;
@endphp

<style>
    /* Variáveis CSS para consistência */
    :root {
        --primary-dark: #2c3e50;
        --primary-green: #27ae60;
        --primary-blue: #3498db;
        --primary-orange: #e67e22;
        --primary-red: #e74c3c;
        --bg-light: #f8f9fa;
        --text-muted: #6c757d;
        --border-radius: 12px;
        --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.08);
        --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s ease;
    }

    /* Linha do dispositivo moderna */
    .device-row {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none !important;
        transition: var(--transition);
        position: relative;
    }

    .device-row::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: transparent;
        transition: var(--transition);
        border-radius: 0 2px 2px 0;
    }

    .device-row:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-light);
        background: rgba(255, 255, 255, 1);
    }

    .device-row:hover::before {
        background: var(--primary-green);
    }

    .device-row td {
        border: none !important;
        padding: 1.2rem 1rem;
        vertical-align: middle;
        position: relative;
    }

    /* Indicador de status moderno */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        position: relative;
        box-shadow: 0 0 8px rgba(0,0,0,0.2);
    }

    .status-indicator::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        border-radius: 50%;
        background: inherit;
        opacity: 0.3;
        animation: pulse 2s infinite;
    }

    .status-online {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        box-shadow: 0 0 12px rgba(39, 174, 96, 0.5);
    }

    .status-offline {
        background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        box-shadow: 0 0 12px rgba(149, 165, 166, 0.5);
    }

    .status-maintenance {
        background: linear-gradient(135deg, #e67e22, #f39c12);
        box-shadow: 0 0 12px rgba(230, 126, 34, 0.5);
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.3;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.1;
        }
        100% {
            transform: scale(1);
            opacity: 0.3;
        }
    }

    /* Badge moderno */
    .modern-status-badge {
        background: linear-gradient(135deg, var(--badge-color), var(--badge-color-dark));
        color: white;
        border: none;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        box-shadow: var(--shadow-light);
    }

    .modern-status-badge.bg-success {
        --badge-color: #27ae60;
        --badge-color-dark: #229954;
    }

    .modern-status-badge.bg-danger {
        --badge-color: #e74c3c;
        --badge-color-dark: #c0392b;
    }

    .modern-status-badge.bg-warning {
        --badge-color: #f39c12;
        --badge-color-dark: #e67e22;
    }

    .modern-status-badge i {
        margin-right: 4px;
        font-size: 0.7rem;
    }

    /* Informações do dispositivo */
    .device-info {
        display: flex;
        align-items: center;
    }

    .device-name {
        font-weight: 700;
        color: var(--primary-dark);
        font-size: 1rem;
        margin: 0;
    }

    .device-meta {
        margin-top: 4px;
    }

    .device-meta small {
        color: var(--text-muted);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        margin-bottom: 2px;
    }

    .device-meta i {
        margin-right: 4px;
        width: 12px;
    }

    /* Tipo de dispositivo */
    .device-type-info {
        display: flex;
        align-items: center;
    }

    .device-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-blue), #5dade2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        box-shadow: var(--shadow-light);
    }

    .device-type-name {
        font-weight: 600;
        color: var(--primary-dark);
        margin: 0;
    }

    .device-power-rating {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin-top: 2px;
    }

    /* Ambiente */
    .environment-info {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.05));
        border-radius: 8px;
        border-left: 3px solid var(--primary-green);
    }

    .environment-info i {
        margin-right: 8px;
        color: var(--primary-green);
    }

    /* Métricas de potência e consumo */
    .metric-value {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: rgba(52, 152, 219, 0.1);
        border-radius: 8px;
        border-left: 3px solid var(--primary-blue);
    }

    .metric-value.power {
        background: linear-gradient(135deg, rgba(230, 126, 34, 0.1), rgba(243, 156, 18, 0.05));
        border-left-color: var(--primary-orange);
    }

    .metric-value.power i {
        color: var(--primary-orange);
    }

    .metric-value.consumption {
        background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.05));
        border-left-color: var(--primary-green);
    }

    .metric-value.consumption i {
        color: var(--primary-green);
    }

    .metric-value i {
        margin-right: 8px;
        color: var(--primary-blue);
    }

    .metric-number {
        font-weight: 700;
        color: var(--primary-dark);
    }

    /* Botões de ação modernos */
    .modern-action-group {
        display: flex;
        gap: 4px;
    }

    .modern-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
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

    .modern-action-btn.view {
        background: linear-gradient(135deg, #3498db, #5dade2);
        color: white;
    }

    .modern-action-btn.edit {
        background: linear-gradient(135deg, #f39c12, #f1c40f);
        color: white;
    }

    .modern-action-btn.diagnostics {
        background: linear-gradient(135deg, #9b59b6, #af7ac5);
        color: white;
    }

    .modern-action-btn.delete {
        background: linear-gradient(135deg, #e74c3c, #ec7063);
        color: white;
    }

    /* Estado vazio */
    .empty-state {
        color: var(--text-muted);
        font-style: italic;
        display: flex;
        align-items: center;
    }

    .empty-state i {
        margin-right: 6px;
        opacity: 0.5;
    }
</style>

<tr class="device-row"
    data-status="{{ $device->status }}"
    data-environment="{{ $device->environment_id }}"
    data-type="{{ $device->device_type_id }}"
    data-name="{{ strtolower($device->name) }}"
    data-device-id="{{ $device->id }}">
    
    <!-- Dispositivo -->
    <td>
        <div class="device-info">
            <div class="status-indicator status-{{ $device->status }} me-3"></div>
            <div>
                <h6 class="device-name">{{ $device->name }}</h6>
                <div class="device-meta">
                    @if($device->location)
                        <small>
                            <i class="bi bi-geo-alt"></i>
                            {{ $device->location }}
                        </small>
                    @endif
                    @if($device->mac_address)
                        <small class="font-monospace">
                            <i class="bi bi-router"></i>
                            {{ $device->mac_address }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </td>
    
    <!-- Status -->
    <td>
        <span class="modern-status-badge @if($device->status === 'online') bg-success @elseif($device->status === 'offline') bg-danger @else bg-warning @endif">
            <i class="bi bi-circle-fill"></i>
            {{ ucfirst($device->status) }}
        </span>
    </td>
    
    <!-- Tipo -->
    <td>
        @if($device->deviceType)
            <div class="device-type-info">
                <div class="device-type-icon">
                    <i class="{{ $device->deviceType->icon ?? 'bi bi-cpu' }}"></i>
                </div>
                <div>
                    <div class="device-type-name">{{ $device->deviceType->name }}</div>
                    @if($device->rated_power)
                        <div class="device-power-rating">{{ number_format($device->rated_power, 0) }}W nominal</div>
                    @endif
                </div>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-question-circle"></i>
                Não definido
            </div>
        @endif
    </td>
    
    <!-- Ambiente -->
    <td>
        @if($device->environment)
            <div class="environment-info">
                <i class="bi bi-house-fill"></i>
                <span class="fw-medium">{{ $device->environment->name }}</span>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-question-circle"></i>
                Não definido
            </div>
        @endif
    </td>
    
    <!-- Potência Atual -->
    <td>
        @if($instantaneousPower !== null)
            <div class="metric-value power">
                <i class="bi bi-lightning-charge-fill"></i>
                <span class="metric-number">{{ number_format($instantaneousPower, 0) }} W</span>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-dash-circle"></i>
                N/A
            </div>
        @endif
    </td>
    
    <!-- Consumo Hoje -->
    <td>
        @if($todayConsumption > 0)
            <div class="metric-value consumption">
                <i class="bi bi-graph-up-arrow"></i>
                <span class="metric-number">{{ number_format($todayConsumption, 2) }} kWh</span>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-dash-circle"></i>
                N/A
            </div>
        @endif
    </td>
    
    <!-- Ações -->
    <td>
        <div class="modern-action-group">

            <a href="{{ route('devices.edit', $device) }}" 
               class="modern-action-btn edit" 
               title="Editar">
                <i class="bi bi-pencil"></i>
            </a>

            <button type="button" 
                    class="modern-action-btn delete delete-device-btn"
                    data-device-id="{{ $device->id }}"
                    data-device-name="{{ $device->name }}"
                    title="Excluir">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>