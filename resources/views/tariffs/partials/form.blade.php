<div class="mb-3">
    <label for="name" class="form-label">Nome *</label>
    <input type="text" name="name" id="name" value="{{ old('name', $tariff->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="provider" class="form-label">Provedor</label>
    <input type="text" name="provider" id="provider" value="{{ old('provider', $tariff->provider ?? '') }}" class="form-control @error('provider') is-invalid @enderror">
    @error('provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="region" class="form-label">Região</label>
    <input type="text" name="region" id="region" value="{{ old('region', $tariff->region ?? '') }}" class="form-control @error('region') is-invalid @enderror">
    @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="tariff_type" class="form-label">Tipo da Tarifa</label>
    <input type="text" name="tariff_type" id="tariff_type" value="{{ old('tariff_type', $tariff->tariff_type ?? '') }}" class="form-control @error('tariff_type') is-invalid @enderror">
    @error('tariff_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr>

<h5>Faixas de Consumo (kWh)</h5>

<div class="mb-3">
    <label for="bracket1_min" class="form-label">Faixa 1 - Mínimo *</label>
    <input type="number" step="0.01" name="bracket1_min" id="bracket1_min" value="{{ old('bracket1_min', $tariff->bracket1_min ?? '') }}" class="form-control @error('bracket1_min') is-invalid @enderror" required>
    @error('bracket1_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket1_max" class="form-label">Faixa 1 - Máximo</label>
    <input type="number" step="0.01" name="bracket1_max" id="bracket1_max" value="{{ old('bracket1_max', $tariff->bracket1_max ?? '') }}" class="form-control @error('bracket1_max') is-invalid @enderror">
    @error('bracket1_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket1_rate" class="form-label">Faixa 1 - Valor por kWh (R$) *</label>
    <input type="number" step="0.0001" name="bracket1_rate" id="bracket1_rate" value="{{ old('bracket1_rate', $tariff->bracket1_rate ?? '') }}" class="form-control @error('bracket1_rate') is-invalid @enderror" required>
    @error('bracket1_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr>

<div class="mb-3">
    <label for="bracket2_min" class="form-label">Faixa 2 - Mínimo</label>
    <input type="number" step="0.01" name="bracket2_min" id="bracket2_min" value="{{ old('bracket2_min', $tariff->bracket2_min ?? '') }}" class="form-control @error('bracket2_min') is-invalid @enderror">
    @error('bracket2_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket2_max" class="form-label">Faixa 2 - Máximo</label>
    <input type="number" step="0.01" name="bracket2_max" id="bracket2_max" value="{{ old('bracket2_max', $tariff->bracket2_max ?? '') }}" class="form-control @error('bracket2_max') is-invalid @enderror">
    @error('bracket2_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket2_rate" class="form-label">Faixa 2 - Valor por kWh (R$)</label>
    <input type="number" step="0.0001" name="bracket2_rate" id="bracket2_rate" value="{{ old('bracket2_rate', $tariff->bracket2_rate ?? '') }}" class="form-control @error('bracket2_rate') is-invalid @enderror">
    @error('bracket2_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr>

<div class="mb-3">
    <label for="bracket3_min" class="form-label">Faixa 3 - Mínimo</label>
    <input type="number" step="0.01" name="bracket3_min" id="bracket3_min" value="{{ old('bracket3_min', $tariff->bracket3_min ?? '') }}" class="form-control @error('bracket3_min') is-invalid @enderror">
    @error('bracket3_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket3_max" class="form-label">Faixa 3 - Máximo</label>
    <input type="number" step="0.01" name="bracket3_max" id="bracket3_max" value="{{ old('bracket3_max', $tariff->bracket3_max ?? '') }}" class="form-control @error('bracket3_max') is-invalid @enderror">
    @error('bracket3_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="bracket3_rate" class="form-label">Faixa 3 - Valor por kWh (R$)</label>
    <input type="number" step="0.0001" name="bracket3_rate" id="bracket3_rate" value="{{ old('bracket3_rate', $tariff->bracket3_rate ?? '') }}" class="form-control @error('bracket3_rate') is-invalid @enderror">
    @error('bracket3_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<hr>

<div class="mb-3">
    <label for="tax_rate" class="form-label">Taxa (%)</label>
    <input type="number" step="0.0001" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $tariff->tax_rate ?? '') }}" class="form-control @error('tax_rate') is-invalid @enderror">
    @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="valid_from" class="form-label">Válido de</label>
    <input type="date" name="valid_from" id="valid_from" value="{{ old('valid_from', isset($tariff->valid_from) ? $tariff->valid_from->format('Y-m-d') : '') }}" class="form-control @error('valid_from') is-invalid @enderror">
    @error('valid_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="valid_until" class="form-label">Válido até</label>
    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', isset($tariff->valid_until) ? $tariff->valid_until->format('Y-m-d') : '') }}" class="form-control @error('valid_until') is-invalid @enderror">
    @error('valid_until')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $tariff->is_active ?? true) ? 'checked' : '' }}>

    <label for="is_active" class="form-check-label">Ativa</label>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif