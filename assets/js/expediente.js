//prueba expediente
const  PACIENTE_CONTROLLER= "/Controller/pacienteController.php";



function mostrarMensaje(mensaje, tipo = "info", extraHTML = "") {
    const contenedor = document.getElementById("mensajeSistema");

    contenedor.innerHTML = `
        <div class="mensaje-sistema mensaje-${tipo}">
            <div>
                ${mensaje}
                ${extraHTML}
            </div>
        </div>
    `;

    setTimeout(() => {
        if (contenedor) contenedor.innerHTML = "";
    }, 4000);
}

//solo para errores criticos
function mostrarModal(titulo, mensaje, tipo = "info") {
    mostrarMensaje(`<strong>${titulo}:</strong> ${mensaje}`, tipo);
}
/* ============================================
   BUSCAR PACIENTE
============================================ */

async function buscarPaciente() {
    const cedula = document.getElementById('cedula').value.trim();
    const resultadoDiv = document.getElementById('resultado');
    const btnAgregar = document.getElementById('btnAgregarExpediente');
    const btnHistorial = document.getElementById('btnHistorial');

    btnAgregar.style.display = 'none';
    btnHistorial.style.display = 'none';
    resultadoDiv.innerHTML = '';

    if (!cedula) {
        mostrarMensaje("Por favor ingrese una cédula.", "warning");
        return;
    }

    try {
        const response = await fetch(PACIENTE_CONTROLLER, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'cedula=' + encodeURIComponent(cedula)
        });

        if (!response.ok) {
            throw new Error("Error HTTP: " + response.status);
        }

        const text = await response.text();

        let data;
        try {
            data = JSON.parse(text);
        } catch {
            console.error("Respuesta inválida:", text);
            throw new Error("Respuesta no es JSON válido");
        }

        //si paciente no existe
        if (data.error) {

            mostrarMensaje(
                data.error,
                "error",
                `
                <div class="mt-3">
                    <a href="RegistrarPaciente.php" class="btn btn-success w-100">
                        Registrar Paciente
                    </a>
                </div>
                `
            );
            return;
        }

        /* =============================
           CASO: PACIENTE EXISTE
        ============================= */
        if (data.PacienteId) {

             mostrarMensaje("Paciente encontrado correctamente.", "success");

            resultadoDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>Nombre:</strong> ${data.nombre} ${data.apellido} ${data.apellidoDos}<br>
                    <strong>Teléfono:</strong> ${data.telefono ?? ''}<br>
                    <strong>Dirección:</strong> ${data.direccion ?? ''}
                </div>`;

            sessionStorage.setItem('paciente', JSON.stringify(data));

            btnAgregar.href = '/View/expedienteDigital.php';
            btnAgregar.style.display = 'block';

            btnHistorial.href = `/Controller/historialExpedientePacienteController.php?PacienteId=${data.PacienteId}`;
            btnHistorial.style.display = 'block';
 return;
        }

      //usuario sin paciente
        else if (data.UsuarioId) {

           mostrarMensaje(
                "El usuario existe pero no tiene expediente. Puede crearlo.",
                "warning"
            );

            btnAgregar.onclick = async (e) => {
                e.preventDefault();

                try {
                    const res = await fetch(`${PACIENTE_CONTROLLER}?action=crearPaciente`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'UsuarioId=' + data.UsuarioId
                    });

                    const newData = await res.json();

                    if (newData.success) {
                        window.location.href = `expedienteDigital.php?PacienteId=${newData.PacienteId}`;
                    } else {
                        mostrarModal("Error", "No se pudo crear el paciente.", "danger");
                    }

                } catch (error) {
                    mostrarModal("Error", "Error creando paciente.", "danger");
                }
            };

            btnAgregar.style.display = 'block';
                return;
        }

        //sin resultado
        else {
                mostrarMensaje("No se encontró información.", "warning");
        }

    } catch (err) {
        console.error(err);

        mostrarMensaje(
            "Ocurrió un error al buscar el paciente. Intente nuevamente.",
            "error"
        );
    }

}