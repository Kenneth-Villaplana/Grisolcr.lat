const CC_PATH = "../Controller/cierreCajaController.php";

/**
 * Helper para convertir texto a número
 */
const num = (id) =>
    parseFloat(document.getElementById(id)?.textContent) || 0;

document.addEventListener("DOMContentLoaded", () => {

    // Cargar resumen solo si existe la sección
    if (document.getElementById("cc-total")) {
        cargarResumen();
    }

    const efectivoContado = document.getElementById("cc-efectivo-contado");
    if (efectivoContado) {
        efectivoContado.addEventListener("input", calcularDiferencia);
    }

    const btnCerrarCaja = document.getElementById("btnCerrarCaja");
    if (btnCerrarCaja) {
        btnCerrarCaja.addEventListener("click", mostrarConfirmacionCierre);
    }

    const btnConfirmar = document.getElementById("btnConfirmarCierre");
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", cerrarCaja);
    }

    // Historial
    if (document.getElementById("tablaCierres")) {
        cargarHistorialCierres();
    }
});

async function cargarResumen() {
    try {
        const res = await fetch(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "resumen" })
        });

        const data = await res.json();
        const r = data.resumen || {};
        const m = data.metodos || {};

        document.getElementById("cc-fecha").textContent =
            new Date().toLocaleDateString();

        document.getElementById("cc-cantidad").textContent = r.Facturas || 0;
        document.getElementById("cc-subtotal").textContent = r.Subtotal || "0.00";
        document.getElementById("cc-descuento").textContent = r.Descuento || "0.00";
        document.getElementById("cc-iva").textContent = r.IVA || "0.00";
        document.getElementById("cc-total").textContent = r.TotalFacturado || "0.00";
        document.getElementById("cc-total-facturado").textContent = r.TotalFacturado || "0.00";
        document.getElementById("cc-total-cobrado").textContent = r.TotalCobrado || "0.00";

        document.getElementById("cc-efectivo").textContent = m.efectivo || "0.00";
        document.getElementById("cc-tarjeta").textContent = m.tarjeta || "0.00";
        document.getElementById("cc-sinpe").textContent = m.sinpe || "0.00";
        document.getElementById("cc-transferencia").textContent = m.transferencia || "0.00";
        document.getElementById("cc-efectivo-esperado").textContent = m.efectivo || "0.00";

    } catch (e) {
        console.error("Error cargando resumen", e);
    }
}

function calcularDiferencia() {
    const esperado = num("cc-efectivo-esperado");
    const contado = parseFloat(this.value) || 0;
    document.getElementById("cc-diferencia").textContent =
        (contado - esperado).toFixed(2);
}

function mostrarConfirmacionCierre() {

    if (num("cc-total-facturado") === 0) {
        mostrarAlerta("No hay movimientos para cerrar la caja.");
        return;
    }

    new bootstrap.Modal(
        document.getElementById("modalConfirmarCierre")
    ).show();
}

async function cerrarCaja() {
    const modalEl = document.getElementById("modalConfirmarCierre");
    const modal = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
    if (modal) modal.hide();

    if (num("cc-total-facturado") === 0) {
        mostrarAlerta("No hay movimientos para cerrar la caja.");
        return;
    }

    const payload = {
        action: "cerrar",
        fecha: new Date().toISOString().slice(0, 10),
        facturas: parseInt(num("cc-cantidad")),
        subtotal: num("cc-subtotal"),
        descuento: num("cc-descuento"),
        iva: num("cc-iva"),
        totalFacturado: num("cc-total-facturado"),
        totalCobrado: num("cc-total-cobrado"),
        efectivo: num("cc-efectivo"),
        tarjeta: num("cc-tarjeta"),
        sinpe: num("cc-sinpe"),
        transferencia: num("cc-transferencia"),
        efectivoEsperado: num("cc-efectivo-esperado"),
        efectivoContado: parseFloat(document.getElementById("cc-efectivo-contado")?.value) || 0,
        diferencia: num("cc-diferencia")
    };

    try {
        const res = await fetch(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.error) {
            mostrarAlerta(data.error);
            return;
        }

        mostrarAlerta("Caja cerrada correctamente.");
        setTimeout(() => location.reload(), 800);

    } catch (e) {
        console.error("Error cerrando caja", e);
        mostrarAlerta("Error al cerrar la caja.");
    }
}

function mostrarAlerta(msg) {
    const body = document.getElementById("modalAlertaCCBody");
    if (!body) return;

    body.textContent = msg;
    new bootstrap.Modal(
        document.getElementById("modalAlertaCC")
    ).show();
}

async function cargarHistorialCierres() {
    try {
        const res = await fetch(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "historial" })
        });

        const data = await res.json();
        const tbody = document.getElementById("tablaCierres");
        const contador = document.getElementById("cantidadCierres");

        if (!tbody) return;

        if (!data.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        No hay cierres registrados
                    </td>
                </tr>`;
            if (contador) contador.textContent = 0;
            return;
        }

        tbody.innerHTML = "";
        if (contador) contador.textContent = data.length;

        data.forEach(c => {
            const diffClass =
                parseFloat(c.Diferencia) === 0
                    ? "monto-total"
                    : parseFloat(c.Diferencia) > 0
                        ? "monto-positivo"
                        : "monto-negativo";

            tbody.innerHTML += `
                <tr>
                    <td>${c.Fecha}</td>

                    <td>
                        <span class="cajero-name">
                            ${c.Cajero}
                        </span>
                    </td>

                    <td>${c.Facturas}</td>

                    <td>
                        <span class="monto-pill monto-total">
                            ₡${c.TotalCobrado}
                        </span>
                    </td>

                    <td><span class="monto-pill monto-total">₡${c.Efectivo}</span></td>
                    <td><span class="monto-pill monto-total">₡${c.Tarjeta}</span></td>
                    <td><span class="monto-pill monto-total">₡${c.Sinpe}</span></td>
                    <td><span class="monto-pill monto-total">₡${c.Transferencia}</span></td>

                    <td>
                        <span class="monto-pill monto-total">
                            ₡${c.EfectivoContado}
                        </span>
                    </td>

                    <td>
                        <span class="monto-pill ${diffClass}">
                            ₡${c.Diferencia}
                        </span>
                    </td>

                    <td>${c.HoraCierre}</td>
                </tr>
            `;
        });

    } catch (e) {
        console.error("Error cargando historial de cierres", e);
    }
}

