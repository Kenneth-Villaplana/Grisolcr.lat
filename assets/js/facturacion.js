const CONTROLLER_PATH = "/Controller/facturacionController.php";

document.addEventListener("DOMContentLoaded", () => {

    cargarFacturas();

    document.getElementById("btnBuscar")?.addEventListener("click", buscarFacturas);
    document.getElementById("btnLimpiar")?.addEventListener("click", limpiarFiltros);

    document.getElementById("codigoInput")?.addEventListener("keyup", (e) => {
        if (e.key === "Enter") buscarFacturas();
    });

    document.getElementById("cedulaInput")?.addEventListener("keyup", (e) => {
        if (e.key === "Enter") buscarFacturas();
    });

});

async function cargarFacturas(filtro = {}) {

    const body = document.getElementById("facturas-body");
    if (!body) return;

    body.innerHTML = `
        <tr>
            <td colspan="10" class="text-center text-muted py-4">
                Cargando facturas...
            </td>
        </tr>
    `;

    try {

        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "obtenerFacturas",
                ...filtro
            })
        });

        const facturas = await res.json();

        body.innerHTML = "";

        if (!Array.isArray(facturas) || facturas.length === 0) {

            body.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        No se encontraron facturas.
                    </td>
                </tr>
            `;

            return;
        }

        facturas.forEach(f => agregarFilaFactura(f));

    } catch (error) {

        console.error(error);

        body.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-danger py-4">
                    Error al cargar facturas
                </td>
            </tr>
        `;
    }
}

function agregarFilaFactura(f) {

    const body = document.getElementById("facturas-body");

    const total = Number(f.Total || 0);
    const pendiente = Number(f.Saldo_Pendiente || f.Pendiente || 0);

    const estadoHTML =
        pendiente > 0
            ? `<span class="badge-estado badge-pendiente">Pendiente</span>`
            : `<span class="badge-estado badge-pagada">Pagada</span>`;

    const botonAbono =
        pendiente > 0
            ? `<button class="btn btn-outline-primary btn-sm"
                    onclick="abrirAbono(${f.FacturaId}, ${pendiente})">
                    Abonar
               </button>`
            : "";

    const fila = `
        <tr>
            <td>${f.FacturaId || "-"}</td>
            <td>${f.Fecha || "-"}</td>
            <td>${f.Cedula || "-"}</td>
            <td>${f.NombreCliente || f.Cliente || "-"}</td>
            <td>${f.Telefono || "-"}</td>
            <td>${f.Productos || "-"}</td>
            <td class="text-end fw-semibold">₡${total.toLocaleString()}</td>
            <td class="text-end">₡${pendiente.toLocaleString()}</td>
            <td class="text-center">${estadoHTML}</td>
            <td class="text-center">
                <button class="btn btn-outline-primary btn-sm"
                    onclick="verFactura(${f.FacturaId})">
                    Ver
                </button>
                ${botonAbono}
            </td>
        </tr>
    `;

    body.insertAdjacentHTML("beforeend", fila);
}

function buscarFacturas() {

    const numero = document.getElementById("codigoInput")?.value || null;
    const cedula = document.getElementById("cedulaInput")?.value || null;

    cargarFacturas({
        numero,
        cedula
    });
}

function limpiarFiltros() {

    document.getElementById("codigoInput").value = "";
    document.getElementById("cedulaInput").value = "";

    cargarFacturas();
}

async function verFactura(id) {

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "obtenerFacturaCompleta",
            facturaId: id
        })
    });

    const data = await res.json();

    const enc = data.encabezado;
    const detalle = data.detalle || [];

    const modalBody = document.getElementById("facturaContenido");

    modalBody.innerHTML = `
        <h4>Factura #${enc.Id}</h4>
        <p><strong>Fecha:</strong> ${enc.Fecha}</p>
        <p><strong>Cliente:</strong> ${enc.Cliente}</p>
        <p><strong>Telefono:</strong> ${enc.Telefono}</p>

        <hr>

        <p><strong>Total:</strong> ₡${Number(enc.Total).toLocaleString()}</p>
        <p><strong>Abonado:</strong> ₡${Number(enc.Abonado || 0).toLocaleString()}</p>
        <p><strong>Pendiente:</strong> ₡${Number(enc.Pendiente || 0).toLocaleString()}</p>

        <hr>

        <h5>Productos</h5>
        <ul>
            ${detalle.map(d =>
                `<li>${d.Cantidad}x ${d.Nombre} — ₡${Number(d.Total).toLocaleString()}</li>`
            ).join("")}
        </ul>
    `;

    new bootstrap.Modal(document.getElementById("modalFactura")).show();
}

function abrirAbono(facturaId, saldo) {

    document.getElementById("abonoFacturaId").value = facturaId;
    document.getElementById("abonoSaldo").value = saldo;
    document.getElementById("abonoMonto").value = "";

    new bootstrap.Modal(document.getElementById("modalAbono")).show();
}

async function guardarAbono() {

    const facturaId = document.getElementById("abonoFacturaId").value;
    const monto = parseFloat(document.getElementById("abonoMonto").value);

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "registrarAbono",
            facturaId,
            monto
        })
    });

    const result = await res.json();

    if (result.success) {

        document.querySelector("#modalAbono .btn-close").click();

        await mostrarReciboAbono(facturaId, monto);

        cargarFacturas();
    }
}

async function mostrarReciboAbono(facturaId, monto) {

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "obtenerFacturaCompleta",
            facturaId
        })
    });

    const data = await res.json();
    const f = data.encabezado;

    const html = `
        <div id="ticketAbono" style="font-family: monospace;">
            <h4 style="text-align:center;">Óptica Grisol</h4>
            <div style="text-align:center;">Recibo de Abono</div>
            <hr>

            Factura: ${f.Id}<br>
            Fecha: ${f.Fecha}<br>
            Cliente: ${f.Cliente}<br>

            <hr>

            Total: ₡${Number(f.Total).toLocaleString()}<br>
            Abono: ₡${Number(monto).toLocaleString()}<br>
            Pendiente: ₡${Number(f.Pendiente).toLocaleString()}
        </div>
    `;

    document.getElementById("reciboAbonoBody").innerHTML = html;

    new bootstrap.Modal(document.getElementById("modalReciboAbono")).show();
}