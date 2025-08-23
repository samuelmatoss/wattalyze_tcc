    document.addEventListener('DOMContentLoaded', function() {
        // Dados do PHP


        // Configuração padrão dos gráficos
        Chart.defaults.font.size = 10;
        Chart.defaults.font.family = '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif';

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255,255,255,0.2)',
                    borderWidth: 1,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `${context.parsed.y.toFixed(2)} kWh`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        font: {
                            size: 9
                        }
                    }
                },
                y: {
                    display: false,
                    grid: {
                        display: false
                    },
                    beginAtZero: true
                }
            },
            elements: {
                point: {
                    radius: 3,
                    hoverRadius: 5,
                    borderWidth: 2
                },
                line: {
                    borderWidth: 2,
                    tension: 0.4
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        };

        // Cores baseadas no status
        const statusColors = {
            online: {
                border: '#198754',
                background: 'rgba(25, 135, 84, 0.1)',
                point: '#198754'
            },
            offline: {
                border: '#dc3545',
                background: 'rgba(220, 53, 69, 0.1)',
                point: '#dc3545'
            },
            maintenance: {
                border: '#ffc107',
                background: 'rgba(255, 193, 7, 0.1)',
                point: '#ffc107'
            }
        };

        // Função para criar gráfico
        function createChart(deviceId, data, status) {
            const canvas = document.getElementById(`dailyChart-${deviceId}`);
            const noDataDiv = document.getElementById(`no-data-${deviceId}`);

            if (!canvas) {
                console.warn(`Canvas não encontrado: dailyChart-${deviceId}`);
                return;
            }

            // Verificar se há dados válidos
            if (!data || !Array.isArray(data) || data.length === 0) {
                console.log(`Sem dados para dispositivo ${deviceId}`);
                canvas.style.display = 'none';
                if (noDataDiv) noDataDiv.style.display = 'block';
                return;
            }

            // Ocultar mensagem de "sem dados"
            if (noDataDiv) noDataDiv.style.display = 'none';
            canvas.style.display = 'block';

            const colors = statusColors[status] || statusColors.offline;

            // Preparar dados
            const labels = [];
            const values = [];

            data.forEach(item => {
                // Processar data
                let dateLabel = '';
                if (item.date) {
                    const [year, month, day] = item.date.split('-');
                    const date = new Date(year, month - 1, day); // ano, mês (0-11), dia
                    dateLabel = date.toLocaleDateString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit'
                    });

                }

                labels.push(dateLabel);
                values.push(parseFloat(item.value) || 0);
            });

            console.log(`Gráfico ${deviceId} - Labels:`, labels, 'Values:', values);

            const ctx = canvas.getContext('2d');

            // Destruir gráfico existente se houver
            const existingChart = Chart.getChart(ctx);
            if (existingChart) {
                existingChart.destroy();
            }

            // Criar novo gráfico
            try {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            borderColor: colors.border,
                            backgroundColor: colors.background,
                            pointBackgroundColor: colors.point,
                            pointBorderColor: colors.border,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: chartOptions
                });

                console.log(`Gráfico criado com sucesso para dispositivo ${deviceId}`);

            } catch (error) {
                console.error(`Erro ao criar gráfico ${deviceId}:`, error);
                canvas.style.display = 'none';
                if (noDataDiv) noDataDiv.style.display = 'block';
            }
        }

        // Inicializar todos os gráficos
        function initCharts() {
            const deviceCards = document.querySelectorAll('.device-card');
            console.log(`Iniciando gráficos para ${deviceCards.length} dispositivos`);

            deviceCards.forEach((card, index) => {
                const status = card.getAttribute('data-status') || 'offline';
                const canvas = card.querySelector('canvas[id^="dailyChart-"]');

                if (!canvas) {
                    console.warn(`Canvas não encontrado no card ${index}`);
                    return;
                }

                const deviceId = canvas.id.replace('dailyChart-', '');
                const deviceData = dailyConsumptionData[deviceId] || [];

                console.log(`Processando dispositivo ${deviceId}:`, deviceData);

                createChart(deviceId, deviceData, status);
            });
        }

        // Filtros
        function applyFilters() {
            const searchTerm = (document.getElementById('searchDevices')?.value || '').toLowerCase();
            const environmentFilter = document.getElementById('filterEnvironment')?.value || '';
            const typeFilter = document.getElementById('filterType')?.value || '';

            document.querySelectorAll('.device-card').forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const environment = card.getAttribute('data-environment') || '';
                const type = card.getAttribute('data-type') || '';

                const matchSearch = name.includes(searchTerm);
                const matchEnv = !environmentFilter || environment === environmentFilter;
                const matchType = !typeFilter || type === typeFilter;

                card.style.display = (matchSearch && matchEnv && matchType) ? 'block' : 'none';
            });
        }

        function filterByStatus(status) {
            document.querySelectorAll('.device-card').forEach(card => {
                const cardStatus = card.getAttribute('data-status');
                card.style.display = (status === 'all' || cardStatus === status) ? 'block' : 'none';
            });

            // Atualizar botões
            document.querySelectorAll('.btn-toolbar .btn').forEach(btn => btn.classList.remove('active'));
            const buttonId = status === 'all' ? 'filterAll' : `filter${status.charAt(0).toUpperCase() + status.slice(1)}`;
            document.getElementById(buttonId)?.classList.add('active');
        }

        // Event Listeners
        document.getElementById('searchDevices')?.addEventListener('input', applyFilters);
        document.getElementById('filterEnvironment')?.addEventListener('change', applyFilters);
        document.getElementById('filterType')?.addEventListener('change', applyFilters);

        document.getElementById('filterOnline')?.addEventListener('click', () => filterByStatus('online'));
        document.getElementById('filterOffline')?.addEventListener('click', () => filterByStatus('offline'));
        document.getElementById('filterAll')?.addEventListener('click', () => filterByStatus('all'));

        document.getElementById('resetFilters')?.addEventListener('click', () => {
            ['searchDevices', 'filterEnvironment', 'filterType'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            applyFilters();
            filterByStatus('all');
        });

        // Animações nos cards
        document.querySelectorAll('.device-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                this.style.transition = 'all 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.08)';
            });
        });

        // Inicializar
        console.log('Inicializando dashboard...');
        initCharts();
    });