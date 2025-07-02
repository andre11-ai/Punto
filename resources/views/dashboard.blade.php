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
        <div class="col-md-4">
            <div class="card card-info card-outline">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas por Día (últimos 7 días)</h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="{{ route('export.ventas.dia', ['formato'=>'excel']) }}" class="dropdown-item">Exportar a Excel</a>
                                    <a href="{{ route('export.ventas.dia', ['formato'=>'pdf']) }}" class="dropdown-item">Exportar a PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <canvas id="ventasPorDia" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas por Semana</h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="{{ route('export.ventas.semana', ['formato'=>'excel']) }}" class="dropdown-item">Exportar a Excel</a>
                                    <a href="{{ route('export.ventas.semana', ['formato'=>'pdf']) }}" class="dropdown-item">Exportar a PDF</a>
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

        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Ventas por Mes</h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="{{ route('export.ventas.mes', ['formato'=>'excel']) }}" class="dropdown-item">Exportar a Excel</a>
                                    <a href="{{ route('export.ventas.mes', ['formato'=>'pdf']) }}" class="dropdown-item">Exportar a PDF</a>
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
                        <div class="btn-group">
                            <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fas fa-wrench"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu">
                                <a href="{{ route('export.productos.vendidos', ['formato'=>'excel']) }}" class="dropdown-item">Exportar a Excel</a>
                                <a href="{{ route('export.productos.vendidos', ['formato'=>'pdf']) }}" class="dropdown-item">Exportar a PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="masVendidosTable" class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Unidades vendidas</th>
                                    <th>Ventas</th>
                                    <th style="width: 40px">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masVendidos as $i => $mv)
                                    <tr>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $mv->producto->producto ?? 'Eliminado' }}</td>
                                        <td>{{ $mv->producto->categoria->nombre ?? '-' }}</td>
                                        <td>{{ $mv->total_vendidos }}</td>
                                        <td>
                                            <div class="progress progress-xs">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ ($mv->total_vendidos / max(1, $masVendidos->max('total_vendidos'))) * 100 }}%">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ round(($mv->total_vendidos / max(1, $masVendidos->max('total_vendidos'))) * 100) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
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
                        @foreach($actividad as $item)
                            <li class="item">
                                <div class="product-img">
                                    @if($item['tipo'] == 'venta')
                                        <i class="fas fa-shopping-cart fa-2x text-success"></i>
                                    @elseif($item['tipo'] == 'cliente')
                                        <i class="fas fa-user-plus fa-2x text-primary"></i>
                                    @elseif($item['tipo'] == 'producto')
                                        <i class="fas fa-box-open fa-2x text-warning"></i>
                                    @endif
                                </div>
                                <div class="product-info">
                                    <a href="javascript:void(0)" class="product-title">
                                        {{ $item['descripcion'] }}
                                        <span class="badge badge-success float-right">{{ $item['badge'] }}</span>
                                    </a>
                                    <span class="product-description">
                                        Realizado por: {{ $item['usuario'] }} - {{ $item['fecha']->diffForHumans() }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
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
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@1.2.1"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        // Exportar tabla a Excel
        function exportTableToExcel(tableID, filename = ''){
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById(tableID);
            var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
            filename = filename?filename+'.xls':'export_data.xls';
            downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);
            if(navigator.msSaveOrOpenBlob){
                var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
                navigator.msSaveOrOpenBlob( blob, filename);
            }else{
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                downloadLink.download = filename;
                downloadLink.click();
            }
        }

        // Exportar tabla a PDF
        function exportTableToPDF(tableID, filename = '') {
            var doc = new jspdf.jsPDF('l', 'pt', 'a4');
            var table = document.getElementById(tableID);
            html2canvas(table).then(function(canvas) {
                var imgData = canvas.toDataURL('image/png');
                var pageWidth = doc.internal.pageSize.getWidth();
                var pageHeight = doc.internal.pageSize.getHeight();
                var imgWidth = pageWidth - 40;
                var imgHeight = canvas.height * imgWidth / canvas.width;
                doc.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                doc.save(filename ? filename + '.pdf' : 'export_data.pdf');
            });
        }

        // Exportar gráfica a PDF
        function exportChartToPDF(canvasId, filename = '') {
            var doc = new jspdf.jsPDF('l', 'pt', 'a4');
            var canvas = document.getElementById(canvasId);
            html2canvas(canvas).then(function(canvasExported) {
                var imgData = canvasExported.toDataURL('image/png');
                var pageWidth = doc.internal.pageSize.getWidth();
                var pageHeight = doc.internal.pageSize.getHeight();
                var imgWidth = pageWidth - 40;
                var imgHeight = canvasExported.height * imgWidth / canvasExported.width;
                doc.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                doc.save(filename ? filename + '.pdf' : 'grafica.pdf');
            });
        }

        // Exportar gráfica a Excel (solo los datos)
        function exportChartToExcel(canvasId, filename = '') {
            let chart;
            if (canvasId === 'ventasPorDia') chart = window.chartDia;
            else if (canvasId === 'ventasPorSemana') chart = window.chartSemana;
            else if (canvasId === 'ventasPorMes') chart = window.chartMes;
            else return;

            if (!chart) return;

            let csv = "Etiqueta,Valor\n";
            chart.data.labels.forEach((label, i) => {
                let value = chart.data.datasets[0].data[i];
                csv += `${label},${value}\n`;
            });

            let blob = new Blob([csv], {type: 'text/csv'});
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.setAttribute('hidden','');
            a.setAttribute('href', url);
            a.setAttribute('download', (filename ? filename : 'grafica') + '.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        $(document).ready(function() {
            // Ventas por día (nueva gráfica)
            var dataDia = {!! json_encode($ventasPorDia) !!};
            var labelsDia = dataDia.map(item => item.fecha);
            var valoresDia = dataDia.map(item => item.total);

            var ctxDia = document.getElementById('ventasPorDia').getContext('2d');
            window.chartDia = new Chart(ctxDia, {
                type: 'line',
                data: {
                    labels: labelsDia,
                    datasets: [{
                        label: 'Ventas por día',
                        data: valoresDia,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false,
                    },
                    plugins: {
                        tooltip: {
                            enabled: true,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return ' $' + context.parsed.y.toLocaleString();
                                }
                            }
                        },
                        zoom: {
                            pan: { enabled: true, mode: 'x' },
                            zoom: { enabled: true, mode: 'x' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Ventas por semana (mejorada)
            var dataSemana = {!! json_encode($ventasPorSemana) !!};
            var labelsSemana = dataSemana.map(item => item.dia);
            var valoresSemana = dataSemana.map(item => item.total);

            var ctxSemana = document.getElementById('ventasPorSemana').getContext('2d');
            window.chartSemana = new Chart(ctxSemana, {
                type: 'line',
                data: {
                    labels: labelsSemana,
                    datasets: [{
                        label: 'Ventas esta semana',
                        data: valoresSemana,
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: '#fff',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false,
                    },
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
                        },
                        zoom: {
                            pan: { enabled: true, mode: 'x' },
                            zoom: { enabled: true, mode: 'x' }
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

            // Ventas por mes (mejorada)
            var dataVenta = @json($ventas);
            if (dataVenta && Object.keys(dataVenta).length > 0) {
                var ctxMes = document.getElementById('ventasPorMes').getContext('2d');
                var years = Object.keys(dataVenta);
                var currentYearData = Object.values(dataVenta[years[years.length - 1]]);
                var previousYearData = years.length > 1 ? Object.values(dataVenta[years.length - 2]) : [];
                window.chartMes = new Chart(ctxMes, {
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
                        responsive: true,
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
                            },
                            zoom: {
                                pan: { enabled: true, mode: 'x' },
                                zoom: { enabled: true, mode: 'x' }
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
