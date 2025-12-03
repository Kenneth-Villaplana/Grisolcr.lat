
const CONTROLLER_PATH = "/OptiGestion/Controller/FacturacionController.php";

document.addEventListener("DOMContentLoaded", () => {

    cargarFacturas();

    document.getElementById("btnBuscar").addEventListener("click", buscarFacturas);
    document.getElementById("btnLimpiar").addEventListener("click", limpiarFiltros);

 
    document.getElementById("codigoInput").addEventListener("keyup", (e) => {
        if (e.key === "Enter") buscarFacturas();
    });

    document.getElementById("cedulaInput").addEventListener("keyup", (e) => {
        if (e.key === "Enter") buscarFacturas();
    });
});



async function cargarFacturas(filtro = {}) {

    const body = document.getElementById("facturas-body");
    body.innerHTML = `
        <tr>
            <td colspan="9" class="text-center text-muted py-4">
                Cargando facturas...
            </td>
        </tr>
    `;

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

    if (!facturas.length) {
        body.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    No se encontraron facturas.
                </td>
            </tr>
        `;
        return;
    }

    facturas.forEach(f => agregarFilaFactura(f));
}


function agregarFilaFactura(f) {

    const total = Number(f.Total);
    const pendiente = Number(f.Saldo_Pendiente);

    const estadoHTML =
        pendiente > 0
            ? `<span class="badge-estado badge-pendiente">Pendiente</span>`
            : `<span class="badge-estado badge-pagada">Pagada</span>`;

    const botonAbono =
        pendiente > 0
            ? `<button class="btn btn-outline-secondary btn-sm" onclick="abrirAbono(${f.FacturaId}, ${pendiente})">
                Abonar
            </button>`
            : "";

    const fila = `
        <tr>
            <td>${f.FacturaId}</td>
            <td>${f.Fecha}</td>
            <td>${f.Cedula || "-"}</td>
            <td>${f.NombreCliente || "-"}</td>
            <td>${f.Telefono || "-"}</td>
            <td>${f.Productos}</td>

            <td class="text-end fw-semibold">₡${total.toLocaleString()}</td>
            <td class="text-end">${pendiente > 0 ? "₡" + pendiente.toLocaleString() : "₡0"}</td>

            <td class="text-center">${estadoHTML}</td>

            <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="verFactura(${f.FacturaId})">
                        Ver
                    </button>
                    ${botonAbono}
                </div>
            </td>
        </tr>
    `;

    document.getElementById("facturas-body")
        .insertAdjacentHTML("beforeend", fila);
}


function buscarFacturas() {

    let num = document.getElementById("codigoInput").value.trim();
    let ced = document.getElementById("cedulaInput").value.trim();

    cargarFacturas({
        numero: num || null,
        cedula: ced || null
    });
}


function limpiarFiltros() {
    document.getElementById("codigoInput").value = "";
    document.getElementById("cedulaInput").value = "";

    cargarFacturas({ numero: null, cedula: null });
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
    const detalle = data.detalle;

    const modalBody = document.getElementById("facturaContenido");

    modalBody.innerHTML = `
        <h4>Factura #${enc.Id}</h4>
        <p><strong>Fecha:</strong> ${enc.Fecha}</p>
        <p><strong>Cliente:</strong> ${enc.Cliente || "-"}</p>
        <p><strong>Telefono:</strong> ${enc.Telefono || "-"}</p>

        <hr>

        <p><strong>Total original:</strong> ₡${Number(enc.Total).toLocaleString()}</p>
        <p><strong>Abonado:</strong> ₡${Number(enc.Abonado).toLocaleString()}</p>
        <p><strong>Saldo pendiente:</strong> ₡${Number(enc.Pendiente).toLocaleString()}</p>

        <hr>
        <h5>Productos</h5>
        <ul>
            ${detalle.map(d => `
                <li>${d.Cantidad}x ${d.Nombre} — ₡${Number(d.Total).toLocaleString()}</li>
            `).join("")}
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
    const saldo = parseFloat(document.getElementById("abonoSaldo").value);
    const monto = parseFloat(document.getElementById("abonoMonto").value);

    if (!monto || monto <= 0) {
        alert("Ingrese un monto válido.");
        return;
    }
    if (monto > saldo) {
        alert("El abono no puede ser mayor al saldo.");
        return;
    }

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

        mostrarReciboAbono(facturaId, monto);

        cargarFacturas();
    }
}


async function mostrarReciboAbono(facturaId, montoAbonado) {

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
    const detalle = data.detalle;

    let ticketDetalle = detalle.map(d => `
        <tr>
            <td>${d.Nombre}</td>
            <td style="text-align:center;">${d.Cantidad}</td>
             <td style="text-align:center;">${d.Descuento}%</td>
            <td style="text-align:right;">₡${Number(d.Total).toLocaleString()}</td>
        </tr>
    `).join("");

    const html = `
        <div id="ticketAbono" style="font-family: monospace; padding: 5px; font-size:13px;">

            <h4 style="text-align:center; margin:0; font-weight:bold;">Óptica Grisol</h4>
            <div style="text-align:center;">Recibo de Abono</div>
            <hr>

            <strong>Factura #:</strong> ${f.Id}<br>
            <strong>Fecha:</strong> ${f.Fecha}<br>
            <strong>Cliente:</strong> ${f.Cliente || "-"}<br>
            <strong>Telefono:</strong> ${f.Telefono || "-"}<br>
            <hr>

                <table style="width:100%; font-size:12px; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;">Producto</th>
                        <th>Cant</th>
                        <th>Desc</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${ticketDetalle}
                </tbody>
            </table>

            <hr>
            <strong>Total factura:</strong> ₡${Number(f.Total).toLocaleString()}<br>
            <strong>Abono actual:</strong> ₡${Number(montoAbonado).toLocaleString()}<br>
            <strong>Total abonado:</strong> ₡${Number(f.Abonado).toLocaleString()}<br>
            <strong>Pendiente:</strong> ₡${Number(f.Pendiente).toLocaleString()}<br>

            <p style="text-align:center;">¡Gracias por su pago!</p>
        </div>
    `;

    document.getElementById("reciboAbonoBody").innerHTML = html;

    new bootstrap.Modal(document.getElementById("modalReciboAbono")).show();
}


function imprimirReciboAbono() {
    const contenido = document.getElementById("ticketAbono").outerHTML;
    const ventana = window.open("", "_blank", "width=300,height=600");

    ventana.document.write(`
        <html>
            <head>
                <style>
                    body { 
                        font-family: monospace; 
                        margin: 0; 
                        padding: 10px;
                        font-size: 13px;
                    }
                    table { width:100%; border-collapse: collapse; }
                    td, th { padding: 2px 0; }
                </style>
            </head>
            <body>${contenido}</body>
        </html>
    `);

    ventana.document.close();
    ventana.focus();
    ventana.print();
}