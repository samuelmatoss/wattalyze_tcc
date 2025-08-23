@include('components.headerDash')
<style>



</style>
<div class="container py-4" style="margin-left: 20vw;">
    <h1>Criar Nova Tarifa</h1>

    <form action="{{ route('tariffs.store') }}" method="POST">
        @csrf

        @include('tariffs.partials.form')
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('tariffs.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>