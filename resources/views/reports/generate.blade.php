@include('components.headerDash')

<style>
    :root {
        --primary-green: #27ae60;
        --primary-dark: #2c3e50;
        --gray-light: #ecf0f1;
        --gray-medium: #95a5a6;
        --danger-red: #e74c3c;
    }
    .sidebar{
        height: 105vh;
    }
    body {
        background-color: var(--gray-light);
    }

    .container {
        position: relative;
        z-index: 10;
        margin-left: 25vw !important;
    }

    h2 {
        color: var(--primary-dark);
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    label.form-label {
        font-weight: 600;
        color: var(--primary-dark);
    }

    input.form-control,
    select.form-select {
        border-radius: 8px;
        border: 1.5px solid var(--gray-medium);
        transition: border-color 0.3s;
    }

    input.form-control:focus,
    select.form-select:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 8px rgba(39, 174, 96, 0.3);
        outline: none;
    }

    button.btn-primary {
        background-color: var(--primary-green);
        border: none;

    }
</style>

<div class="container col-xl-7">
    <h2>Gerar Relatório</h2>

    <form method="POST" action="{{ route('reports.generate.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nome do Relatório</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <select name="type" class="form-select" required>
                <option value="consumption">Consumo</option>
                <option value="cost">Custo</option>
                <option value="efficiency">Eficiência</option>
                <option value="comparative">Comparativo</option>
                <option value="custom">Personalizado</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo de Período</label>
            <select name="period_type" class="form-select" required>
                <option value="daily">Diário</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensal</option>
                <option value="yearly">Anual</option>
                <option value="custom">Personalizado</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Início</label>
            <input type="date" name="period_start" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Fim</label>
            <input type="date" name="period_end" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Formato</label>
            <select name="format" class="form-select" required>
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Dispositivos</label>
            <select name="devices[]" class="form-select" multiple>
                @foreach($devices as $device)
                <option value="{{ $device->id }}">{{ $device->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Ambientes</label>
            <select name="environments[]" class="form-select" multiple>
                @foreach($environments as $env)
                <option value="{{ $env->id }}">{{ $env->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Gerar Relatório</button>
    </form>
</div>