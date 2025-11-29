const PACIENTE_CONTROLLER = "/OptiGestion/Controller/pacienteController.php";


async function buscarPaciente() {
    const cedula = document.getElementById('cedula').value.trim();
    const resultadoDiv = document.getElementById('resultado');
    const btnAgregar = document.getElementById('btnAgregarExpediente');
    const btnHistorial = document.getElementById('btnHistorial');

    btnAgregar.style.display = 'none';
    btnHistorial.style.display = 'none';
    resultadoDiv.innerHTML = '';

    if (!cedula) {
        alert("Por favor ingrese una cédula.");
        return;
    }

    try {
        const response = await fetch('../Controller/pacienteController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'cedula=' + encodeURIComponent(cedula)
        });

        const data = await response.json();

        if (data.error) {
            resultadoDiv.innerHTML = `
                <div class="alert alert-danger">${data.error}</div>
                <a href="RegistrarPaciente.php" class="btn btn-success mt-2">Registrar Paciente</a>
            `;
            return;
        }

        if (data.PacienteId) {
            resultadoDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>Nombre:</strong> ${data.nombre} ${data.apellido} ${data.apellidoDos}<br>
                    <strong>Teléfono:</strong> ${data.telefono ?? ''}<br>
                    <strong>Dirección:</strong> ${data.direccion ?? ''}
                </div>`;

            sessionStorage.setItem('paciente', JSON.stringify(data));

            btnAgregar.href = 'expedienteDigital.php';
            btnAgregar.style.display = 'block';

            btnHistorial.href = `../Controller/historialExpedientePacienteController.php?PacienteId=${data.PacienteId}`;
            btnHistorial.style.display = 'block';
        }

        else if (data.UsuarioId) {
            resultadoDiv.innerHTML = `<div class="alert alert-warning">Usuario registrado pero sin paciente asociado.</div>`;

            btnAgregar.onclick = async (e) => {
                e.preventDefault();

                const res = await fetch('../Controller/pacienteController.php?action=crearPaciente', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'UsuarioId=' + data.UsuarioId
                });

                const newData = await res.json();

                if (newData.success) {
                    window.location.href = 'expedienteDigital.php?PacienteId=' + newData.PacienteId;
                } else {
                    alert("Error al crear paciente.");
                }
            };

            btnAgregar.style.display = 'block';
        }

    } catch (err) {
        alert("Ocurrió un error al buscar el paciente.");
    }
}