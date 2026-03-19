//prueba expediente
const  PACIENTE_CONTROLLER= "/Controller/pacienteController.php";


/* ============================================
   MODAL HELPERS (Bootstrap 5)
============================================ */

function mostrarModal(titulo, mensaje, tipo = "info") {
    const modalHtml = `
        <div class="modal fade" id="modalPaciente" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-${tipo}">
                    <div class="modal-header bg-${tipo} text-white">
                        <h5 class="modal-title">${titulo}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${mensaje}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML("beforeend", modalHtml);

    const modal = new bootstrap.Modal(document.getElementById("modalPaciente"));
    modal.show();

    document.getElementById("modalPaciente").addEventListener("hidden.bs.modal", () => {
        document.getElementById("modalPaciente").remove();
    });
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
        mostrarModal("Campo requerido", "Por favor ingrese una cédula.", "warning");
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

        /* =============================
           CASO: ERROR CONTROLADO
        ============================= */
        if (data.error) {
            mostrarModal(
                "Paciente no encontrado",
                `
                ${data.error}<br><br>
                <a href="RegistrarPaciente.php" class="btn btn-success w-100">
                    Registrar Paciente
                </a>
                `,
                "danger"
            );
            return;
        }

        /* =============================
           CASO: PACIENTE EXISTE
        ============================= */
        if (data.PacienteId) {

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

            mostrarModal(
                "Paciente encontrado",
                `${data.nombre} ${data.apellido}`,
                "success"
            );
        }

        /* =============================
           CASO: USUARIO SIN PACIENTE
        ============================= */
        else if (data.UsuarioId) {

            mostrarModal(
                "Usuario encontrado",
                "Existe el usuario pero no tiene expediente. ¿Desea crearlo?",
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
        }

        /* =============================
           CASO: RESPUESTA VACÍA
        ============================= */
        else {
            mostrarModal("Sin resultados", "No se encontró información.", "warning");
        }

    } catch (err) {
        console.error(err);

        mostrarModal(
            "Error del sistema",
            "Ocurrió un error al buscar el paciente. Por favor intente nuevamente.",
            "danger"
        );
    }
}