<?php
include('layout.php');
include_once __DIR__ . '/../Model/personalModel.php';

$cedulaFiltro = $_GET['cedula'] ?? null;
$listaPersonal = ObtenerPersonal($cedulaFiltro);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Óptica Grisol</title>
    <?php IncluirCSS(); ?>
</head>

<body style="min-height:100vh; margin:0; padding:0;">
<div style="min-height:100vh; display:flex; flex-direction:column;">

    
    <?php MostrarMenu(); ?>

    <main style="flex:1 0 auto;" class="container-fluid py-4 px-4">
        <div class="mx-auto" style="max-width: 1500px;">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Gestión de Personal</h2>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center mb-4 gap-3">

                <form method="GET" 
                      class="d-flex flex-column flex-sm-row align-items-center gap-2 w-100" 
                      style="max-width: 900px;">

                    <input type="text" 
                        name="cedula" 
                        class="form-control"
                        placeholder="Filtrar por cédula">

                    <button type="submit" class="btn btn-outline-primary px-4">Buscar</button>
                    <a href="personal.php" class="btn btn-outline-secondary px-4">Limpiar</a>
                </form>

                <a href="registrarPersonal.php" 
                class="btn btn-outline-primary rounded-pill d-flex align-items-center gap-2 px-4 py-2">
                    <i class="bi bi-plus-circle"></i> Agregar personal
                </a>
            </div>

           <div class="card shadow-sm border-0 p-4 h-100 card-expandible">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Cédula</th>
                                <th>Nombre</th>
                                <th>Correo Electrónico</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($listaPersonal as $personal): ?>
                                <tr>
                                    <td><?= $personal['Cedula']; ?></td>
                                    <td><?= $personal['Nombre'] . ' ' . $personal['Apellido'] . ' ' . $personal['ApellidoDos']; ?></td>
                                    <td><?= $personal['CorreoElectronico']; ?></td>
                                    <td><?= $personal['Telefono']; ?></td>
                                    <td><?= $personal['Direccion']; ?></td>
                                    <td>
                                        <?php
                                        switch ($personal['Id_rol']) {
                                            case 1: echo "Administrador/a"; break;
                                            case 2: echo "Asistente"; break;
                                            case 3: echo "Doctor/a"; break;
                                            case 4: echo "Cajero/a"; break;
                                            default: echo "Desconocido";
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <span class="badge <?= $personal['Estado'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                            <?= $personal['Estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="editarpersonal.php?id=<?= $personal['IdUsuario']; ?>" 
                                        class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php MostrarFooter(); ?>

</div>

<?php IncluirScripts(); ?>

</body>
</html>