const CONTROLLER_PATH = "/Controller/facturacionController.php";

document.addEventListener("DOMContentLoaded", () => {
    const btnBuscar = document.getElementById("btnBuscar");
    const btnLimpiar = document.getElementById("btnLimpiar");
    const codigoInput = document.getElementById("codigoInput");
    const cedulaInput = document.getElementById("cedulaInput");

    cargarFacturas();

    if (btnBuscar) {
        btnBuscar.addEventListener("click", buscarFacturas);
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", limpiarFiltros);
    }

    if (codigoInput) {
        codigoInput.addEventListener("keyup", (e) => {
            if (e.key === "Enter") buscarFacturas();
        });
    }

    if (cedulaInput) {
        cedulaInput.addEventListener("keyup", (e) => {
            if (e.key === "Enter") buscarFacturas();
        });
    }
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

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const text = await res.text();

        let facturas = [];
        try {
            facturas = JSON.parse(text);
        } catch (e) {
            console.error("Respuesta no válida del controller:", text);
            throw new Error("El controller no devolvió JSON válido.");
        }

        console.log("Facturas recibidas:", facturas);

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
        console.error("Error al cargar facturas:", error);

        body.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-danger py-4">
                    Error al cargar las facturas.
                </td>
            </tr>
        `;
    }
}

function agregarFilaFactura(f) {
    const body = document.getElementById("facturas-body");
    if (!body) return;

    const total = Number(f.Total ?? 0);
    const pendiente = Number(f.Saldo_Pendiente ?? 0);

    const estadoHTML =
        pendiente > 0
            ? `<span class="badge-estado badge-pendiente">Pendiente</span>`
            : `<span class="badge-estado badge-pagada">Pagada</span>`;

    const botonAbono =
        pendiente > 0
            ? `<button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                    onclick="abrirAbono(${Number(f.FacturaId)}, ${pendiente})">
                    Abonar
               </button>`
            : "";

    const fila = `
        <tr>
            <td>${f.FacturaId ?? "-"}</td>
            <td>${f.Fecha ?? "-"}</td>
            <td>${f.Cedula ?? "-"}</td>
            <td>${f.NombreCliente ?? "-"}</td>
            <td>${f.Telefono ?? "-"}</td>
            <td>${f.Productos ?? "-"}</td>
            <td class="text-end fw-semibold">₡${total.toLocaleString()}</td>
            <td class="text-end">₡${pendiente.toLocaleString()}</td>
            <td class="text-center">${estadoHTML}</td>
            <td class="text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="verFactura(${Number(f.FacturaId)})">
                        Ver
                    </button>
                    ${botonAbono}
                </div>
            </td>
        </tr>
    `;

    body.insertAdjacentHTML("beforeend", fila);
}

function buscarFacturas() {
    const num = document.getElementById("codigoInput")?.value.trim() || null;
    const ced = document.getElementById("cedulaInput")?.value.trim() || null;

    cargarFacturas({
        numero: num,
        cedula: ced
    });
}

function limpiarFiltros() {
    const codigoInput = document.getElementById("codigoInput");
    const cedulaInput = document.getElementById("cedulaInput");

    if (codigoInput) codigoInput.value = "";
    if (cedulaInput) cedulaInput.value = "";

    cargarFacturas({
        numero: null,
        cedula: null
    });
}

async function verFactura(id) {
    try {
        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "obtenerFacturaCompleta",
                facturaId: id
            })
        });

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const data = await res.json();
        console.log("Factura completa:", data);

        if (!data || !data.encabezado) {
            throw new Error("No se encontró la factura.");
        }

        const enc = data.encabezado;
        const detalle = Array.isArray(data.detalle) ? data.detalle : [];

        const modalBody = document.getElementById("facturaContenido");
        if (!modalBody) return;

        modalBody.innerHTML = `
            <h4>Factura #${enc.FacturaId ?? enc.Id ?? "-"}</h4>
            <p><strong>Fecha:</strong> ${enc.Fecha ?? "-"}</p>
            <p><strong>Cliente:</strong> ${enc.Cliente ?? enc.NombreCliente ?? "-"}</p>
            <p><strong>Telefono:</strong> ${enc.Telefono ?? "-"}</p>

            <hr>

            <p><strong>Total original:</strong> ₡${Number(enc.Total ?? 0).toLocaleString()}</p>
            <p><strong>Abonado:</strong> ₡${Number(enc.Abonado ?? 0).toLocaleString()}</p>
            <p><strong>Saldo pendiente:</strong> ₡${Number(enc.Pendiente ?? enc.Saldo_Pendiente ?? 0).toLocaleString()}</p>

            <hr>
            <h5>Productos</h5>
            <ul>
                ${detalle.map(d => `
                    <li>${d.Cantidad ?? 0}x ${d.Nombre ?? "-"} — ₡${Number(d.Total ?? 0).toLocaleString()}</li>
                `).join("")}
            </ul>
        `;

        new bootstrap.Modal(document.getElementById("modalFactura")).show();

    } catch (error) {
        console.error("Error al obtener factura completa:", error);
        alert("No se pudo cargar la factura.");
    }
}

function abrirAbono(facturaId, saldo) {
    const abonoFacturaId = document.getElementById("abonoFacturaId");
    const abonoSaldo = document.getElementById("abonoSaldo");
    const abonoMonto = document.getElementById("abonoMonto");

    if (abonoFacturaId) abonoFacturaId.value = facturaId;
    if (abonoSaldo) abonoSaldo.value = saldo;
    if (abonoMonto) abonoMonto.value = "";

    new bootstrap.Modal(document.getElementById("modalAbono")).show();
}

async function guardarAbono() {
    try {
        const facturaId = document.getElementById("abonoFacturaId")?.value;
        const saldo = parseFloat(document.getElementById("abonoSaldo")?.value || 0);
        const monto = parseFloat(document.getElementById("abonoMonto")?.value || 0);

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

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const result = await res.json();
        console.log("Resultado abono:", result);

        if (result.success) {
            document.querySelector("#modalAbono .btn-close")?.click();
            await mostrarReciboAbono(facturaId, monto);
            await cargarFacturas();
        } else {
            alert(result.error || "No se pudo registrar el abono.");
        }

    } catch (error) {
        console.error("Error al registrar abono:", error);
        alert("Ocurrió un error al registrar el abono.");
    }
}

async function mostrarReciboAbono(facturaId, montoAbonado) {
    try {
        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "obtenerFacturaCompleta",
                facturaId
            })
        });

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}`);
        }

        const data = await res.json();
        const f = data.encabezado;
        const detalle = Array.isArray(data.detalle) ? data.detalle : [];

        if (!f) {
            throw new Error("No se pudo obtener la factura para el recibo.");
        }

        let ticketDetalle = detalle.map(d => `
            <tr>
                <td>${d.Nombre ?? "-"}</td>
                <td style="text-align:center;">${d.Cantidad ?? 0}</td>
                <td style="text-align:center;">${d.Descuento ?? 0}%</td>
                <td style="text-align:right;">₡${Number(d.Total ?? 0).toLocaleString()}</td>
            </tr>
        `).join("");

        const html = `
            <div id="ticketAbono" style="font-family: monospace; padding: 5px; font-size:13px;">
                <h4 style="text-align:center; margin:0; font-weight:bold;">Óptica Grisol</h4>
                <div style="text-align:center;">Recibo de Abono</div>
                <hr>
                <strong>Factura #:</strong> ${f.FacturaId ?? f.Id ?? "-"}<br>
                <strong>Fecha:</strong> ${f.Fecha ?? "-"}<br>
                <strong>Cliente:</strong> ${f.Cliente ?? f.NombreCliente ?? "-"}<br>
                <strong>Telefono:</strong> ${f.Telefono ?? "-"}<br>
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
                <strong>Total factura:</strong> ₡${Number(f.Total ?? 0).toLocaleString()}<br>
                <strong>Abono actual:</strong> ₡${Number(montoAbonado ?? 0).toLocaleString()}<br>
                <strong>Total abonado:</strong> ₡${Number(f.Abonado ?? 0).toLocaleString()}<br>
                <strong>Pendiente:</strong> ₡${Number(f.Pendiente ?? f.Saldo_Pendiente ?? 0).toLocaleString()}<br>
                <p style="text-align:center;">¡Gracias por su pago!</p>
            </div>
        `;

        document.getElementById("reciboAbonoBody").innerHTML = html;
        new bootstrap.Modal(document.getElementById("modalReciboAbono")).show();

    } catch (error) {
        console.error("Error al mostrar recibo de abono:", error);
        alert("No se pudo generar el recibo.");
    }
}

function imprimirReciboAbono() {
    const ticket = document.getElementById("ticketAbono");
    if (!ticket) return;

    const contenido = ticket.outerHTML;
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