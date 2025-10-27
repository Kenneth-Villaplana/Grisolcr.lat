<?php
session_start();
include('layout.php');
include_once __DIR__ . '/../Model/baseDatos.php';

$conn = AbrirBD();

$usuarioLoggeado = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// === OBTENER CITAS EXISTENTES ===
$citasExistentes = [];
$result = mysqli_query($conn, "SELECT Fecha FROM cita");
while ($row = mysqli_fetch_assoc($result)) {
  $citasExistentes[] = $row['Fecha'];
}
CerrarBD($conn);

// === FUNCIÓN PARA OBTENER USUARIO DESDE BASE DE DATOS ===
function obtenerUsuarioDesdeSesion($conn)
{
  if (!empty($_SESSION["UsuarioID"])) {
    $id = intval($_SESSION["UsuarioID"]);
    $query = "SELECT IdUsuario, Nombre, Apellido, ApellidoDos, CorreoElectronico, Telefono, Direccion FROM usuario WHERE IdUsuario = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($usuario) {
      $usuario['Apellidos'] = trim(($usuario['Apellido'] ?? '') . ' ' . ($usuario['ApellidoDos'] ?? ''));
      return $usuario;
    }
  }
  return null;
}

// === GUARDAR CITA ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fecha = $_POST['date'] ?? '';
  $hora = $_POST['time'] ?? '';
  $duracion = $_POST['duration'] ?? 30;
  $nombreCita = trim($_POST['Name'] ?? '');
  $estado = 'Pendiente';
  $idPaciente = $_POST['idPaciente'] ?? 0;
  $nombrePacienteManual = trim($_POST['nombrePacienteManual'] ?? '');

  if (empty($fecha) || empty($hora) || empty($nombreCita)) {
    $mensajeError = "Faltan datos obligatorios.";
  } else {
    $fechaHora = $fecha . ' ' . $hora . ':00';
    $conn = AbrirBD();

    // Obtener usuario loggeado desde sesión o BD
    $usuarioLoggeado = obtenerUsuarioDesdeSesion($conn);

    if ($usuarioLoggeado) {
      $idUsuario = intval($usuarioLoggeado['IdUsuario']);

      // Verificar si ya existe un paciente vinculado a este usuario
      $stmtCheck = mysqli_prepare($conn, "SELECT PacienteId FROM paciente WHERE UsuarioId = ?");
      mysqli_stmt_bind_param($stmtCheck, "i", $idUsuario);
      mysqli_stmt_execute($stmtCheck);
      mysqli_stmt_bind_result($stmtCheck, $existingPacienteId);
      mysqli_stmt_fetch($stmtCheck);
      mysqli_stmt_close($stmtCheck);

      if ($existingPacienteId) {
        $idPaciente = $existingPacienteId;
      } else {
        // Crear nuevo paciente vinculado al usuario
        $nombreCompleto = trim(($usuarioLoggeado['Nombre'] ?? '') . ' ' . ($usuarioLoggeado['Apellidos'] ?? ''));
        $stmtPac = mysqli_prepare($conn, "INSERT INTO paciente (NombreCompleto, UsuarioId) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmtPac, "si", $nombreCompleto, $idUsuario);
        if (mysqli_stmt_execute($stmtPac)) {
          $idPaciente = mysqli_insert_id($conn);
        } else {
          $mensajeError = "Error al crear paciente: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmtPac);
      }
    } elseif (!empty($nombrePacienteManual)) {
      // No hay usuario loggeado → crear paciente sin usuario
      $stmtPac = mysqli_prepare($conn, "INSERT INTO paciente (NombreCompleto) VALUES (?)");
      mysqli_stmt_bind_param($stmtPac, "s", $nombrePacienteManual);
      if (mysqli_stmt_execute($stmtPac)) {
        $idPaciente = mysqli_insert_id($conn);
      } else {
        $mensajeError = "Error al crear paciente manual: " . mysqli_error($conn);
      }
      mysqli_stmt_close($stmtPac);
    } else {
      $mensajeError = "Debe ingresar el nombre del paciente.";
    }

    // === INSERTAR LA CITA ===
    if (empty($mensajeError) && $idPaciente > 0) {
      $stmtCita = mysqli_prepare($conn, "INSERT INTO cita (Fecha, Duracion, Nombre, Estado, ID_Paciente) VALUES (?, ?, ?, ?, ?)");
      mysqli_stmt_bind_param($stmtCita, "sissi", $fechaHora, $duracion, $nombreCita, $estado, $idPaciente);
      if (mysqli_stmt_execute($stmtCita)) {
        $mensajeExito = "Cita agendada correctamente.";
      } else {
        $mensajeError = "Error al agendar la cita: " . mysqli_error($conn);
      }
      mysqli_stmt_close($stmtCita);
    }

    CerrarBD($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Óptica Grisol - Agendar Cita</title>
  <?php IncluirCSS(); ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --bg: #f4f6f9;
      --card: #fff;
      --text: #2a2f3a;
      --accent: #0d6efd;
      --accent-dark: #0b5ed7;
      --slot-border: #dde2ea;
      --navbar-green: #198754;
    }

    .theme-dark {
      --bg: #0f1623;
      --card: #1a2233;
      --text: #e6eaf2;
      --accent: #5aa2ff;
      --accent-dark: #418ef0;
      --slot-border: #2d3b57;
      --navbar-green: #198754;
    }

    body {
      font-family: 'Noto Sans', sans-serif;
      background: var(--bg);
      color: var(--text);
    }

    .app-header {
      background: var(--card);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .title {
      font-size: 1.5rem;
      font-weight: 700;
    }

    .switch-wrapper {
      display: flex;
      align-items: center;
      gap: .5rem;
    }

    .switch {
      position: relative;
      width: 60px;
      height: 30px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: var(--slot-border);
      border-radius: 30px;
    }

    .slider::before {
      content: "";
      position: absolute;
      height: 26px;
      width: 26px;
      left: 2px;
      bottom: 2px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider::before {
      transform: translateX(30px);
    }

    .calendar-frame {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: .5rem;
      padding: 1rem;
    }

    .day {
      background: var(--card);
      border-radius: 12px;
      padding: .5rem;
    }

    .day h5 {
      font-weight: 700;
      margin-bottom: .5rem;
    }

    .time-slot {
      display: block;
      border: 1px solid var(--slot-border);
      border-radius: 8px;
      padding: .4rem;
      margin-bottom: .4rem;
      text-align: center;
      cursor: pointer;
    }

    .time-slot.disabled {
      background: #f44336;
      color: #fff;
      cursor: not-allowed;
    }

    .time-slot:hover:not(.disabled) {
      background: #fff8cc;
    }

    .theme-dark .time-slot {
      background: #555;
      color: #fff;
    }

    .theme-dark .time-slot.disabled {
      background: #d32f2f;
      color: #fff;
    }

    .theme-dark .time-slot:hover:not(.disabled) {
      background: #3a4460;
    }

    .theme-dark .day {
      background: #1a2233;
    }

    .week-control {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 1rem;
    }

    .modal-header {
      background-color: var(--navbar-green);
      color: #fff;
    }

    .theme-dark .modal-content {
      background-color: #2a3246;
      color: #fff;
    }
  </style>
</head>

<body>
  <?php MostrarMenu(); ?>

  <header class="app-header">
    <h1 class="title">Agendar Cita</h1>
    <div class="switch-wrapper">
      <label class="switch">
        <input type="checkbox" id="toggleTheme">
        <span class="slider"></span>
      </label>
      <span id="themeLabel">Modo noche</span>
    </div>
  </header>

  <main class="container my-3">
    <?php if (!empty($mensajeExito))
      echo "<div class='alert alert-success'>$mensajeExito</div>"; ?>
    <?php if (!empty($mensajeError))
      echo "<div class='alert alert-danger'>$mensajeError</div>"; ?>

    <div class="week-control">
      <button class="btn btn-primary" id="prevWeek">← Semana anterior</button>
      <input type="date" id="datePicker">
      <button class="btn btn-primary" id="nextWeek">Semana siguiente →</button>
    </div>

    <section class="calendar-frame" id="calendarGrid"></section>
  </main>

  <!-- MODAL -->
  <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Datos del paciente</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="selectionPreview" class="alert alert-info">Sin selección</div>
          <form method="post" id="appointmentForm">
            <input type="hidden" name="date" id="selectedDate">
            <input type="hidden" name="time" id="selectedTime">

            <div class="mb-3">
              <label class="form-label">¿Para quién desea agendar?</label>
              <select id="appointmentType" class="form-select">
                <option value="self">Para mí</option>
                <option value="other">Para otra persona</option>
              </select>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Cédula</label>
                <input type="text" class="form-control" name="Cedula" required
                  value="<?= $usuarioLoggeado["Cedula"] ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="Nombre" required
                  value="<?= $usuarioLoggeado["Nombre"] ?>">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control" name="Apellido" required
                  value="<?= $usuarioLoggeado["Apellido"] ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Edad</label>
                <input type="number" class="form-control" name="edad" min="1" max="120" required>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="CorreoElectronico" required
                  value="<?= $usuarioLoggeado["CorreoElectronico"] ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" class="form-control" name="Telefono" required
                  value="<?= $usuarioLoggeado["Telefono"] ?>">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Mensaje (opcional)</label>
              <textarea class="form-control" name="message" rows="3"></textarea>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="submit" class="btn btn-primary">Agendar Cita</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php MostrarFooter(); ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const loggedUser = <?php echo json_encode($usuarioLoggeado); ?>;
    const citasExistentes = <?php echo json_encode($citasExistentes); ?>;
    const datePicker = document.getElementById("datePicker");

    function getMonday(d) {
      d = new Date(d); const day = d.getDay(); const diff = d.getDate() - day + (day === 0 ? -6 : 1);
      return new Date(d.setDate(diff));
    }
    const state = new Proxy({ currentMonday: getMonday(new Date()), dark: false, selectedDate: '', selectedTime: '' }, {
      set(t, p, v) { t[p] = v; if (p === 'currentMonday') renderWeek(v); if (p === 'dark') document.body.classList.toggle('theme-dark', v); return true; }
    });
    const grid = document.getElementById("calendarGrid");
    const modal = new bootstrap.Modal(document.getElementById("formModal"));

    function renderWeek(start) {
      datePicker.value = start.toISOString().split("T")[0];
      grid.innerHTML = "";
      const days = ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"];

      for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);

        const iso = d.toISOString().split("T")[0];
        const div = document.createElement("div");
        const dateToDisplay = d.toLocaleDateString();
        div.classList.add("day");

        div.innerHTML = `<h5>${days[i]}<br><small>${dateToDisplay}</small></h5>`;

        for (let h = 8; h <= 16; h++) {
          const time = h.toString().padStart(2, '0') + ":00";
          const full = iso + " " + time + ":00";
          const slot = document.createElement("div");
          slot.classList.add("time-slot"); slot.textContent = time;

          if (citasExistentes.includes(full)) slot.classList.add("disabled");

          slot.onclick = () => {
            if (slot.classList.contains("disabled")) return;
            document.querySelectorAll(".time-slot").forEach(s => s.classList.remove("selected"));

            slot.classList.add("selected");
            state.selectedDate = iso; state.selectedTime = time;

            document.getElementById("selectedDate").value = iso;
            document.getElementById("selectedTime").value = time;

            modal.show();
          }; div.appendChild(slot);
        } grid.appendChild(div);
      }
    }
    renderWeek(state.currentMonday);

    datePicker.onchange = e => {
      const selectedDate = new Date(e.target.value);
      state.currentMonday = getMonday(selectedDate);
      renderWeek(state.currentMonday);
    };
    document.getElementById("prevWeek").onclick = () => { state.currentMonday.setDate(state.currentMonday.getDate() - 7); renderWeek(state.currentMonday); }
    document.getElementById("nextWeek").onclick = () => { state.currentMonday.setDate(state.currentMonday.getDate() + 7); renderWeek(state.currentMonday); }
    document.getElementById("toggleTheme").onchange = e => { state.dark = e.target.checked; }

    const apotype = document.getElementById("appointmentType");
    const formModal = document.getElementById("formModal");
    apotype.addEventListener('change', event => {
      formModal.querySelectorAll('input, textarea').forEach(input => {
        if (event.target.value === 'self') {
          if (loggedUser && loggedUser[input.name]) {
            input.value = loggedUser[input.name];
          }
        } else {
          input.value = '';
        }
      });
    });
  </script>
</body>

</html>