@include('components.headerDash')

<style>
    :root {
        --primary-dark: #2c3e50;
        --primary-green: #27ae60;
        --primary-red: #e74c3c;
        --bg-light: #f8f9fa;
        --border-radius: 16px;
        --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.08);
        --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.12);
        --transition: all 0.3s ease;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .modern-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .modern-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
    }

    .modern-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
        position: relative;
    }

    .modern-title::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-dark));
        border-radius: 2px;
    }

    .btn-modern {
        border-radius: 12px;
        transition: var(--transition);
    }

    .btn-modern:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-light);
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-light);
        background: white;
    }

    .table-modern th, .table-modern td {
        border: none;
        vertical-align: middle;
        padding: 12px 15px;
    }

    .table-modern th {
        background: #f1f3f5;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .table-modern tbody tr {
        transition: var(--transition);
    }

    .table-modern tbody tr:hover {
        background: rgba(39, 174, 96, 0.05);
    }

    .badge-status {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
    }

    .badge-active { background: var(--primary-green); }
    .badge-inactive { background: #95a5a6; }
</style>

<div class="container py-4" style="margin-left:24vw;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="modern-title">Tarifas</h1>
        <a href="{{ route('tariffs.create') }}" class="btn btn-success btn-modern">Nova Tarifa</a>
    </div>

    @if(session('success'))
        <div class="modern-card text-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($tariffs->count())
        <div class="modern-card p-0">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Provedor</th>
                            <th>Tipo</th>
                            <th>Ativa</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tariffs as $tariff)
                        <tr>
                            <td>{{ $tariff->name }}</td>
                            <td>{{ $tariff->provider ?? '-' }}</td>
                            <td>{{ $tariff->tariff_type ?? '-' }}</td>
                            <td>
                                <span class="badge-status {{ $tariff->is_active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $tariff->is_active ? 'Sim' : 'Não' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('tariffs.edit', $tariff) }}" class="btn btn-primary btn-sm btn-modern">Editar</a>
                                <form action="{{ route('tariffs.destroy', $tariff) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirma exclusão?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm btn-modern" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $tariffs->links() }}
        </div>
    @else
        <div class="modern-card text-center text-muted">
            Nenhuma tarifa cadastrada.
        </div>
    @endif
</div>
