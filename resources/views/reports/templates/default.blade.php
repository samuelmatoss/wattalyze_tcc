<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>{{ $reportData['report_name'] }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=SUSE:wght@300&display=swap');

        body {
            font-family: "SUSE", sans-serif;
            color: #333;
            text-align: center;
            align-items: center;
        }

        header {
            display: flex;
            align-items: center;
        }



        header h1 {
            font-weight: 700;
            color: #28a745;
            /* verde */
            font-size: 3vh;
            margin: 0;
        }

        hr.separator {
            border: none;
            border-top: 0.1vh solid #b0b0b0;
            margin: 2vh 0;
        }

        .filter-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1vh 4vh;
            margin-bottom: 3vh;
            max-width: 50vh;
        }

        .filter-buttons button {
            background: transparent;
            border: 0.1vh solid #6c757d;
            border-radius: 6vh;
            padding: 1vh 2vh;
            font-size: 2vh;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            cursor: default;
            width: 10vw;
            height: 4vw;

        }

        .report-section {

            padding: 1vh 2vh;
            border-radius: 0.4vh;
            margin-bottom: 3vh;
            box-shadow: 0 0 0.6vh rgb(0 0 0 / 0.1);
        }

        .report-section h2 {
            font-weight: 700;
            font-size: 2vh;
            color: #28a745;
            margin-top: 0;
            margin-bottom: 2vh;
        }

        .chart-img {
            width: 100%;
            max-width: 80vw;
            height: 30vh;
            object-fit: contain;
        }

        /* Lista simples para dados detalhados */
        ul.data-list {
            list-style: none;
            padding-left: 0;
            font-size: 13px;
            color: #444;
        }

        ul.data-list li {
            margin-bottom: 0.5vh;
        }
    </style>
</head>

<body>
    @foreach ($reportData['devices'] as $deviceName => $dates)
    <header class="row">
        <h1>RELATÓRIO</h1>
        <div class="filter-buttons">
            <button>{{ $reportData['report_name']}}</button>
        </div>
    </header>


    <hr class="separator" />


    <div class="filter-buttons">
        <button>{{$reportData['period_start']}}</button>
        <button>{{$reportData['period_end']}}</button>
        <div class="filter-buttons">

            <hr class="separator" />


            <div class="report-section">
                <h2>CUSTO</h2>
                <img class="chart-img" src="{{ $reportData['charts'][$deviceName]['cost_chart_base64'] ?? '' }}" alt="Gráfico de Custo">
            </div>

            <div class="report-section">
                <h2>ENERGIA </h2>
                <img class="chart-img" src="{{ $reportData['charts'][$deviceName]['consumption_chart_base64'] ?? '' }}" alt="Gráfico de Energia">
            </div>


            @endforeach

</body>

</html>