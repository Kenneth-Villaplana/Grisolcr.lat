
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
    <body class="d-flex flex-column min-vh-100">
    <?php MostrarMenu();?>

    <main class="flex-fill">
        <div class="container py-5">
        <h2 class="text-center mb-4">Historial Médico</h2>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento</th>
                        <th>Doctor</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2024-03-15</td>
                        <td>Dolor de cabeza</td>
                        <td>Migraña</td>
                        <td>Analgésicos</td>
                        <td>Dra. Vargas</td>
                        <td><span class="text-muted">Finalizada</span></td>
                    </tr>
                    <tr class="align-bottom">
                        <td>2024-04-10</td>
                        <td>Visión borrosa</td>
                        <td>Astigmatismo</td>
                        <td>Lentes recetados</td>
                        <td>Dr. Rodríguez</td>
                        <td>
                            <a href="editarCita.php?id=2" class="btn btn-custom btn-outline-primary">
                                <i class="bi bi-pencil-square"></i> Pendiente
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>2024-06-05</td>
                        <td class="align-top">Control anual</td>
                        <td>Sin cambios</td>
                        <td>Revisión general</td>
                        <td>Dra. Morales</td>
                        <td><span class="text-muted">Finalizada</span></td>
                    </tr>
                    <tr>
    <td>2024-03-15</td>
    <td>Dolor de cabeza</td>
    <td>Migraña</td>
    <td>Analgésicos</td>
    <td>Dra. Vargas</td>
    <td><span class="text-muted">Finalizada</span></td>
</tr>
<tr class="align-bottom">
    <td>2024-04-10</td>
    <td>Visión borrosa</td>
    <td>Astigmatismo</td>
    <td>Lentes recetados</td>
    <td>Dr. Rodríguez</td>
    <td>
        <a href="editarCita.php?id=2" class="btn btn-custom btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Pendiente
        </a>
    </td>
</tr>
<tr>
    <td>2024-05-20</td>
    <td>Chequeo general</td>
    <td>Sin cambios</td>
    <td>Revisión rutinaria</td>
    <td>Dra. Morales</td>
    <td><span class="text-muted">Finalizada</span></td>
</tr>
<tr>
    <td>2024-06-15</td>
    <td>Dolor ocular</td>
    <td>Conjuntivitis</td>
    <td>Gotas oftálmicas</td>
    <td>Dr. Vargas</td>
    <td><span class="text-muted">Finalizada</span></td>
</tr>
<tr>
    <td>2024-07-01</td>
    <td>Control anual</td>
    <td>Leve miopía</td>
    <td>Lentes recetados</td>
    <td>Dra. Morales</td>
    <td><span class="text-muted">Finalizada</span></td>
</tr>
<tr>
    <td>2024-07-20</td>
    <td>Visión borrosa</td>
    <td>Astigmatismo</td>
    <td>Lentes recetados</td>
    <td>Dr. Rodríguez</td>
    <td>
        <a href="editarCita.php?id=3" class="btn btn-custom btn-outline-primary">
            <i class="bi bi-pencil-square"></i> Pendiente
        </a>
    </td>
</tr>
                </tbody>
            </table>
        </div>
    </main>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
</body>
</html>