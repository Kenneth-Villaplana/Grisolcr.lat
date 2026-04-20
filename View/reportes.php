<?php
session_start();
require_once __DIR__ . '/seguridad.php';
include('layout.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>

    <!-- Chart.js para los dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

</head>

<body>
    <?php MostrarMenu(); ?>

    <main class="container py-5 dashboard-page">
        <h2 class="text-center mb-5 dashboard-title-main">Reportes</h2>
        <!-- 
    Filtros (por ahora visuales, listos para conectar a futuro)
    <form class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="tipoReporte" class="form-label">Tipos de Reporte</label>
            <select id="tipoReporte" class="form-select">
                <option value="todos" selected>Todos</option>
                <option value="ventas">Ventas</option>
                <option value="pacientes">Pacientes</option>
                <option value="inventario">Inventario</option>
            </select>
        </div>

        <div class="col-md-4">
            <label for="fechaInicio" class="form-label">Desde</label>
            <input type="date" id="fechaInicio" class="form-control">
        </div>

        <div class="col-md-4">
            <label for="fechaFin" class="form-label">Hasta</label>
            <input type="date" id="fechaFin" class="form-control">
        </div>
    </form>

    TARJETAS DE REPORTES PDF (legacy, como ya las tenías) 
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">

        <div class="col">
            <div class="card h-100 border-success">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>Ventas Mensuales
                    </h5>
                    <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
                    <p class="card-text text-muted">Por: Chase Gonzalez</p>
                    <a href="reportes/REP-0001.pdf" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-download"></i> Descargar Reporte
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>Pacientes Atendidos
                    </h5>
                    <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
                    <p class="card-text text-muted">Por: Chase Gonzalez</p>
                    <a href="reportes/REP-0011.pdf" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-download"></i> Descargar Reporte
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 border-secondary">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>Inventario
                    </h5>
                    <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
                    <p class="card-text text-muted">Por: Administrador</p>
                    <button class="btn btn-outline-secondary w-100" disabled>
                        <i class="bi bi-clock-history"></i> En proceso
                    </button>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 border-info">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>Ingresos Diarios
                    </h5>
                    <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
                    <p class="card-text text-muted">Por: Maria López</p>
                    <a href="reportes/REP-0020.pdf" target="_blank" class="btn btn-outline-success w-100">
                        <i class="bi bi-download"></i> Descargar Reporte
                    </a>
                </div>
            </div>
        </div>

    </div>
-->
        <!-- DASHBOARD INTERACTIVO (nuevo) -->
        <section class="dashboard-shell">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 dashboard-header">
                <div>
                    <h3 class="mb-1 dashboard-heading">Dashboard de Ventas</h3>
                    <small class="dashboard-subtitle">Visualización moderna en tiempo real</small>
                </div>

                <button id="btnReloadDash" class="btn dashboard-btn mt-2">
                    <i class="bi bi-arrow-clockwise"></i> Recargar
                </button>
            </div>

            <!-- KPIs -->
            <div class="row g-4 align-items-stretch mb-4">

                <!-- KPI redondo 1 -->
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="kpi-circle-wrap">
                        <div class="kpi-circle kpi-circle-blue">
                            <span class="kpi-circle-label">Unidades totales</span>
                            <h3 id="kpiUnits" class="kpi-circle-value">226</h3>
                        </div>
                    </div>
                </div>

                <!-- KPI redondo 2 -->
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="kpi-circle-wrap">
                        <div class="kpi-circle kpi-circle-cyan">
                            <span class="kpi-circle-label">Ingresos totales</span>
                            <h3 id="kpiMoney" class="kpi-circle-value">6 511 994</h3>
                        </div>
                    </div>
                </div>

                <!-- KPI rectangular -->
                <div class="col-md-4">
                    <div class="dash-glass kpi-top-card h-100">
                        <span class="kpi-sub">Producto Top #1</span>
                        <h3 id="kpiTop1" class="kpi-top-value">Persol PO3007V Persol Eyewear</h3>
                    </div>
                </div>

            </div>

            <!-- GRÁFICOS -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="dash-glass h-100">
                        <h5 class="chart-card-title">Top productos (unidades)</h5>
                        <canvas id="cTop"></canvas>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="dash-glass h-100">
                        <h5 class="chart-card-title">Top productos (€)</h5>
                        <canvas id="cTopMoney"></canvas>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="dash-glass mb-4">
                        <h5 class="chart-card-title">Tendencia mensual (unidades)</h5>
                        <canvas id="cMesUnits"></canvas>
                    </div>
                </div>

                <div class="col-12">
                    <div class="dash-glass">
                        <h5 class="chart-card-title">Tendencia mensual (€)</h5>
                        <canvas id="cMesMoney"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>

    <script>
        Chart.defaults.color = "#64748b";
        Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";

        const gridColor = "rgba(148, 163, 184, 0.18)";

        const kpiUnits = document.getElementById('kpiUnits');
        const kpiMoney = document.getElementById('kpiMoney');
        const kpiTop1 = document.getElementById('kpiTop1');
        const btnReloadDash = document.getElementById('btnReloadDash');

        let chTop, chTopMoney, chMesUnits, chMesMoney;

        async function loadDashboardVentas() {
            const res = await fetch('/Controller/dashbaordVentasData.php');
            const data = await res.json();

            if (!data.ok) return;

            const totalUnidades = data.meses.reduce((a, b) => a + b.unidades, 0);
            const totalMoney = data.meses.reduce((a, b) => a + b.total, 0);

            kpiUnits.textContent = totalUnidades.toLocaleString();
            kpiMoney.textContent = totalMoney.toLocaleString();
            kpiTop1.textContent = data.top[0]?.producto || '—';

            renderTop(data.top);
            renderTopMoney(data.top);
            renderMesUnits(data.meses);
            renderMesMoney(data.meses);
        }

        /* BARRAS */
        function renderTop(top) {
            const ctx = document.getElementById('cTop').getContext('2d');

            if (chTop) chTop.destroy();

            const gradient = ctx.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, "#2f6fe4");
            gradient.addColorStop(1, "#8dbbff");

            chTop = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: top.map(x => x.producto),
                    datasets: [{
                        data: top.map(x => x.unidades),
                        backgroundColor: gradient,
                        borderRadius: 14,
                        borderSkipped: false,
                        barThickness: 16,
                        hoverBackgroundColor: "#245fcb"
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: "#8a94a7",
                                maxRotation: 38,
                                minRotation: 20,
                                font: {
                                    size: 11
                                }
                            },
                            grid: { display: false },
                            border: { display: false }
                        },
                        y: {
                            ticks: {
                                color: "#8a94a7",
                                font: {
                                    size: 11
                                }
                            },
                            grid: { color: gridColor },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        /* DOUGHNUT */
        function renderTopMoney(top) {
            const ctx = document.getElementById('cTopMoney').getContext('2d');

            if (chTopMoney) chTopMoney.destroy();

            chTopMoney = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: top.map(x => x.producto),
                    datasets: [{
                        data: top.map(x => x.total),
                        backgroundColor: [
                            "#2f6fe4",
                            "#3d82f6",
                            "#60a5fa",
                            "#8ec5ff",
                            "#1d4ed8",
                            "#4f7df0",
                            "#78aefc",
                            "#9ac8ff",
                            "#5b74e8",
                            "#b7d8ff"
                        ],
                        borderColor: "rgba(255,255,255,0.65)",
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    cutout: "68%",
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: {
                                color: "#6b7280",
                                padding: 16,
                                boxWidth: 12,
                                usePointStyle: true,
                                pointStyle: 'rectRounded',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        /* LINEAS UNIDADES */
        function renderMesUnits(meses) {
            const ctx = document.getElementById('cMesUnits').getContext('2d');

            if (chMesUnits) chMesUnits.destroy();

            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, "rgba(76, 139, 245, 0.35)");
            gradient.addColorStop(1, "rgba(76, 139, 245, 0.05)");

            chMesUnits = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: meses.map(x => x.mes),
                    datasets: [{
                        data: meses.map(x => x.unidades),
                        borderColor: "#4c8bf5",
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.42,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#4c8bf5",
                        pointBorderColor: "#ffffff",
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: { color: "#8a94a7" },
                            grid: { color: gridColor },
                            border: { display: false }
                        },
                        x: {
                            ticks: { color: "#8a94a7" },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }

        /* LINEAS INGRESOS */
        function renderMesMoney(meses) {
            const ctx = document.getElementById('cMesMoney').getContext('2d');

            if (chMesMoney) chMesMoney.destroy();

            const gradient = ctx.createLinearGradient(0, 0, 0, 320);
            gradient.addColorStop(0, "rgba(37, 99, 235, 0.32)");
            gradient.addColorStop(1, "rgba(37, 99, 235, 0.04)");

            chMesMoney = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: meses.map(x => x.mes),
                    datasets: [{
                        data: meses.map(x => x.total),
                        borderColor: "#2563eb",
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.42,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                        pointBackgroundColor: "#2563eb",
                        pointBorderColor: "#ffffff",
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: { color: "#8a94a7" },
                            grid: { color: gridColor },
                            border: { display: false }
                        },
                        x: {
                            ticks: { color: "#8a94a7" },
                            grid: { display: false },
                            border: { display: false }
                        }
                    }
                }
            });
        }
        btnReloadDash.onclick = loadDashboardVentas;
        loadDashboardVentas();
    </script>

</body>

</html>