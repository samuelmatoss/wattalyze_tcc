@include('components.headerDash')

<style>
    /* Variáveis para consistência com o dashboard */
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
        margin-left: 10vw;
    }

    .modern-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .modern-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-medium);
    }

    .modern-title {
        font-size: 1.4rem;
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
        width: 40px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-green), var(--primary-dark));
        border-radius: 2px;
    }

    .btn-modern {
        border-radius: 12px;
        padding: 6px 14px;
        transition: var(--transition);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-light);
    }

    .table-modern {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-light);
        background: white;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern th, .table-modern td {
        border: none;
        vertical-align: middle;
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

    .badge-completed { background: var(--primary-green); }
    .badge-pending { background: #f39c12; }
    .badge-failed { background: var(--primary-red); }

</style>

<div class="container col-xl-8 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="modern-title">Meus Relatórios</h2>
        <a href="{{ route('reports.generate') }}" class="btn btn-outline-primary btn-modern">Criar Relatórios</a>
    </div>

    @if(session('success'))
        <div class="modern-card p-3 mb-4 text-success">
            {{ session('success') }}
        </div>
    @endif

    @if($reports->isEmpty())
        <div class="modern-card p-4 text-center text-muted">
            Nenhum relatório encontrado.
        </div>
    @else
        <div class="table-responsive modern-card p-3">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Status</th>
                        <th>Período</th>
                        <th>Formato</th>
                        <th>Arquivo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    @php
                        $statusClass = match($report->status) {
                            'completed' => 'badge-completed',
                            'pending' => 'badge-pending',
                            'failed' => 'badge-failed',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <tr>
                        <td>{{ $report->name }}</td>
                        <td><span class="badge-status {{ $statusClass }}">{{ ucfirst($report->status) }}</span></td>
                        <td>{{ $report->period_start->format('d/m/Y') }} - {{ $report->period_end->format('d/m/Y') }}</td>
                        <td>{{ strtoupper($report->format) }}</td>
                        <td>
                            @if($report->status === 'completed')
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-success btn-modern">Baixar</a>
                            @else
                                <span class="text-muted">Indisponível</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Excluir relatório?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger btn-modern">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
