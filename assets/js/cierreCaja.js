const CC_PATH = "../Controller/cierreCajaController.php";

let cierresGlobal = [];
let paginaCierresActual = 1;
const registrosCierresPorPagina = 10;

const num = (id) => {
    const el = document.getElementById(id);
    if (!el) return 0;

    return parseFloat(
        el.textContent.replace(/[^\d.-]/g, '')
    ) || 0;
};

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

/**
  Helper fetch(evita crash si backend devuelve HTML o error)
 */
async function fetchJSON(url, options) {
    const res = await fetch(url, options);
    const text = await res.text();

    let data;
    try {
        data = JSON.parse(text);
    } catch {
        console.error("Respuesta NO JSON:", text);
        throw new Error("Respuesta inválida del servidor");
    }

    if (!res.ok) {
        console.error("HTTP Error:", res.status, data);
        throw new Error(data?.error || "Error en servidor");
    }

    return data;
}

/**
 * =====================================================
 * RESUMEN
 * =====================================================
 */
async function cargarResumen() {
    try {
        const data = await fetchJSON(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "resumen" })
        });

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
        document.getElementById("cc-cobros").textContent = r.TotalCobrado || "0.00";

        document.getElementById("cc-efectivo").textContent = m.efectivo || "0.00";
        document.getElementById("cc-tarjeta").textContent = m.tarjeta || "0.00";
        document.getElementById("cc-sinpe").textContent = m.sinpe || "0.00";
        document.getElementById("cc-transferencia").textContent = m.transferencia || "0.00";
        document.getElementById("cc-efectivo-esperado").textContent = m.efectivo || "0.00";

    } catch (e) {
        console.error("Error cargando resumen:", e);
        mostrarAlerta("Error cargando resumen del día.");
    }
}

/**
 * =====================================================
 * DIFERENCIA
 * =====================================================
 */
function calcularDiferencia() {
    const esperado = num("cc-efectivo-esperado");
    const contado = parseFloat(this.value) || 0;

    document.getElementById("cc-diferencia").textContent =
        (contado - esperado).toFixed(2);
}

/**
 * =====================================================
 * CONFIRMACIÓN
 * =====================================================
 */
function mostrarConfirmacionCierre() {

    if (num("cc-total-facturado") === 0) {
        mostrarAlerta("No hay movimientos para cerrar la caja.");
        return;
    }

    new bootstrap.Modal(
        document.getElementById("modalConfirmarCierre")
    ).show();
}

/**
 * =====================================================
 * CERRAR CAJA
 * =====================================================
 */
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

        //  Fecha local correcta
        fecha: new Date().toLocaleDateString('sv-SE'),

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
        efectivoContado: parseFloat(
            document.getElementById("cc-efectivo-contado")?.value
        ) || 0,

        diferencia: num("cc-diferencia")
    };

    console.log(" Payload cierre:", payload);

    try {
        const data = await fetchJSON(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        if (data.error) {
            mostrarAlerta(data.error);
            return;
        }

        mostrarAlerta("Caja cerrada correctamente.");
        setTimeout(() => location.reload(), 800);

    } catch (e) {
        console.error("Error cerrando caja:", e);
        mostrarAlerta("Error al cerrar la caja.");
    }
}

/**
 * =====================================================
 * ALERTA
 * =====================================================
 */
function mostrarAlerta(msg) {
    const body = document.getElementById("modalAlertaCCBody");
    if (!body) return;

    body.textContent = msg;

    new bootstrap.Modal(
        document.getElementById("modalAlertaCC")
    ).show();
}

/**
 * =====================================================
 * HISTORIAL
 * =====================================================
 */
async function cargarHistorialCierres() {
    try {
        const data = await fetchJSON(CC_PATH, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "historial" })
        });

        cierresGlobal = Array.isArray(data) ? data : [];
        paginaCierresActual = 1;

        renderHistorialCierres();

    } catch (e) {
        console.error("Error cargando historial:", e);
        mostrarAlerta("Error cargando historial de cierres.");
    }
}
        function renderHistorialCierres() {
    const tbody = document.getElementById("tablaCierres");
    const contador = document.getElementById("cantidadCierres");

    const elTotal = document.getElementById("resumen-total");
    const elEfectivo = document.getElementById("resumen-efectivo");
    const elTarjeta = document.getElementById("resumen-tarjeta");
    const elSinpe = document.getElementById("resumen-sinpe");
    const elTransferencia = document.getElementById("resumen-transferencia");

    if (!tbody) return;

    const money = (val) => {
        return Number(val || 0).toLocaleString("es-CR", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    if (!cierresGlobal.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center text-muted py-4">
                    No hay cierres registrados
                </td>
            </tr>`;

        if (contador) contador.textContent = 0;
        if (elTotal) elTotal.textContent = "₡0.00";
        if (elEfectivo) elEfectivo.textContent = "₡0.00";
        if (elTarjeta) elTarjeta.textContent = "₡0.00";
        if (elSinpe) elSinpe.textContent = "₡0.00";
        if (elTransferencia) elTransferencia.textContent = "₡0.00";

        paginaCierresActual = 1;
        renderPaginacionCierres();
        return;
    }

    if (contador) contador.textContent = cierresGlobal.length;

    let total = 0;
    let efectivo = 0;
    let tarjeta = 0;
    let sinpe = 0;
    let transferencia = 0;

    cierresGlobal.forEach(c => {
        total += parseFloat(c.TotalCobrado) || 0;
        efectivo += parseFloat(c.Efectivo) || 0;
        tarjeta += parseFloat(c.Tarjeta) || 0;
        sinpe += parseFloat(c.Sinpe) || 0;
        transferencia += parseFloat(c.Transferencia) || 0;
    });

    const inicio = (paginaCierresActual - 1) * registrosCierresPorPagina;
    const fin = inicio + registrosCierresPorPagina;
    const cierresPagina = cierresGlobal.slice(inicio, fin);

    let html = "";

    cierresPagina.forEach(c => {
        const totalCobrado = parseFloat(c.TotalCobrado) || 0;
        const ef = parseFloat(c.Efectivo) || 0;
        const tj = parseFloat(c.Tarjeta) || 0;
        const sp = parseFloat(c.Sinpe) || 0;
        const tr = parseFloat(c.Transferencia) || 0;
        const contado = parseFloat(c.EfectivoContado) || 0;
        const diff = parseFloat(c.Diferencia) || 0;

        const diffClass =
            diff === 0 ? "monto-total" :
            diff > 0 ? "monto-positivo" :
            "monto-negativo";

        html += `
            <tr>
                <td>${c.Fecha}</td>
                <td><span class="cajero-name">${c.Cajero}</span></td>
                <td>${c.Facturas}</td>
                <td><span class="monto-pill monto-total">₡${money(totalCobrado)}</span></td>
                <td><span class="monto-pill monto-total">₡${money(ef)}</span></td>
                <td><span class="monto-pill monto-total">₡${money(tj)}</span></td>
                <td><span class="monto-pill monto-total">₡${money(sp)}</span></td>
                <td><span class="monto-pill monto-total">₡${money(tr)}</span></td>
                <td><span class="monto-pill monto-total">₡${money(contado)}</span></td>
                <td><span class="monto-pill ${diffClass}">₡${money(diff)}</span></td>
                <td>${c.HoraCierre}</td>
            </tr>
        `;
    });

    tbody.innerHTML = html;

    if (elTotal) elTotal.textContent = "₡" + money(total);
    if (elEfectivo) elEfectivo.textContent = "₡" + money(efectivo);
    if (elTarjeta) elTarjeta.textContent = "₡" + money(tarjeta);
    if (elSinpe) elSinpe.textContent = "₡" + money(sinpe);
    if (elTransferencia) elTransferencia.textContent = "₡" + money(transferencia);

    renderPaginacionCierres();
}

function renderPaginacionCierres() {
    const contenedor = document.getElementById("paginacionCierres");
    if (!contenedor) return;

    const totalPaginas = Math.ceil(cierresGlobal.length / registrosCierresPorPagina);

    if (paginaCierresActual > totalPaginas) {
        paginaCierresActual = totalPaginas || 1;
    }

    if (totalPaginas <= 1) {
        contenedor.innerHTML = "";
        return;
    }

    let html = `
        <li class="page-item ${paginaCierresActual <= 1 ? 'disabled' : ''}">
            <button class="page-link" type="button" data-pagina="${paginaCierresActual - 1}">
                ‹ Anterior
            </button>
        </li>
    `;

    for (let i = 1; i <= totalPaginas; i++) {
        html += `
            <li class="page-item ${i === paginaCierresActual ? 'active' : ''}">
                <button class="page-link" type="button" data-pagina="${i}">
                    ${i}
                </button>
            </li>
        `;
    }

    html += `
        <li class="page-item ${paginaCierresActual >= totalPaginas ? 'disabled' : ''}">
            <button class="page-link" type="button" data-pagina="${paginaCierresActual + 1}">
                Siguiente ›
            </button>
        </li>
    `;

    contenedor.innerHTML = html;

    contenedor.querySelectorAll("button[data-pagina]").forEach(btn => {
        btn.addEventListener("click", () => {
            const nuevaPagina = parseInt(btn.dataset.pagina);

            if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
                paginaCierresActual = nuevaPagina;
                renderHistorialCierres();
            }
        });
    });
}