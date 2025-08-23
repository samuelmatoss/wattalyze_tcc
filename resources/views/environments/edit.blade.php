@include('components.headerDash')

<div class="container col-xl-7" style="margin-left: 30vw;">
    <h1>Editar Ambiente</h1>

    <form action="{{ route('environments.update', $environment->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                class="form-control @error('name') is-invalid @enderror" 
                value="{{ old('name', $environment->name) }}" 
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
            <select 
                name="type" 
                id="type" 
                class="form-select @error('type') is-invalid @enderror" 
                required
            >
                <option value="">-- Selecione --</option>
                <option value="residential" {{ old('type', $environment->type) == 'residential' ? 'selected' : '' }}>Residencial</option>
                <option value="commercial" {{ old('type', $environment->type) == 'commercial' ? 'selected' : '' }}>Comercial</option>
                <option value="industrial" {{ old('type', $environment->type) == 'industrial' ? 'selected' : '' }}>Industrial</option>
                <option value="public" {{ old('type', $environment->type) == 'public' ? 'selected' : '' }}>Público</option>
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Descrição</label>
            <textarea 
                name="description" 
                id="description" 
                class="form-control @error('description') is-invalid @enderror" 
                rows="3"
            >{{ old('description', $environment->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="size_sqm" class="form-label">Tamanho (m²)</label>
                <input 
                    type="number" 
                    step="0.01" 
                    min="0" 
                    name="size_sqm" 
                    id="size_sqm" 
                    class="form-control @error('size_sqm') is-invalid @enderror" 
                    value="{{ old('size_sqm', $environment->size_sqm) }}"
                >
                @error('size_sqm')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="occupancy" class="form-label">Ocupação (pessoas)</label>
                <input 
                    type="number" 
                    min="0" 
                    name="occupancy" 
                    id="occupancy" 
                    class="form-control @error('occupancy') is-invalid @enderror" 
                    value="{{ old('occupancy', $environment->occupancy) }}"
                >
                @error('occupancy')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="voltage_standard" class="form-label">Padrão de Voltagem</label>
                <input 
                    type="text" 
                    name="voltage_standard" 
                    id="voltage_standard" 
                    class="form-control @error('voltage_standard') is-invalid @enderror" 
                    value="{{ old('voltage_standard', $environment->voltage_standard) }}"
                >
                @error('voltage_standard')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="tariff_type" class="form-label">Tipo de Tarifa</label>
            <input 
                type="text" 
                name="tariff_type" 
                id="tariff_type" 
                class="form-control @error('tariff_type') is-invalid @enderror" 
                value="{{ old('tariff_type', $environment->tariff_type) }}"
            >
            @error('tariff_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="energy_provider" class="form-label">Fornecedor de Energia</label>
            <input 
                type="text" 
                name="energy_provider" 
                id="energy_provider" 
                class="form-control @error('energy_provider') is-invalid @enderror" 
                value="{{ old('energy_provider', $environment->energy_provider) }}"
            >
            @error('energy_provider')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="installation_date" class="form-label">Data de Instalação</label>
            <input 
                type="date" 
                name="installation_date" 
                id="installation_date" 
                class="form-control @error('installation_date') is-invalid @enderror" 
                value="{{ old('installation_date', optional($environment->installation_date)->format('Y-m-d')) }}"
            >
            @error('installation_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-4">
            <input 
                class="form-check-input" 
                type="checkbox" 
                id="is_default" 
                name="is_default" 
                value="1" 
                {{ old('is_default', $environment->is_default) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="is_default">
                Definir como ambiente padrão
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="{{ route('environments.show', $environment->id) }}" class="btn btn-secondary ms-2">Cancelar</a>
    </form>
</div>
