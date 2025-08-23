<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Wattalyze - A Ponte</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">

</head>

<body>
    @include('components.header')
    <section class="hero">
        <div class="div-1">
            <h1>A Ponte Wattalyze</h1>
            <p class="div-1-p">
                Mais poderoso do que seu gateway normal. Preencha a lacuna entre seus equipamentos e a tecnologia IoT.
                Colete dados localmente ou por meio de sua rede de nuvem privada.
            </p>
            <button class="div-1-btnP">Nossos Produtos</button>
            <button class="div-1-btnS">Saiba mais</button>
            <img src="{{ asset('images/wa1.png') }}" alt="Imagem da Ponte Wattalyze" class="div-1-img">
        </div>

    </section>
    <section class="section-2">
        <h2>Mais poderoso do que seu gateway normal</h2>
        <h4>Colete dados localmente ou por meio de sua rede de nuvem privada.
        </h4>
        <p>No gerenciamento inteligente de consumo e automação, um gateway comum não é suficiente. Nossa solução oferece uma tecnologia mais avançada,
            capaz de conectar dispositivos, processar dados e otimizar o uso de energia com eficiência e segurança
        </p>
        <img src="{{ asset('images/wa3.png') }}" alt="">

    </section>
    <h2 class="h1-features">Comprometidos com você</h2>
    <section class="features">

        <div class="feature-box">
            <img src="{{ asset('images/icons/cabo.png') }}" class="icon" alt="">
            <h3>Instalação rápida e fácil</h3>

        </div>
        <div class="feature-box">
            <img src="{{ asset('images/icons/headset.png') }}" class="icon" alt="">
            <h3>Suporte técnico dedicado</h3>

        </div>
        <div class="feature-box">
            <img src="{{ asset('images/icons/ondas.png') }}" class="icon" alt="">
            <h3>Atualizações pelo ar</h3>

        </div>
        <div class="feature-box">
            <img src="{{ asset('images/icons/brasil.png') }}" class="icon" alt="">
            <h3>Feito no Brasil</h3>
        </div>
    </section>


    <section class="faq" >
        <h2 class="h1-faq">Perguntas Frequentes</h2>
        <div class="faq-box">
            <h3>Como funciona um equipamento IoT para controle de energia?</h3>
            <p>O dispositivo se conecta aos eletrodomésticos e mede o consumo de energia em tempo
                real. Ele transmite essas informações para um aplicativo, permitindo que o usuário...</p>
        </div>
        <hr>
        <div class="faq-box">
            <h3>Como posso acessar os dados de consumo de energia?</h3>
            <p>Os dados são acessíveis por meio de um aplicativo móvel ou plataforma web, onde o
                usuário pode visualizar gráficos detalhados, receber alertas...</p>
        </div>
        <hr>
        <div class="faq-box">
            <h3>Preciso de um profissional para instalar o equipamento IoT?</h3>
            <p>Depende do modelo. Alguns dispositivos são plug and play e podem ser instalados
                facilmente pelo usuário, enquanto outros podem exigir instalação...</p>
        </div>
    </section>
    
</body>
@include('components.footer')
</html>