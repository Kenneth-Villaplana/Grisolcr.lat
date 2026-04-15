<?php
session_start();
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
    <?php IncluirCSS();?>

    <!-- Chart.js para los dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

</head>

<body>
<?php MostrarMenu();?>

<main class="container py-5">
    <h2 class="text-center mb-5">Reportes</h2>
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
     <section>
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Dashboard de Ventas</h3>
                <small class="text-muted">Visualización moderna en tiempo real</small>
            </div>

            <button id="btnReloadDash" class="btn btn-outline-primary mt-2">
                <i class="bi bi-arrow-clockwise"></i> Recargar
            </button>
        </div>

        <!-- KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 dash-glass">
                    <div class="kpi-sub">Unidades totales</div>
                    <div id="kpiUnidades" class="kpi">—</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 dash-glass">
                    <div class="kpi-sub">Ingresos totales (₡)</div>
                    <div id="kpiTotal" class="kpi">—</div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-3 dash-glass">
                    <div class="kpi-sub">Producto Top #1</div>
                    <div id="kpiTop1" class="kpi">—</div>
                </div>
            </div>
        </div>

        <!-- GRÁFICOS -->
        <div class="row g-4">

            <div class="col-lg-6">
                <div class="p-3 dash-glass">
                    <h5>Top productos (unidades)</h5>
                    <canvas id="cTop"></canvas>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-3 dash-glass">
                    <h5>Top productos (₡)</h5>
                    <canvas id="cTopMoney"></canvas>
                </div>
            </div>

            <div class="col-12">
                <div class="p-3 dash-glass">
                    <h5>Tendencia mensual (unidades)</h5>
                    <canvas id="cMesUnits"></canvas>
                </div>
            </div>

            <div class="col-12">
                <div class="p-3 dash-glass">
                    <h5>Tendencia mensual (₡)</h5>
                    <canvas id="cMesMoney"></canvas>
                </div>
            </div>

        </div>
    </section>

</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script>
Chart.defaults.color = "#cbd5f5";
Chart.defaults.font.family = "'Inter', sans-serif";

const gridColor = "rgba(255,255,255,0.08)";

let chTop, chTopMoney, chMesUnits, chMesMoney;

async function loadDashboardVentas() {
    const res = await fetch('/Controller/dashbaordVentasData.php');
    const data = await res.json();

    if (!data.ok) return;

    const totalUnidades = data.meses.reduce((a,b)=>a+b.unidades,0);
    const totalMoney = data.meses.reduce((a,b)=>a+b.total,0);

    kpiUnidades.textContent = totalUnidades.toLocaleString();
    kpiTotal.textContent = totalMoney.toLocaleString();
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

    chTop = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: top.map(x => x.producto),
            datasets: [{
                data: top.map(x => x.unidades),
                backgroundColor: "#3f72af",
                borderRadius: 20,
                barThickness: 18
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    ticks: {
                        color: "#f1f5f9",
                        maxRotation: 40,
                        minRotation: 20
                    },
                    grid: { display: false }
                },
                y: {
                    ticks: { color: "#cbd5f5" },
                    grid: { color: gridColor }
                }
            }
        }
    });
}

/* BARRAS HORIZONTALES */
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
                    "#3f72af",
                    "#2563eb",
                    "#60a5fa",
                    "#93c5fd",
                    "#1d4ed8",
                    "#1e40af",
                    "#3b82f6",
                    "#6366f1",
                    "#818cf8",
                    "#a5b4fc"
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: "65%",
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        color: "#e2e8f0",
                        padding: 15,
                        boxWidth: 12
                    }
                }
            }
        }
    });
}

/* LINEAS */
function renderMesUnits(meses) {
    const ctx = document.getElementById('cMesUnits').getContext('2d');

    if (chMesUnits) chMesUnits.destroy();

    chMesUnits = new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses.map(x => x.mes),
            datasets: [{
                data: meses.map(x => x.unidades),
                borderColor: "#60a5fa",
                backgroundColor: "rgba(96,165,250,0.2)",
                fill: true,
                tension: 0.45,
                pointRadius: 5,
                pointBackgroundColor: "#60a5fa"
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: { color: "#cbd5f5" },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: "#e2e8f0" },
                    grid: { display: false }
                }
            }
        }
    });
}

function renderMesMoney(meses) {
    const ctx = document.getElementById('cMesMoney').getContext('2d');

    if (chMesMoney) chMesMoney.destroy();

    chMesMoney = new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses.map(x => x.mes),
            datasets: [{
                data: meses.map(x => x.total),
                borderColor: "#2563eb",
                backgroundColor: "rgba(37,99,235,0.25)",
                fill: true,
                tension: 0.45,
                pointRadius: 5,
                pointBackgroundColor: "#2563eb"
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: { color: "#cbd5f5" },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: "#e2e8f0" },
                    grid: { display: false }
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