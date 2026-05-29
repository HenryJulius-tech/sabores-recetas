document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('financeChart');
    const filterSelect = document.getElementById('chart-filter');
    
    if (!ctx) return;
    
    let financeChart;
    
    const fetchChartData = (filterValue) => {
        fetch(`api_finance.php?filter=${filterValue}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener datos del servidor');
                }
                return response.json();
            })
            .then(data => {
                renderChart(data.labels, data.ingresos, data.gastos);
            })
            .catch(error => {
                console.error(error);
                if (window.showToast) {
                    window.showToast('No se pudieron cargar los datos financieros para el gráfico.', 'error');
                }
            });
    };

    const renderChart = (labels, ingresos, gastos) => {
        // Si el gráfico ya existe, lo destruimos para volverlo a dibujar
        if (financeChart) {
            financeChart.destroy();
        }

        financeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingresos ($)',
                        data: ingresos,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)', // Verde Esmeralda
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 32,
                    },
                    {
                        label: 'Gastos ($)',
                        data: gastos,
                        backgroundColor: 'rgba(239, 68, 68, 0.75)', // Rojo Coral
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        maxBarThickness: 32,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Inter',
                                size: 12,
                                weight: '500'
                            },
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', // Slate 900
                        titleFont: {
                            family: 'Inter',
                            size: 13,
                            weight: '700'
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)', // Bordes muy suaves
                        },
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    };

    // Escuchar cambios de filtro
    if (filterSelect) {
        filterSelect.addEventListener('change', (e) => {
            fetchChartData(e.target.value);
        });
    }

    // Carga inicial (Mensual por defecto)
    fetchChartData('mensual');
});
