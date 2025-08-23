
@include('components.headerDash')
<div class="container py-4" style="margin-left: 20vw;">
    <h1>Editar Tarifa: {{ $tariff->name }}</h1>

    <form action="{{ route('tariffs.update', $tariff) }}" method="POST">
        @csrf
        @method('PUT')

        @include('tariffs.partials.form', ['tariff' => $tariff])

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('tariffs.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

