<?php
// ACTIVAR REPORTE DE ERRORES AL MÁXIMO
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

try {
  // Incluir layout primero
  include('layout.php');

  // Incluir otros archivos necesarios
  $baseDatosPath = __DIR__ . '/../Model/baseDatos.php';
  $vendorPath = __DIR__ . '/../vendor/autoload.php';
  $googleModelPath = __DIR__ . '/../Model/GoogleCalendarModel.php';

  if (!file_exists($baseDatosPath)) {
    throw new Exception("baseDatos.php no encontrado en: $baseDatosPath");
  }
  if (!file_exists($vendorPath)) {
    throw new Exception("vendor/autoload.php no encontrado");
  }
  if (!file_exists($googleModelPath)) {
    throw new Exception("GoogleCalendarModel.php no encontrado");
  }

  include_once $baseDatosPath;
  require_once $vendorPath;
  require_once $googleModelPath;

  // Verificar si el usuario está loggeado
  if (!isset($_SESSION['UsuarioID'])) {
    header('Location: /OptiGestion/view/iniciarSesion.php');
    exit;
  }

  $usuarioId = $_SESSION['UsuarioID'];
  $mensajeExito = '';
  $mensajeError = '';

  // Obtener información del usuario
  function obtenerUsuarioInfo($conn, $usuarioId)
  {
    $query = "SELECT u.*, p.PacienteId 
                  FROM usuario u 
                  LEFT JOIN paciente p ON u.IdUsuario = p.UsuarioId 
                  WHERE u.IdUsuario = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuarioId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
  }

  // Función para verificar si un doctor tiene Google Calendar habilitado
  function hasGoogleCalendar($doctorId)
  {
    $conn = AbrirBD();
    $query = "SELECT google_calendar_enabled 
                  FROM usuario u
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                  WHERE p.EmpleadoId = ? AND u.google_calendar_enabled = 1";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $doctorId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $hasCalendar = mysqli_stmt_num_rows($stmt) > 0;

    mysqli_stmt_close($stmt);
    CerrarBD($conn);

    return $hasCalendar;
  }

  // Función para obtener el token de Google de un doctor
  function getGoogleToken($doctorId)
  {
    $conn = AbrirBD();
    $query = "SELECT google_access_token, google_refresh_token, google_token_expires_at
                  FROM usuario u
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                  WHERE p.EmpleadoId = ? AND u.google_access_token IS NOT NULL";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $doctorId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $tokenData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    CerrarBD($conn);

    return $tokenData;
  }

  // Obtener todos los doctores - CORREGIDO para tu estructura de base de datos
  function obtenerDoctores($conn)
  {
    $query = "SELECT 
                    p.EmpleadoId as id_empleado,
                    u.IdUsuario,
                    u.Nombre,
                    u.Apellido,
                    u.ApellidoDos,
                    u.CorreoElectronico,
                    u.Telefono,
                    u.google_calendar_enabled
                  FROM usuario u
                  INNER JOIN personal p ON u.IdUsuario = p.UsuarioId
                  WHERE u.RolUsuario = 'Empleado' 
                    AND p.Id_rol = 3 
                    AND u.Estado = 1";

    $result = mysqli_query($conn, $query);

    if (!$result) {
      throw new Exception("Error en consulta de doctores: " . mysqli_error($conn));
    }

    $doctores = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $doctores[] = $row;
    }

    return $doctores;
  }

  // Procesar agendamiento de cita
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'agendar_cita') {

    $conn = AbrirBD();

    try {
      $doctorId = intval($_POST['doctor_id']);
      $fechaHora = $_POST['fecha_hora'];
      $motivo = trim($_POST['motivo']);
      $duracion = 30; // 30 minutos por defecto

      // Validaciones
      if (empty($doctorId) || empty($fechaHora) || empty($motivo)) {
        throw new Exception("Todos los campos son obligatorios");
      }

      // Verificar que la fecha sea futura
      if (strtotime($fechaHora) <= time()) {
        throw new Exception("La cita debe ser en una fecha y hora futura");
      }

      // Obtener información del usuario/paciente
      $usuarioInfo = obtenerUsuarioInfo($conn, $usuarioId);
      $pacienteId = $usuarioInfo['PacienteId'];

      // Si no tiene pacienteId, crear uno
      if (!$pacienteId) {
        $nombreCompleto = $usuarioInfo['Nombre'] . ' ' . $usuarioInfo['Apellido'] . ' ' . $usuarioInfo['ApellidoDos'];
        $stmtPac = mysqli_prepare($conn, "INSERT INTO paciente (UsuarioId, Nombre) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmtPac, "is", $usuarioId, $nombreCompleto);
        mysqli_stmt_execute($stmtPac);
        $pacienteId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmtPac);
      }

      // Verificar disponibilidad con Google Calendar
      if (hasGoogleCalendar($doctorId)) {
        $googleToken = getGoogleToken($doctorId);
        if ($googleToken) {
          $googleModel = new GoogleCalendarModel();
          $googleModel->setAccessToken($googleToken['google_access_token']);

          // Verificar si el horario está disponible
          $startDateTime = $fechaHora;
          $endDateTime = date('Y-m-d H:i:s', strtotime($fechaHora . ' + ' . $duracion . ' minutes'));

          // USAR checkAvailability CORRECTAMENTE
          $disponibilidad = $googleModel->checkAvailability($startDateTime, $endDateTime);
          if (!$disponibilidad['available']) {
            throw new Exception("El horario seleccionado ya no está disponible. Por favor elige otro.");
          }
        }
      }

      // Crear cita en la base de datos - CORREGIDO
      $estado = 'pendiente'; // Variable para el estado
      $stmtCita = mysqli_prepare(
        $conn,
        query: "INSERT INTO cita (Fecha, Duracion, Nombre, Estado, ID_Paciente, id_empleado) 
     VALUES (?, ?, ?, ?, ?, ?)"
      );

      if (!$stmtCita) {
        throw new Exception("Error preparando consulta: " . mysqli_error($conn));
      }

      // CORRECCIÓN: "sissii" = 6 caracteres para 6 parámetros
      $bindResult = mysqli_stmt_bind_param(
        $stmtCita,
        "sissii",
        $fechaHora,     // s - string (Fecha)
        $duracion,      // i - integer (Duracion)
        $motivo,        // s - string (Nombre)
        $estado,        // s - string (Estado) - AHORA ES UNA VARIABLE
        $pacienteId,    // i - integer (ID_Paciente)
        $doctorId       // i - integer (id_empleado)
      );

      if (!$bindResult) {
        throw new Exception("Error en bind_param: " . mysqli_stmt_error($stmtCita));
      }

      if (!mysqli_stmt_execute($stmtCita)) {
        throw new Exception("Error al crear la cita: " . mysqli_stmt_error($stmtCita));
      }

      // Sincronizar con Google Calendar si está disponible
      if (hasGoogleCalendar($doctorId) && isset($googleToken) && isset($googleModel)) {
        $pacienteInfo = obtenerUsuarioInfo($conn, $usuarioId);

        // Preparar datos del evento en el formato correcto
        $eventData = [
          'summary' => "Cita Óptica - " . $pacienteInfo['Nombre'] . " " . $pacienteInfo['Apellido'],
          'start_time' => $startDateTime,
          'end_time' => $endDateTime,
          'description' => "Paciente: " . $pacienteInfo['Nombre'] . " " . $pacienteInfo['Apellido'] . "\n" .
            "Teléfono: " . $pacienteInfo['Telefono'] . "\n" .
            "Motivo: " . $motivo,
          'attendees' => [
            ['email' => $pacienteInfo['CorreoElectronico']]
          ],
          'timezone' => 'America/Costa_Rica'
        ];

        // Llamar al método con un solo parámetro (array)
        $resultadoEvento = $googleModel->createEvent($eventData);

        if ($resultadoEvento['success']) {
          // Actualizar cita con ID de Google Calendar
          $stmtUpdate = mysqli_prepare(
            $conn,
            "UPDATE cita SET google_event_id = ?, google_calendar_synced = TRUE WHERE IdCita = ?"
          );
          mysqli_stmt_bind_param($stmtUpdate, "si", $resultadoEvento['event_id'], $citaId);
          mysqli_stmt_execute($stmtUpdate);
          mysqli_stmt_close($stmtUpdate);
        }
      }

      $mensajeExito = "¡Cita agendada exitosamente! " .
        (isset($resultadoEvento) && $resultadoEvento['success'] ? "Se ha sincronizado con el calendario del doctor." : "");

    } catch (Exception $e) {
      $mensajeError = $e->getMessage();
    }

    CerrarBD($conn);
  }

  // Obtener datos para la vista
  $conn = AbrirBD();
  $usuarioInfo = obtenerUsuarioInfo($conn, $usuarioId);
  $doctores = obtenerDoctores($conn);
  CerrarBD($conn);

  ?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - Óptica Grisol</title>
    <?php IncluirCSS(); ?>
    <style>
      .main-content {
        min-height: calc(100vh - 200px);
        padding: 40px 0;
      }

      .form-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      }

      .form-group {
        margin-bottom: 20px;
      }

      label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
      }

      .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
      }

      .alert {
        border-radius: 8px;
        border: none;
      }

      .doctor-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
    </style>
  </head>

  <body>
    <?php MostrarMenu(); ?>

    <div class="main-content">
      <div class="container">
        <div class="form-container">
          <h1 class="text-center mb-4">Agendar Nueva Cita</h1>

          <?php if (!empty($mensajeExito)): ?>
            <div class="alert alert-success alert-dismissible fade show">
              <?php echo htmlspecialchars($mensajeExito); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (!empty($mensajeError)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
              <?php echo htmlspecialchars($mensajeError); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <input type="hidden" name="action" value="agendar_cita">

            <div class="form-group">
              <label for="doctor_id">Seleccionar Doctor:</label>
              <select name="doctor_id" id="doctor_id" class="form-control" required>
                <option value="">-- Seleccione un doctor --</option>
                <?php foreach ($doctores as $doctor): ?>
                  <option value="<?php echo $doctor['id_empleado']; ?>">
                    <span class="doctor-option">
                      <span>Dr. <?php echo htmlspecialchars($doctor['Nombre'] . ' ' . $doctor['Apellido']); ?></span>
                      <?php if ($doctor['google_calendar_enabled']): ?>
                        <span class="badge bg-success">📅 Sincronizado</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Sin calendario</span>
                      <?php endif; ?>
                    </span>
                  </option>
                <?php endforeach; ?>
              </select>
              <small class="form-text text-muted">
                Los doctores con 📅 están sincronizados con Google Calendar para verificar disponibilidad en tiempo real.
              </small>
            </div>

            <div class="form-group">
              <label for="fecha_hora">Fecha y Hora:</label>
              <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="form-control" required
                min="<?php echo date('Y-m-d\TH:i'); ?>">
            </div>

            <div class="form-group">
              <label for="motivo">Motivo de la cita:</label>
              <textarea name="motivo" id="motivo" class="form-control" rows="4" required
                placeholder="Describa el motivo de su consulta..."></textarea>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary btn-lg">Agendar Cita</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php MostrarFooter(); ?>
    <?php IncluirScripts(); ?>
  </body>

  </html>
  <?php

} catch (Exception $e) {
  echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f8d7da; color: #721c24; }
            .error-container { max-width: 800px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1>💥 Error en el sistema</h1>
            <p><strong>Mensaje:</strong> " . $e->getMessage() . "</p>
            <p><strong>Archivo:</strong> " . $e->getFile() . "</p>
            <p><strong>Línea:</strong> " . $e->getLine() . "</p>
        </div>
    </body>
    </html>";
}
?>