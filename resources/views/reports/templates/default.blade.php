<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportData['report_name'] }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
            color: #2D3748;
            line-height: 1.6;
            background: #F7FAFC;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Section */
        .header {
            background: #2E8B57 ;
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(46, 139, 87, 0.2);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }

        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
            flex: 1;
            min-width: 200px;
            text-align: center;
        }

        .info-card h3 {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .info-card p {
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #2E8B57;

        }


        .summary-card h3 {
            color: #2E8B57;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2D3748;
            display: block;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #718096;
            font-weight: 500;
            margin-top: 5px;
        }

        /* Device Sections */
        .device-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            overflow: hidden;
        }

        .device-header {
            background:  #2D3748 ;
            color: white;
            padding: 25px 30px;
        }

        .device-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .device-header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .chart-container {
            background: #F8F9FA;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #E2E8F0;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2D3748;
            margin-bottom: 15px;
            text-align: center;
        }

        .chart-img {
            width: 100%;
            height: auto;
            max-height: 300px;
            object-fit: contain;
            border-radius: 6px;
        }


        .footer {
            text-align: center;
            padding: 30px 0;
            border-top: 1px solid #E2E8F0;
            color: #718096;
            font-size: 0.9rem;
            margin-top: 40px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .header-info {
                flex-direction: column;
            }
            
            .summary-stats {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            
            .container {
                padding: 0;
            }
            
            .device-section {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            
            .summary-grid {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>{{ $reportData['report_name'] }}</h1>
            <div class="header-info">
                <div class="info-card">
                    <h3>Período</h3>
                    <p>{{ $reportData['period_start'] }} - {{ $reportData['period_end'] }}</p>
                </div>
                <div class="info-card">
                    <h3>Dispositivos</h3>
                    <p>{{ $reportData['total_devices'] ?? count($reportData['devices']) }}</p>
                </div>
                <div class="info-card">
                    <h3>Gerado em</h3>
                    <p>{{ $reportData['generated_at'] ?? now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </header>

        <!-- Summary Cards -->
        @if(isset($reportData['summary']))
        <div class="summary-grid">
            @foreach($reportData['summary'] as $deviceName => $summary)
            <div class="summary-card">
                <h3>{{ $deviceName }} - Resumo</h3>
                <div class="summary-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $summary['total_consumption'] }}</span>
                        <span class="stat-label">kWh Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">R$ {{ $summary['total_cost'] }}</span>
                        <span class="stat-label">Custo Total</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $summary['avg_consumption'] }}</span>
                        <span class="stat-label">Média kWh/dia</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">R$ {{ $summary['avg_cost'] }}</span>
                        <span class="stat-label">Custo Médio/dia</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Device Charts -->
        @foreach($reportData['devices'] as $deviceName => $dates)
        <div class="device-section">
            <div class="device-header">
                <h2>{{ $deviceName }}</h2>
                <p>Análise detalhada de consumo e custos energéticos</p>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3 class="chart-title">Consumo de Energia</h3>
                    <img class="chart-img" 
                         src="{{ $reportData['charts'][$deviceName]['consumption_chart_base64'] ?? '' }}" 
                         alt="Gráfico de Consumo - {{ $deviceName }}"
                         loading="lazy">
                </div>
                
                <div class="chart-container">
                    <h3 class="chart-title">Custo Energético</h3>
                    <img class="chart-img" 
                         src="{{ $reportData['charts'][$deviceName]['cost_chart_base64'] ?? '' }}" 
                         alt="Gráfico de Custo - {{ $deviceName }}"
                         loading="lazy">
                </div>
            </div>
        </div>
        @endforeach

        <!-- Footer -->
        <footer class="footer">
            <p>Relatório gerado automaticamente pelo Sistema de Monitoramento Energético</p>
            <p>© {{ date('Y') }} - Todos os direitos reservados</p>
        </footer>
    </div>
</body>
</html>