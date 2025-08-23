@include('components.headerDash')

<div class="container" style="margin-left: 30vw;">
    <h1>Editar Dispositivo</h1>

    <form action="{{ route('devices.update', $device->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nome do Dispositivo</label>
            <input type="text" class="form-control" id="name" name="name"
                   value="{{ old('name', $device->name) }}" required>
        </div>

        {{-- MAC Address --}}
        <div class="mb-3">
            <label for="mac_address" class="form-label">MAC Address</label>
            <input type="text" class="form-control" id="mac_address" name="mac_address"
                   value="{{ old('mac_address', $device->mac_address) }}" required>
        </div>

        {{-- Número de Série --}}
        <div class="mb-3">
            <label for="serial_number" class="form-label">Número de Série</label>
            <input type="text" class="form-control" id="serial_number" name="serial_number"
                   value="{{ old('serial_number', $device->serial_number) }}">
        </div>

        {{-- Modelo --}}
        <div class="mb-3">
            <label for="model" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="model" name="model"
                   value="{{ old('model', $device->model) }}">
        </div>

        {{-- Fabricante --}}
        <div class="mb-3">
            <label for="manufacturer" class="form-label">Fabricante</label>
            <input type="text" class="form-control" id="manufacturer" name="manufacturer"
                   value="{{ old('manufacturer', $device->manufacturer) }}">
        </div>

        {{-- Versão do Firmware --}}
        <div class="mb-3">
            <label for="firmware_version" class="form-label">Versão do Firmware</label>
            <input type="text" class="form-control" id="firmware_version" name="firmware_version"
                   value="{{ old('firmware_version', $device->firmware_version) }}">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="online" {{ old('status', $device->status) == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ old('status', $device->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="maintenance" {{ old('status', $device->status) == 'maintenance' ? 'selected' : '' }}>Manutenção</option>
            </select>
        </div>

        {{-- Localização --}}
        <div class="mb-3">
            <label for="location" class="form-label">Localização</label>
            <input type="text" class="form-control" id="location" name="location"
                   value="{{ old('location', $device->location) }}">
        </div>

        {{-- Data de Instalação --}}
        <div class="mb-3">
            <label for="installation_date" class="form-label">Data de Instalação</label>
            <input type="date" class="form-control" id="installation_date" name="installation_date"
                   value="{{ old('installation_date', $device->installation_date ? $device->installation_date->format('Y-m-d') : '') }}">
        </div>

        {{-- Potência Nominal --}}
        <div class="mb-3">
            <label for="rated_power" class="form-label">Potência Nominal (W)</label>
            <input type="number" step="0.01" class="form-control" id="rated_power" name="rated_power"
                   value="{{ old('rated_power', $device->rated_power) }}">
        </div>

        {{-- Voltagem Nominal --}}
        <div class="mb-3">
            <label for="rated_voltage" class="form-label">Voltagem Nominal (V)</label>
            <input type="number" step="0.01" class="form-control" id="rated_voltage" name="rated_voltage"
                   value="{{ old('rated_voltage', $device->rated_voltage) }}">
        </div>

        {{-- Tipo de Dispositivo --}}
        <div class="mb-3">
            <label for="device_type_id" class="form-label">Tipo de Dispositivo</label>
            <select class="form-select" id="device_type_id" name="device_type_id">
                <option value="">Selecione um tipo</option>
                @foreach($deviceTypes as $type)
                    <option value="{{ $type->id }}" {{ old('device_type_id', $device->device_type_id) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Ambiente --}}
        <div class="mb-3">
            <label for="environment_id" class="form-label">Ambiente</label>
            <select class="form-select" id="environment_id" name="environment_id">
                <option value="">Selecione um ambiente</option>
                @foreach($environments as $env)
                    <option value="{{ $env->id }}" {{ old('environment_id', $device->environment_id) == $env->id ? 'selected' : '' }}>
                        {{ $env->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <a href="{{ route('devices.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
