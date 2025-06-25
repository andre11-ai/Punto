@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Panel de Control</h1>
        <small class="text-muted">Última actualización: {{ now()->format('d/m/Y H:i') }}</small>
    </div>
@stop

@section('content')
    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm bg-gradient-success">
                <span class="info-box-icon"><i class="fas fa-users fa-2x"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Clientes Deudores</span>
                    <span class="info-box-number">{{ $totales['clients'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 70%"></div>
                    </div>
                    <span class="progress-description">
                        +12% este mes
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm bg-gradient-primary">
                <span class="info-box-icon"><i class="fas fa-list fa-2x"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Productos</span>
                    <span class="info-box-number">{{ $totales['products'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 50%"></div>
                    </div>
                    <span class="progress-description">
                        +5% este mes
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm bg-gradient-warning">
                <span class="info-box-icon"><i class="fas fa-tags fa-2x"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Categorías</span>
                    <span class="info-box-number">{{ $totales['categories'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 30%"></div>
                    </div>
                    <span class="progress-description">
                        +2% este mes
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-sm bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-truck fa-2x"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Proveedores</span>
                    <span class="info-box-number">{{ $totales['proveedores'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 40%"></div>
                    </div>
                    <span class="progress-description">
                        +8% este mes
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos principales -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas por Semana</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="dropdown-item">Exportar</a>
                                    <a href="#" class="dropdown-item">Configuración</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="ventasPorSemana" height="300"></canvas>
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                            <i class="fas fa-square text-primary"></i> Este año
                        </span>
                        <span>
                            <i class="fas fa-square text-gray"></i> Año pasado
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas por Mes</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="dropdown-item">Exportar</a>
                                    <a href="#" class="dropdown-item">Configuración</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="ventasPorMes" height="300"></canvas>
                    </div>
                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                            <i class="fas fa-square text-success"></i> Este año
                        </span>
                        <span>
                            <i class="fas fa-square text-gray"></i> Año pasado
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección adicional -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-gradient-dark">
                    <h3 class="card-title">Productos más vendidos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Ventas</th>
                                    <th style="width: 40px">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>Producto Ejemplo 1</td>
                                    <td>Categoría A</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-success" style="width: 90%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">90%</span></td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Producto Ejemplo 2</td>
                                    <td>Categoría B</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-warning" style="width: 70%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning">70%</span></td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Producto Ejemplo 3</td>
                                    <td>Categoría C</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-danger" style="width: 50%"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">50%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-gradient-info">
                    <h3 class="card-title">Actividad Reciente</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-shopping-cart fa-2x text-success"></i>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Nueva venta
                                    <span class="badge badge-success float-right">$1,200</span></a>
                                <span class="product-description">
                                    Realizada por: Admin - Hace 10 minutos
                                </span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-user-plus fa-2x text-primary"></i>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Nuevo cliente
                                    <span class="badge badge-primary float-right">Empresa XYZ</span></a>
                                <span class="product-description">
                                    Registrado por: Vendedor1 - Hace 1 hora
                                </span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-box-open fa-2x text-warning"></i>
                            </div>
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">Nuevo producto
                                    <span class="badge badge-warning float-right">SKU-1001</span></a>
                                <span class="product-description">
                                    Agregado por: Inventario - Hace 2 horas
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="javascript:void(0)" class="uppercase">Ver toda la actividad</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box {
            border-radius: 0.25rem;
            transition: all 0.3s ease-in-out;
        }
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .info-box-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        .card-outline {
            border-top: 3px solid !important;
        }
        .bg-gradient-primary {
            background: linear-gradient(to right, #4e73df, #224abe) !important;
        }
        .bg-gradient-success {
            background: linear-gradient(to right, #1cc88a, #13855c) !important;
        }
        .bg-gradient-info {
            background: linear-gradient(to right, #36b9cc, #258391) !important;
        }
        .bg-gradient-warning {
            background: linear-gradient(to right, #f6c23e, #dda20a) !important;
        }
        .bg-gradient-dark {
            background: linear-gradient(to right, #5a5c69, #3a3b45) !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Ventas por semana
            var ctxSemana = document.getElementById('ventasPorSemana').getContext('2d');
            var dataSemana = {!! json_encode($ventasPorSemana) !!};

            var labelsSemana = dataSemana.map(item => item.dia);
            var valoresSemana = dataSemana.map(item => item.total);

            var chartSemana = new Chart(ctxSemana, {
                type: 'line',
                data: {
                    labels: labelsSemana,
                    datasets: [{
                        label: 'Ventas esta semana',
                        data: valoresSemana,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            titleMarginBottom: 10,
                            titleFontColor: '#6e707e',
                            titleFontSize: 14,
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: {
                                label: function(context) {
                                    return 'Ventas: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    }
                }
            });

            // Ventas por mes
            var dataVenta = @json($ventas);
            if (dataVenta && Object.keys(dataVenta).length > 0) {
                var ctxMes = document.getElementById('ventasPorMes').getContext('2d');

                var years = Object.keys(dataVenta);
                var currentYearData = Object.values(dataVenta[years[years.length - 1]]);
                var previousYearData = years.length > 1 ? Object.values(dataVenta[years[years.length - 2]]) : [];

                var chartMes = new Chart(ctxMes, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(dataVenta[Object.keys(dataVenta)[0]]),
                        datasets: [
                            {
                                label: years[years.length - 1],
                                data: currentYearData,
                                backgroundColor: 'rgba(28, 200, 138, 0.8)',
                                borderColor: 'rgba(28, 200, 138, 1)',
                                borderWidth: 1
                            },
                            {
                                label: years.length > 1 ? years[years.length - 2] : 'Año anterior',
                                data: previousYearData,
                                backgroundColor: 'rgba(220, 220, 220, 0.8)',
                                borderColor: 'rgba(220, 220, 220, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                titleMarginBottom: 10,
                                titleFontColor: '#6e707e',
                                titleFontSize: 14,
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                intersect: false,
                                mode: 'index',
                                caretPadding: 10,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: "rgb(234, 236, 244)",
                                    zeroLineColor: "rgb(234, 236, 244)",
                                    drawBorder: false,
                                    borderDash: [2],
                                    zeroLineBorderDash: [2]
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@stop
