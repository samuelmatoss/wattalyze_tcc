@include('components.headerDash')
<style>
    .sidebar {
        height: 130vh;
    }
</style>
<div class="col-xl-9" style="margin-left: 20vw; ">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Cadastrar Novo Dispositivo</h1>
                <p class="text-muted mb-0">Adicione um dispositivo para começar o monitoramento de energia</p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('devices.index') }}" class="text-decoration-none">
                            <i class="bi bi-house"></i> Dispositivos
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Novo Dispositivo</li>
                </ol>
            </nav>
        </div>

        <!-- Mensagens de Erro Otimizadas -->
        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <strong>Ops! Encontramos alguns problemas:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Formulário Otimizado -->
        <div class="row">
            <div class="col-lg-8 col-xl-9">
                <form action="{{ route('devices.store') }}" method="POST" id="deviceForm" novalidate>
                    @csrf

                    <!-- Card: Informações Básicas -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle"></i>
                                Informações Básicas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        Nome do Dispositivo *
                                        <i class="bi bi-question-circle"
                                            data-bs-toggle="tooltip"
                                            title="Nome para identificar facilmente o dispositivo"></i>
                                    </label>
                                    <input type="text"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        required
                                        maxlength="255"
                                        placeholder="Ex: TV Sala, Geladeira Cozinha">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status Inicial *</label>
                                    <select id="status"
                                        name="status"
                                        required
                                        class="form-select @error('status') is-invalid @enderror">
                                        <option value="offline" {{ old('status', 'offline') == 'offline' ? 'selected' : '' }}>
                                            <span class="text-danger">● Offline</span>
                                        </option>
                                        <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>
                                            <span class="text-success">● Online</span>
                                        </option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                                            <span class="text-warning">● Manutenção</span>
                                        </option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="device_type_id" class="form-label">Tipo de Dispositivo</label>
                                    <select id="device_type_id"
                                        name="device_type_id"
                                        class="form-select @error('device_type_id') is-invalid @enderror">
                                        <option value="">Selecione um tipo</option>
                                        @foreach ($deviceTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('device_type_id') == $type->id ? 'selected' : '' }}
                                            data-icon="{{ $type->icon ?? 'bi bi-cpu' }}">
                                            {{ $type->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('device_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="environment_id" class="form-label">Ambiente</label>
                                    <div class="input-group">
                                        <select id="environment_id"
                                            name="environment_id"
                                            class="form-select @error('environment_id') is-invalid @enderror">
                                            <option value="">Selecione um ambiente</option>
                                            @foreach ($environments as $env)
                                            <option value="{{ $env->id }}" {{ old('environment_id') == $env->id ? 'selected' : '' }}>
                                                {{ $env->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-outline-secondary"
                                            type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#newEnvironmentModal"
                                            title="Criar novo ambiente">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    @error('environment_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Localização Específica</label>
                                <input type="text"
                                    id="location"
                                    name="location"
                                    value="{{ old('location') }}"
                                    class="form-control @error('location') is-invalid @enderror"
                                    maxlength="255"
                                    placeholder="Ex: Próximo à janela, Mesa do escritório">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Card: Identificação Técnica -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-gear"></i>
                                Identificação Técnica
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mac_address" class="form-label">
                                        Endereço MAC *
                                        <i class="bi bi-question-circle"
                                            data-bs-toggle="tooltip"
                                            title="Endereço MAC único do dispositivo (formato: XX:XX:XX:XX:XX:XX)"></i>
                                    </label>
                                    <input type="text"
                                        id="mac_address"
                                        name="mac_address"
                                        value="{{ old('mac_address') }}"
                                        class="form-control font-monospace @error('mac_address') is-invalid @enderror"
                                        required
                                        maxlength="17"
                                        placeholder="00:11:22:33:44:55"
                                        pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
                                    @error('mac_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Formato: XX:XX:XX:XX:XX:XX</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="serial_number" class="form-label">Número de Série</label>
                                    <input type="text"
                                        id="serial_number"
                                        name="serial_number"
                                        value="{{ old('serial_number') }}"
                                        class="form-control font-monospace @error('serial_number') is-invalid @enderror"
                                        maxlength="255"
                                        placeholder="Ex: SN123456789">
                                    @error('serial_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="model" class="form-label">Modelo</label>
                                    <input type="text"
                                        id="model"
                                        name="model"
                                        value="{{ old('model') }}"
                                        class="form-control @error('model') is-invalid @enderror"
                                        maxlength="255"
                                        placeholder="Ex: XR-2000">
                                    @error('model')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="manufacturer" class="form-label">Fabricante</label>
                                    <input type="text"
                                        id="manufacturer"
                                        name="manufacturer"
                                        value="{{ old('manufacturer') }}"
                                        class="form-control @error('manufacturer') is-invalid @enderror"
                                        maxlength="255"
                                        placeholder="Ex: Samsung, LG">
                                    @error('manufacturer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="firmware_version" class="form-label">Versão do Firmware</label>
                                    <input type="text"
                                        id="firmware_version"
                                        name="firmware_version"
                                        value="{{ old('firmware_version') }}"
                                        class="form-control @error('firmware_version') is-invalid @enderror"
                                        maxlength="255"
                                        placeholder="Ex: v1.2.3">
                                    @error('firmware_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Especificações Elétricas -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-lightning"></i>
                                Especificações Elétricas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="rated_power" class="form-label">
                                        Potência Nominal (W)
                                        <i class="bi bi-question-circle"
                                            data-bs-toggle="tooltip"
                                            title="Potência máxima do dispositivo em Watts"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                            step="0.01"
                                            min="0"
                                            max="999999"
                                            id="rated_power"
                                            name="rated_power"
                                            value="{{ old('rated_power') }}"
                                            class="form-control @error('rated_power') is-invalid @enderror"
                                            placeholder="Ex: 150">
                                        <span class="input-group-text">W</span>
                                        @error('rated_power')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="rated_voltage" class="form-label">
                                        Tensão Nominal (V)
                                        <i class="bi bi-question-circle"
                                            data-bs-toggle="tooltip"
                                            title="Tensão de operação do dispositivo"></i>
                                    </label>
                                    <div class="input-group">
                                        <select class="form-select @error('rated_voltage') is-invalid @enderror"
                                            id="rated_voltage"
                                            name="rated_voltage">
                                            <option value="">Selecione</option>
                                            <option value="110" {{ old('rated_voltage') == '110' ? 'selected' : '' }}>110V</option>
                                            <option value="127" {{ old('rated_voltage') == '127' ? 'selected' : '' }}>127V</option>
                                            <option value="220" {{ old('rated_voltage') == '220' ? 'selected' : '' }}>220V</option>
                                            <option value="240" {{ old('rated_voltage') == '240' ? 'selected' : '' }}>240V</option>
                                            <option value="custom">Outro</option>
                                        </select>
                                        @error('rated_voltage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="number"
                                        step="0.01"
                                        min="0"
                                        max="1000"
                                        id="custom_voltage"
                                        name="custom_voltage"
                                        class="form-control mt-2"
                                        placeholder="Voltagem customizada"
                                        style="display: none;">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="installation_date" class="form-label">Data de Instalação</label>
                                    <input type="date"
                                        id="installation_date"
                                        name="installation_date"
                                        value="{{ old('installation_date') }}"
                                        class="form-control @error('installation_date') is-invalid @enderror"
                                        max="{{ date('Y-m-d') }}">
                                    @error('installation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estimativa de Consumo -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info d-flex align-items-center" id="consumptionEstimate" style="display: none !important;">
                                        <i class="bi bi-lightbulb me-2"></i>
                                        <div>
                                            <strong>Estimativa de Consumo:</strong>
                                            <span id="estimateText"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-info-circle"></i>
                                        Os campos marcados com * são obrigatórios
                                    </p>
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('devices.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="button" class="btn btn-outline-primary" id="previewBtn">
                                        <i class="bi bi-eye"></i> Prévia
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <span class="spinner-border spinner-border-sm me-2" id="submitSpinner" style="display: none;"></span>
                                        <i class="bi bi-save"></i> Salvar Dispositivo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Sidebar com Ajuda -->
            <div class="col-lg-4 col-xl-3">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Card de Ajuda -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-question-circle"></i>
                                Precisa de Ajuda?
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="helpAccordion">
                                <div class="accordion-item">
                                    <h6 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpMAC">
                                            Como encontrar o MAC?
                                        </button>
                                    </h6>
                                    <div id="helpMAC" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            <p>O endereço MAC pode ser encontrado:</p>
                                            <ul class="mb-0">
                                                <li>Na etiqueta do dispositivo</li>
                                                <li>No manual do produto</li>
                                                <li>Nas configurações de rede</li>
                                                <li>No aplicativo do fabricante</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h6 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpPower">
                                            Potência Nominal
                                        </button>
                                    </h6>
                                    <div id="helpPower" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            <p>A potência nominal está geralmente:</p>
                                            <ul class="mb-2">
                                                <li>Na etiqueta energética</li>
                                                <li>No manual do produto</li>
                                                <li>Na parte traseira/inferior do dispositivo</li>
                                            </ul>
                                            <p class="mb-0"><strong>Exemplos comuns:</strong></p>
                                            <ul class="mb-0 small">
                                                <li>TV 42": 100-150W</li>
                                                <li>Geladeira: 150-400W</li>
                                                <li>Micro-ondas: 700-1200W</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h6 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#helpTips">
                                            Dicas Importantes
                                        </button>
                                    </h6>
                                    <div id="helpTips" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                        <div class="accordion-body small">
                                            <ul class="mb-0">
                                                <li>Use nomes descritivos para facilitar identificação</li>
                                                <li>Organize por ambientes para melhor controle</li>
                                                <li>Mantenha as informações técnicas atualizadas</li>
                                                <li>Configure o tipo correto para relatórios precisos</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Card -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="bi bi-list-check"></i>
                                Progresso do Cadastro
                            </h6>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" id="formProgress" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="progressText">0% concluído</small>

                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkBasic" disabled>
                                    <label class="form-check-label small" for="checkBasic">
                                        Informações básicas
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkTechnical" disabled>
                                    <label class="form-check-label small" for="checkTechnical">
                                        Dados técnicos
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkSpecs" disabled>
                                    <label class="form-check-label small" for="checkSpecs">
                                        Especificações (opcional)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Novo Ambiente -->
<div class="modal fade" id="newEnvironmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Criar Novo Ambiente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newEnvironmentForm">
                    <div class="mb-3">
                        <label for="newEnvName" class="form-label">Nome do Ambiente *</label>
                        <input type="text" class="form-control" id="newEnvName" placeholder="Ex: Sala de Estar" required>
                    </div>
                    <div class="mb-3">
                        <label for="newEnvDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="newEnvDescription" rows="2" placeholder="Descrição opcional"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveNewEnvironment">
                    <i class="bi bi-save"></i> Criar Ambiente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Prévia do Dispositivo -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye"></i>
                    Prévia do Dispositivo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Conteúdo será gerado dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('deviceForm').submit()">
                    <i class="bi bi-save"></i> Confirmar e Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS Otimizado -->
<style>
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }

    .progress-bar {
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .btn-group .btn {
        position: relative;
        z-index: 1;
    }

    .sticky-top {
        z-index: 10;
    }

    .form-check-input:checked[disabled] {
        background-color: #198754;
        border-color: #198754;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4M7.2 6l-1.4 1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    @media (max-width: 768px) {
        .col-xl-9 {
            margin-left: 0 !important;
            margin-top: 0 !important;
        }

        .sticky-top {
            position: relative !important;
            top: auto !important;
        }
    }
</style>

<!-- JavaScript Otimizado -->
<script>
    class DeviceFormManager {
        constructor() {
            this.form = document.getElementById('deviceForm');
            this.requiredFields = ['name', 'mac_address', 'status'];
            this.optionalFields = ['device_type_id', 'environment_id', 'location'];
            this.technicalFields = ['serial_number', 'model', 'manufacturer', 'firmware_version'];

            this.init();
        }

        init() {
            this.setupEventListeners();
            this.setupValidation();
            this.setupTooltips();
            this.updateProgress();
            this.setupMACFormatter();
            this.setupVoltageHandler();
            this.calculateConsumptionEstimate();
        }

        setupEventListeners() {
            // Form submission
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));

            // Real-time validation
            this.form.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('input', () => this.validateField(field));
                field.addEventListener('blur', () => this.validateField(field));
                field.addEventListener('change', () => {
                    this.validateField(field);
                    this.updateProgress();
                    this.calculateConsumptionEstimate();
                });
            });

            // Preview button
            document.getElementById('previewBtn').addEventListener('click', () => this.showPreview());

            // New environment
            document.getElementById('saveNewEnvironment').addEventListener('click', () => this.createNewEnvironment());
        }

        setupValidation() {
            // MAC address validation
            const macField = document.getElementById('mac_address');
            macField.addEventListener('input', (e) => {
                let value = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
                if (value.length > 12) value = value.substring(0, 12);

                // Format as MAC address
                const formatted = value.match(/.{1,2}/g)?.join(':') || value;
                if (formatted !== e.target.value) {
                    e.target.value = formatted;
                }
            });

            // Name field validation
            const nameField = document.getElementById('name');
            nameField.addEventListener('input', (e) => {
                if (e.target.value.length > 255) {
                    e.target.value = e.target.value.substring(0, 255);
                }
            });
        }

        setupMACFormatter() {
            const macInput = document.getElementById('mac_address');
            macInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9A-Fa-f]/g, '');
                let formatted = '';

                for (let i = 0; i < value.length; i += 2) {
                    if (i > 0) formatted += ':';
                    formatted += value.substr(i, 2);
                }

                e.target.value = formatted.toUpperCase();
            });
        }

        setupVoltageHandler() {
            const voltageSelect = document.getElementById('rated_voltage');
            const customVoltage = document.getElementById('custom_voltage');

            voltageSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customVoltage.style.display = 'block';
                    customVoltage.required = true;
                } else {
                    customVoltage.style.display = 'none';
                    customVoltage.required = false;
                    customVoltage.value = '';
                }
            });
        }

        setupTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        validateField(field) {
            const value = field.value.trim();
            let isValid = true;
            let message = '';

            // Required field validation
            if (field.hasAttribute('required') && !value) {
                isValid = false;
                message = 'Este campo é obrigatório';
            }

            // Specific validations
            switch (field.id) {
                case 'mac_address':
                    const macPattern = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
                    if (value && !macPattern.test(value)) {
                        isValid = false;
                        message = 'Formato de MAC inválido (XX:XX:XX:XX:XX:XX)';
                    }
                    break;

                case 'name':
                    if (value && value.length < 2) {
                        isValid = false;
                        message = 'Nome deve ter pelo menos 2 caracteres';
                    }
                    break;

                case 'rated_power':
                    if (value && (isNaN(value) || value < 0 || value > 999999)) {
                        isValid = false;
                        message = 'Potência deve ser um número entre 0 e 999999';
                    }
                    break;

                case 'custom_voltage':
                    if (field.style.display !== 'none' && value && (isNaN(value) || value < 0 || value > 1000)) {
                        isValid = false;
                        message = 'Voltagem deve ser um número entre 0 e 1000';
                    }
                    break;
            }

            // Update field appearance
            field.classList.toggle('is-invalid', !isValid);
            field.classList.toggle('is-valid', isValid && value);

            // Update error message
            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = message;
            }

            return isValid;
        }

        updateProgress() {
            let completed = 0;
            let total = 0;

            // Check basic fields
            const basicComplete = this.requiredFields.every(fieldId => {
                const field = document.getElementById(fieldId);
                return field && field.value.trim();
            });

            // Check technical fields
            const technicalComplete = this.technicalFields.some(fieldId => {
                const field = document.getElementById(fieldId);
                return field && field.value.trim();
            });

            // Check optional specs
            const specsComplete = document.getElementById('rated_power').value.trim() ||
                document.getElementById('rated_voltage').value.trim();

            // Update checkboxes and progress
            document.getElementById('checkBasic').checked = basicComplete;
            document.getElementById('checkTechnical').checked = technicalComplete;
            document.getElementById('checkSpecs').checked = specsComplete;

            let progress = 0;
            if (basicComplete) progress += 60;
            if (technicalComplete) progress += 25;
            if (specsComplete) progress += 15;

            document.getElementById('formProgress').style.width = progress + '%';
            document.getElementById('progressText').textContent = progress + '% concluído';
        }

        calculateConsumptionEstimate() {
            const power = parseFloat(document.getElementById('rated_power').value);
            const estimateDiv = document.getElementById('consumptionEstimate');
            const estimateText = document.getElementById('estimateText');

            if (power > 0) {
                const dailyKwh = (power * 8) / 1000; // 8 horas por dia
                const monthlyKwh = dailyKwh * 30;
                const monthlyCost = monthlyKwh * 0.65; // R$ 0,65 por kWh (média)

                estimateText.innerHTML = `
                <br>• Consumo diário estimado: <strong>${dailyKwh.toFixed(2)} kWh</strong>
                <br>• Consumo mensal estimado: <strong>${monthlyKwh.toFixed(2)} kWh</strong>
                <br>• Custo mensal estimado: <strong>R$ ${monthlyCost.toFixed(2)}</strong>
                <br><small class="text-muted">(Baseado em 8h/dia de uso e tarifa média)</small>
            `;
                estimateDiv.style.display = 'flex';
            } else {
                estimateDiv.style.display = 'none';
            }
        }

        showPreview() {
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData);

            // Get selected option texts
            const deviceType = document.getElementById('device_type_id');
            const environment = document.getElementById('environment_id');

            const deviceTypeName = deviceType.selectedOptions[0]?.text || 'Não definido';
            const environmentName = environment.selectedOptions[0]?.text || 'Não definido';

            const previewHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações Básicas</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Nome:</strong></td><td>${data.name || '<em>Não informado</em>'}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-${data.status === 'online' ? 'success' : data.status === 'offline' ? 'danger' : 'warning'}">${data.status || 'offline'}</span></td></tr>
                        <tr><td><strong>Tipo:</strong></td><td>${deviceTypeName}</td></tr>
                        <tr><td><strong>Ambiente:</strong></td><td>${environmentName}</td></tr>
                        <tr><td><strong>Localização:</strong></td><td>${data.location || '<em>Não informada</em>'}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Especificações Técnicas</h6>
                    <table class="table table-sm">
                        <tr><td><strong>MAC:</strong></td><td><code>${data.mac_address || '<em>Não informado</em>'}</code></td></tr>
                        <tr><td><strong>Serial:</strong></td><td><code>${data.serial_number || '<em>Não informado</em>'}</code></td></tr>
                        <tr><td><strong>Modelo:</strong></td><td>${data.model || '<em>Não informado</em>'}</td></tr>
                        <tr><td><strong>Fabricante:</strong></td><td>${data.manufacturer || '<em>Não informado</em>'}</td></tr>
                        <tr><td><strong>Firmware:</strong></td><td>${data.firmware_version || '<em>Não informado</em>'}</td></tr>
                    </table>
                </div>
            </div>
            ${data.rated_power || data.rated_voltage ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Especificações Elétricas</h6>
                    <table class="table table-sm">
                        ${data.rated_power ? `<tr><td><strong>Potência:</strong></td><td>${data.rated_power} W</td></tr>` : ''}
                        ${data.rated_voltage ? `<tr><td><strong>Tensão:</strong></td><td>${data.rated_voltage} V</td></tr>` : ''}
                        ${data.installation_date ? `<tr><td><strong>Instalação:</strong></td><td>${new Date(data.installation_date).toLocaleDateString('pt-BR')}</td></tr>` : ''}
                    </table>
                </div>
            </div>
            ` : ''}
        `;

            document.getElementById('previewContent').innerHTML = previewHTML;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }

        createNewEnvironment() {
            const name = document.getElementById('newEnvName').value.trim();
            const description = document.getElementById('newEnvDescription').value.trim();

            if (!name) {
                alert('Nome do ambiente é obrigatório');
                return;
            }

            // Simulate API call (replace with actual implementation)
            const newOption = new Option(name, 'temp_' + Date.now(), true, true);
            document.getElementById('environment_id').add(newOption);

            bootstrap.Modal.getInstance(document.getElementById('newEnvironmentModal')).hide();
            document.getElementById('newEnvironmentForm').reset();

            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
            <strong>Sucesso!</strong> Ambiente "${name}" criado temporariamente. 
            Será salvo definitivamente junto com o dispositivo.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
            this.form.insertBefore(alert, this.form.firstChild);
        }

        handleSubmit(e) {
            let isFormValid = true;

            // Validate all fields
            this.form.querySelectorAll('input, select, textarea').forEach(field => {
                if (!this.validateField(field)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();

                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                <strong>Erro!</strong> Por favor, corrija os campos destacados antes de continuar.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
                this.form.insertBefore(alert, this.form.firstChild);

                // Scroll to first error
                const firstError = this.form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }

                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitSpinner = document.getElementById('submitSpinner');

            submitBtn.disabled = true;
            submitSpinner.style.display = 'inline-block';
            submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Salvando...
        `;
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', () => {
        new DeviceFormManager();
    });
</script>