<link rel="stylesheet" href="{{ asset('css/header.css') }}">
<header>

    <nav>
        <a href="#"><img src="{{ asset('images/logo.png') }}" alt="" class="img-001"></a>    
        <div class="nav-links">
        <a href="#" class="c-link">Produtos</a>
        <a href="#" class="c-link">Contato</a>
        <a href="#" class="c-link">Notícias</a>
        <a href="#" class="c-link">Suporte</a>
        <a href="{{ route('login') }}" class="c-link "><button class="btn-l">Login</button></a>
        <a href="{{ route('register') }}" class="c-link "><button class="btn-c">Cadastrar</button></a>
        </div>
    </nav>
</header>