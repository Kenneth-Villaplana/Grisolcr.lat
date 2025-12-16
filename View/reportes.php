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

    <style>
        .dash-glass {
            border-radius: 18px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0,0,0,.25);
        }
        .kpi {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -.5px;
        }
        .kpi-sub {
            opacity: .8;
        }
        canvas {
            width: 100% !important;
            height: 330px !important;
        }
    </style>
</head>

<body>
<?php MostrarMenu();?>

<main class="container py-5">
    <h2 class="text-center mb-5">Reportes</h2>

    <!-- Filtros (por ahora visuales, listos para conectar a futuro) -->
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

    <!-- TARJETAS DE REPORTES PDF (legacy, como ya las tenías) -->
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

    <!-- DASHBOARD INTERACTIVO (nuevo) -->
    <section class="mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0">Dashboard de Ventas</h3>
                <div class="text-muted">Productos más vendidos y tendencia mensual (en vivo desde la BD)</div>
            </div>
            <button id="btnReloadDash" type="button" class="btn btn-outline-success mt-2">
                <i class="bi bi-arrow-clockwise"></i> Recargar
            </button>
        </div>

        <!-- KPIs -->
        <div class="row g-3 mb-3">
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

        <!-- Gráficos -->
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="p-3 dash-glass">
                    <h5 class="mb-2">Top 10 productos (unidades)</h5>
                    <canvas id="cTop"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-3 dash-glass">
                    <h5 class="mb-2">Top 10 productos (₡ total vendido)</h5>
                    <canvas id="cTopMoney"></canvas>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 dash-glass">
                    <h5 class="mb-2">Tendencia mensual (unidades)</h5>
                    <canvas id="cMesUnits"></canvas>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3 dash-glass">
                    <h5 class="mb-2">Tendencia mensual (₡)</h5>
                    <canvas id="cMesMoney"></canvas>
                </div>
            </div>
        </div>
    </section>
</main>

<?php MostrarFooter(); ?>
<?php IncluirScripts(); ?>

<script>
let chTop, chTopMoney, chMesUnits, chMesMoney;

async function loadDashboardVentas() {
    const respuesta = await fetch('/Controller/dashbaordVentasData.php', { cache: 'no-store' });
    const data = await respuesta.json();

    if (!data.ok) {
        console.error(data.error);
        alert('Error cargando dashboard: ' + data.error);
        return;
    }

    // KPIs
    const totalUnidades = (data.meses || []).reduce((acc, fila) => acc + (fila.unidades || 0), 0);
    const totalMoney    = (data.meses || []).reduce((acc, fila) => acc + (fila.total || 0), 0);

    document.getElementById('kpiUnidades').textContent = totalUnidades.toLocaleString('es-CR');
    document.getElementById('kpiTotal').textContent    = totalMoney.toLocaleString('es-CR', { minimumFractionDigits: 2 });
    document.getElementById('kpiTop1').textContent     = (data.top && data.top[0]) ? data.top[0].producto : '—';

    renderTop(data.top || []);
    renderTopMoney(data.top || []);
    renderMesUnits(data.meses || []);
    renderMesMoney(data.meses || []);
}

function renderTop(top) {
    const labels = top.map(x => x.producto);
    const values = top.map(x => x.unidades);

    if (chTop) chTop.destroy();
    chTop = new Chart(document.getElementById('cTop'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Unidades',
                data: values,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

function renderTopMoney(top) {
    const labels = top.map(x => x.producto);
    const values = top.map(x => x.total);

    if (chTopMoney) chTopMoney.destroy();
    chTopMoney = new Chart(document.getElementById('cTopMoney'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: '₡ total vendido',
                data: values,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
}

function renderMesUnits(meses) {
    const labels = meses.map(x => x.mes);
    const values = meses.map(x => x.unidades);

    if (chMesUnits) chMesUnits.destroy();
    chMesUnits = new Chart(document.getElementById('cMesUnits'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Unidades',
                data: values,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

function renderMesMoney(meses) {
    const labels = meses.map(x => x.mes);
    const values = meses.map(x => x.total);

    if (chMesMoney) chMesMoney.destroy();
    chMesMoney = new Chart(document.getElementById('cMesMoney'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: '₡',
                data: values,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

document.getElementById('btnReloadDash').addEventListener('click', () => {
    loadDashboardVentas().catch(err => alert(err.message));
});

// Carga inicial
loadDashboardVentas().catch(err => console.error(err));
</script>

</body>
</html>
