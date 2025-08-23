@include('components.headerDash')
<div class="container-fluid py-4" >
    <div class="row">
        <main class="col-md-10 ms-sm-auto px-md-4">

            <h1 class="mb-4" style="color: var(--bs-dark)">Perfil do Usuário</h1>

            {{-- Dados Cadastrais --}}
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-6">
                    <div class="card text-white shadow-sm" style="background-color: #2c3e50;">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2">Nome</h6>
                            <h4>{{ $user->name }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-6">
                    <div class="card text-white shadow-sm" style="background-color: #27ae60;">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2">E-mail</h6>
                            <h5>{{ $user->email }}</h5>
                        </div>
                    </div>
                </div>

            </div>


            {{-- Alterar Senha --}}
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h5 class="mb-0">Alterar Senha</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('settings.security.update') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha Atual</label>
                            <input type="password" name="current_password" id="current_password" class="form-control" required>
                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirme a Nova Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">Atualizar Senha</button>
                    </form>
                </div>
            </div>

        </main>
    </div>
</div>

