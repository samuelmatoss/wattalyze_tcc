<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Wattalyze - Monitoramento de Energia')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=SUSE:wght@300&display=swap');

        body {
            font-family: "SUSE", sans-serif;
            background-color: #ecf0f1;
        }

        /* Navbar fixa no topo */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        /* Sidebar fixa na lateral */
        .sidebar {
            position: fixed;
            top: 56px; /* Altura da navbar */
            left: 0;
            height: calc(100vh - 56px);
            width: 240px;
            background: #2c3e50;
            overflow-y: auto;
        }

        /* Ajuste para o conteúdo principal */
        main {
            margin-top: 56px; /* Altura da navbar */
            margin-left: 240px; /* Largura da sidebar */
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .stats-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .alert-card {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .device-card {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .consumption-card {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .status-online {
            color: #28a745;
        }

        .status-offline {
            color: #dc3545;
        }

        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }

        .sidebar .nav-link.active {
            color: #27ae60 !important;
            background-color: rgba(39, 174, 96, 0.15);
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard.home') }}">
                <i class="bi bi-lightning-charge-fill"></i> Wattalyze
            </a>

            <div class="navbar-nav ms-auto">
                <!-- Notificações -->
                <div class="dropdown me-2">
                    <a class="nav-link position-relative" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill" style="font-size: 1rem;"></i>

                        @if($alerts->count() > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $alerts->count() }}
                        </span>
                        @endif
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="alertsDropdown" style="width: 300px;">
                        <li class="dropdown-header fw-bold">Notificações</li>

                        @forelse($alerts as $alert)
                        <li>
                            <a href="{{ route('alerts.show', $alert->id) }}" class="dropdown-item small">
                                <div class="fw-bold text-danger">{{ $alert->title }}</div>
                                <div class="text-muted small">
                                    {{ optional($alert->device)->name ?? 'Sem dispositivo' }}
                                    @if($alert->environment) ({{ $alert->environment->name }}) @endif
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        @empty
                        <li class="dropdown-item text-muted small">Nenhuma notificação</li>
                        @endforelse

                        <li><a class="dropdown-item text-center small text-primary" href="{{ route('settings.notifications') }}">Ver todas</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('settings.profile') }}"><i class="bi bi-person"></i> Perfil</a></li>
                        <hr class="dropdown-divider">
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="btn btn-link dropdown-item"><i class="bi bi-box-arrow-right"></i> Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav id="sidebarMenu" class="sidebar ">
        <div class="pt-3">
            <ul class="nav flex-column">
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" href="{{ route('dashboard.home') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('environments.*') ? 'active' : '' }}" href="{{ route('environments.index') }}">
                        <i class="bi bi-building me-2"></i> Ambientes
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}" href="{{ route('devices.index') }}">
                        <i class="bi bi-cpu me-2"></i> Dispositivos
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}" href="{{ route('alerts.rules') }}">
                        <i class="bi bi-exclamation-triangle me-2"></i> Alertas
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-text me-2"></i> Relatórios
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link {{ request()->routeIs('tariffs.*') ? 'active' : '' }}" href="{{ route('tariffs.index') }}">
                        <i class="bi bi-currency-dollar me-2"></i> Tarifas
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main content -->
    <main class="px-md-4">
        <div class="pt-3 pb-2 mb-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
