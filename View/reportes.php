
<?php
 include('layout.php')
 
?>

<!DOCTYPE html>
<html lang="en">
 <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Óptica Grisol</title>
   <?php IncluirCSS();?>
</head>
    <body>
       <?php MostrarMenu();?>

<main class="container py-5">
    <h2 class="text-center mb-5">Reportes</h2>
<form class="row g-3 mb-4">
    <div class="col-md-4">
        <label for="tipoReporte" class="form-label">Tipos de Reporte</label>
        <select id="tipoReporte" class="form-select">
            <option selected>Todos</option>
            <option selected>Ventas</option>
            <option selected>Pacientes</option>
            <option selected>Inventario</option>
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
 <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <div class="col">
        <div class="card h-100 border-success">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Ventas Mensuales</h5>
             <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
             <p class="card-text text-muted">Por: Chase Gonzalez</p>
             <a href="reportes/REP-0001.pdf" target="_blank" class="btn btn-outline-success w-100">
            <i class="bi bi-download"></i>Descargar Reporte</a>
        </div>
    </div>
</div>
       <div class="col">
        <div class="card h-100 border-info">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Pacientes Atendidos</h5>
             <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
             <p class="card-text text-muted">Por: Chase Gonzalez</p>
             <a href="reportes/REP-0011.pdf" target="_blank" class="btn btn-outline-success w-100">
            <i class="bi bi-download"></i>Descargar Reporte</a>
        </div>
    </div>
</div>
           <div class="col">
            <div class="card h-100 border-secondary">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Inventario</h5>
             <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
             <p class="card-text text-muted">Por: Administrador</p>
             <button class="btn btn-outline-secundary w-100" disable>
            <i class="bi bi-clock-history"></i>En proceso</button>
        </div>
        
    </div>
  </div>
   <div class="col">
        <div class="card h-100 border-info">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Ingresos Diarios</h5>
                <p class="card-text text-muted mb-1">Generado: 30/07/2025</p>
                <p class="card-text text-muted">Por: Maria López</p>
                <a href="reportes/REP-0020.pdf" target="_blank" class="btn btn-outline-success w-100">
                    <i class="bi bi-download"></i> Descargar Reporte
                </a>
            </div>
        </div>
    </div>
</div>
  </main>
          <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
    </body>
</html>