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

<body class="body-fullscreen">
<div class="layout-vertical">
    <?php MostrarMenu(); ?>

   
  <header class="hero-img-header personal-hero">
    <div class="container position-relative">
        <h1 class="catalogo-title">Gestión de Personal</h1>
    </div>
</header>



    <main class="staff-main-wrapper">
        <div class="container-xl px-4">


         <div class="inventario-filters shadow-sm rounded-4 mb-5">

    <form method="GET" class="row g-3 align-items-end">

        <!-- CÉDULA -->
        <div class="col-md-6">
            <label class="form-label fw-semibold inventario-label">
                Filtrar por cédula:
            </label>

            <input type="text"
                   name="cedula"
                   class="form-control inventario-input"
                   placeholder="Ingrese número de cédula"
                   value="<?php echo htmlspecialchars($cedulaFiltro ?? '', ENT_QUOTES); ?>">
        </div>

        <!-- BOTONES -->
        <div class="col-md-3 text-md-end">
            <label class="form-label d-none d-md-block">&nbsp;</label>

            <div class="d-flex justify-content-md-end justify-content-center gap-2">

                <button type="submit" class="btn-inv-primary">
                    <i class="bi bi-search me-2"></i> Buscar
                </button>

               <a href="personal.php" class="btn-inv-ghost btn-inv-ghost-link">
                <i class="me-1"></i> Limpiar
            </a>

            </div>
        </div>

        <!-- AGREGAR PERSONAL -->
        <div class="col-md-3 text-md-end">
            <label class="form-label d-none d-md-block">&nbsp;</label>

            <a href="registrarPersonal.php"
               class="btn btn-staff-outline rounded-pill d-flex align-items-center gap-2 px-4 py-2 w-100 justify-content-center">
                <i class="bi bi-plus-circle"></i>
                Agregar personal
            </a>
        </div>

    </form>
</div>

          
            <div class="staff-card-wrapper mt-4 mb-5">
                <div class="staff-card-inner">

                    <div class="staff-card-header d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-0 fw-semibold">Listado de personal</h5>
                            <span class="staff-chip-count">
                                <i class="bi bi-people-fill me-1"></i>
                                <?php echo count($listaPersonal); ?> personas registradas
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive staff-table-responsive">
                        <table class="table staff-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Correo electrónico</th>
                                    <th>Teléfono</th>
                                    <th>Dirección</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                     <th class="text-center">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                            <?php foreach ($listaPersonal as $personal): ?>
                                <tr>
                                    <td class="staff-col-id">
                                        <span class="staff-id-pill">
                                            <?php echo $personal['Cedula']; ?>
                                        </span>
                                    </td>

                                    <td class="staff-col-name">
                                        <div class="d-flex align-items-center">
                                            
                                              
                                            </div>
                                            <div>
                                                <div class="staff-name">
                                                    <?php echo $personal['Nombre'] . ' ' . $personal['Apellido'] . ' ' . $personal['ApellidoDos']; ?>
                                              
                                            </div>
                                        </div>
                                    </td>

                                    <td class="staff-col-email">
                                        <span class="staff-text-muted">
                                            <?php echo $personal['CorreoElectronico']; ?>
                                        </span>
                                    </td>

                                    <td><?php echo $personal['Telefono']; ?></td>

                                    <td class="staff-col-address">
                                        <span class="staff-text-muted">
                                            <?php echo $personal['Direccion']; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php
                                        switch ($personal['Id_rol']) {
                                            case 1: $rolTexto = "Administrador/a"; break;
                                            case 2: $rolTexto = "Asistente"; break;
                                            case 3: $rolTexto = "Doctor/a"; break;
                                            case 4: $rolTexto = "Cajero/a"; break;
                                            default: $rolTexto = "Desconocido";
                                        }
                                        ?>
                                        <span class="staff-role-pill">
                                            <i class="bi bi-person-badge me-1"></i><?php echo $rolTexto; ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php $esActivo = ($personal['Estado'] == 1); ?>
                                        <span class="staff-status-pill <?php echo $esActivo ? 'status-activo' : 'status-inactivo'; ?>">
                                            <span class="status-dot"></span>
                                            <?php echo $esActivo ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="editarpersonal.php?id=<?php echo $personal['IdUsuario']; ?>"
                                           class="btn btn-sm staff-btn-edit">
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
        </div>
    </main>

    <?php MostrarFooter(); ?>

</div>

<?php IncluirScripts(); ?>

</body>
</html>