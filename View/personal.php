
<?php
include('layout.php');
include_once __DIR__ . '/../Model/personalModel.php';

 $cedulaFiltro = $_GET['cedula'] ?? null;

 $listaPersonal =ObtenerPersonal($cedulaFiltro);
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
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-center mb-4">Datos de personal</h2>
     <a href="registrarPersonal.php" class="btn btn-custom "><i class="bi bi-plus-circle"></i>Agregar personal</a>
</div>

<div class="mb-4 d-flex justify-content-center">
  <form class="filter-form text-center d-flexflex-wrap gap-2" method="GET">
    <input type="text" name="cedula" class="form-control" placeholder="Filtrar por cédula">
    <button type="submit" class="mb-2 my-2 btn btn-custom">Buscar</button>
   <a href="personal.php" class="mb-2 my-2 btn btn-outline-secondary">Limpiar</a>
  </form>
</div>

      <div class="card card-custom p-3">
      <div class="table-responsive">
  <table class="table table-hover align-middle">
    <thead class="table-light">
      <tr>
       <th>Cédula</th>
       <th>Nombre</th>
       <th>Correo Electrónico</th>
       <th>Número de Teléfono</th>
       <th>Dirección</th>
       <th>Rol</th>
       <th>Estado</th>
      </tr>
    </thead>
    <tbody>
        <?php foreach ($listaPersonal as $personal): ?>
     <tr>
        <td><?php echo $personal['Cedula']; ?></td>
        <td><?php echo $personal['Nombre']. ' '.$personal['Apellido'].' '.$personal['ApellidoDos']; ?></td>
        <td><?php echo $personal['CorreoElectronico']; ?></td>
        <td><?php echo $personal['Telefono']; ?></td>
        <td><?php echo $personal['Direccion']; ?></td>
        <td> <?php switch ($personal['Id_rol']) {
            case 1: echo "Administrador/a"; break;
            case 2: echo "Asistente"; break;
            case 3: echo "Doctor/a"; break;
            case 4: echo "Cajero/a"; break;
            default: echo "Desconocido";
        }?>
        </td>
        
        <td><?php echo $personal['Estado'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
        <td>
            <a href="editarpersonal.php?id=<?php echo $personal['IdUsuario']; ?>" class="btn btn-custom btn-outline-primary">
                <i class="bi bi-pencil-square"></i>
            </a>
        </td>
    </tr>
    <?php endforeach;?>
        </tbody>
    </table>
  </div>
  </div>
</main>
          <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
    </body>
</html>
