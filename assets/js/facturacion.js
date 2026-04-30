let facturasGlobal = [];
let paginaActualFacturas = 1;
const facturasPorPagina = 5;

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
            <td colspan="9" class="text-center text-muted py-4">
                Cargando facturas...
            </td>
        </tr>
    `;

    try {

const facturas = await res.json();
body.innerHTML = "";

if (!Array.isArray(facturas) || facturas.length === 0) {
    facturasGlobal = [];
    renderizarPaginacionFacturas();

    body.innerHTML = `
        <tr>
            <td colspan="10" class="text-center text-muted py-4">
                No se encontraron facturas.
            </td>
        </tr>
    `;
    return;
}

facturasGlobal = facturas;
paginaActualFacturas = 1;

mostrarFacturasPaginadas();
renderizarPaginacionFacturas();

    } catch (error) {

        console.error("Error cargando facturas:", error);

        body.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-danger py-4">
                    Error al cargar facturas
                </td>
            </tr>
        `;
    }
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
            ? `<button class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                    onclick="abrirAbono(${f.FacturaId}, ${pendiente})">
                <i class=""></i> Abonar
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
    try {

        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "obtenerFacturaCompleta",
                facturaId: id
            })
        });

        const data = await res.json();
        if (!data) return;

        const enc = data.encabezado;
        const detalle = data.detalle || [];

        const modalBody = document.getElementById("facturaContenido");
        if (!modalBody) return;

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

    } catch (error) {
        console.error("Error viendo factura:", error);
        alert("Error al cargar la factura");
    }
}

function abrirAbono(facturaId, saldo) {

    document.getElementById("abonoFacturaId").value = facturaId;
    document.getElementById("abonoSaldo").value = saldo;

    document.getElementById("abonoMonto").value = "";

    new bootstrap.Modal(document.getElementById("modalAbono")).show();
}


async function guardarAbono() {
    try {

        const facturaId = document.getElementById("abonoFacturaId")?.value;
        const saldo = parseFloat(document.getElementById("abonoSaldo")?.value);
        const monto = parseFloat(document.getElementById("abonoMonto")?.value);

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
            document.querySelector("#modalAbono .btn-close")?.click();

            mostrarReciboAbono(facturaId, monto);
            cargarFacturas();
        }

    } catch (error) {
        console.error("Error guardando abono:", error);
        alert("Error al registrar el abono");
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

        const data = await res.json();
        console.log("DATA RECIBO:", data);

        if (!data || !data.encabezado) {
            throw new Error("No se recibió información de la factura");
        }

        const f = data.encabezado;
        const detalle = Array.isArray(data.detalle) ? data.detalle : [];

        let ticketDetalle = detalle.map(d => `
            <tr>
                <td>${d.Nombre ?? "-"}</td>
                <td style="text-align:center;">${d.Cantidad ?? 0}</td>
                <td style="text-align:center;">${parseFloat(d.Descuento || 0)}%</td>
                <td style="text-align:center;">${parseFloat(d.Impuesto || 0)}%</td>
                <td style="text-align:right;">₡${Number(d.Total || 0).toLocaleString()}</td>
            </tr>
        `).join("");

        const html = `
            <div id="ticketAbono" style="font-family: monospace; font-size: 13px; color: #000;">
                <div style="text-align:center; margin-bottom:10px;">
                    <h5 style="margin:0;">Óptica Grisol</h5>
                    <div>Recibo de abono</div>
                </div>

                <hr>

                <p style="margin:4px 0;"><strong>Factura:</strong> #${f.Id ?? facturaId}</p>
                <p style="margin:4px 0;"><strong>Fecha:</strong> ${f.Fecha ?? "-"}</p>
                <p style="margin:4px 0;"><strong>Cliente:</strong> ${f.Cliente ?? "-"}</p>
                <p style="margin:4px 0;"><strong>Teléfono:</strong> ${f.Telefono ?? "-"}</p>

                <hr>

                <p style="margin:4px 0;"><strong>Total original:</strong> ₡${Number(f.Total || 0).toLocaleString()}</p>
                <p style="margin:4px 0;"><strong>Abono realizado:</strong> ₡${Number(montoAbonado || 0).toLocaleString()}</p>
                <p style="margin:4px 0;"><strong>Total abonado acumulado:</strong> ₡${Number(f.Abonado || 0).toLocaleString()}</p>
                <p style="margin:4px 0;"><strong>Saldo pendiente:</strong> ₡${Number(f.Pendiente || 0).toLocaleString()}</p>

                <hr>

                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Producto</th>
                            <th style="text-align:center;">Cant.</th>
                            <th style="text-align:center;">Desc.</th>
                            <th style="text-align:center;">Imp.</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${ticketDetalle || `
                            <tr>
                                <td colspan="5" style="text-align:center;">Sin detalle</td>
                            </tr>
                        `}
                    </tbody>
                </table>

                <hr>

                <p style="text-align:center; margin-top:10px;">
                    Gracias por su pago
                </p>
            </div>
        `;

        const body = document.getElementById("reciboAbonoBody");
        if (body) {
            body.innerHTML = html;
        } else {
            throw new Error("No se encontró el contenedor del recibo");
        }

        new bootstrap.Modal(document.getElementById("modalReciboAbono")).show();

    } catch (error) {
        console.error("Error mostrando recibo:", error);
        alert("Error al generar el recibo");
    }
}


function imprimirReciboAbono() {
    const ticket = document.getElementById("ticketAbono");

    if (!ticket) {
        alert("No hay recibo para imprimir.");
        return;
    }

    const contenido = ticket.outerHTML;
    const ventana = window.open("", "_blank", "width=300,height=600");

    if (!ventana) {
        alert("El navegador bloqueó la ventana de impresión.");
        return;
    }

    ventana.document.write(`
        <html>
            <head>
                <style>
                    @page {
                        size: 80mm auto;
                        margin: 0;
                    }

                    html, body {
                        width: 80mm;
                        margin: 0;
                        padding: 8px;
                        font-family: monospace;
                        font-size: 12px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    td, th {
                        padding: 3px 0;
                    }

                    hr {
                        border: none;
                        border-top: 1px dashed #000;
                        margin: 6px 0;
                    }
                </style>
            </head>
            <body>${contenido}</body>
        </html>
    `);

    function mostrarFacturasPaginadas() {
    const body = document.getElementById("facturas-body");
    if (!body) return;

    body.innerHTML = "";

    const inicio = (paginaActualFacturas - 1) * facturasPorPagina;
    const fin = inicio + facturasPorPagina;
    const facturasPagina = facturasGlobal.slice(inicio, fin);

    facturasPagina.forEach(f => agregarFilaFactura(f));
}


function renderizarPaginacionFacturas() {
    const contenedor = document.getElementById("paginacionFacturas");
    if (!contenedor) return;

    contenedor.innerHTML = "";

    const totalPaginas = Math.ceil(facturasGlobal.length / facturasPorPagina);

    if (totalPaginas <= 1) return;

    contenedor.innerHTML += `
        <li class="page-item ${paginaActualFacturas === 1 ? "disabled" : ""}">
            <button class="page-link" onclick="cambiarPaginaFacturas(${paginaActualFacturas - 1})">
                ‹ Anterior
            </button>
        </li>
    `;

    for (let i = 1; i <= totalPaginas; i++) {
        contenedor.innerHTML += `
            <li class="page-item ${paginaActualFacturas === i ? "active" : ""}">
                <button class="page-link" onclick="cambiarPaginaFacturas(${i})">
                    ${i}
                </button>
            </li>
        `;
    }

    contenedor.innerHTML += `
        <li class="page-item ${paginaActualFacturas === totalPaginas ? "disabled" : ""}">
            <button class="page-link" onclick="cambiarPaginaFacturas(${paginaActualFacturas + 1})">
                Siguiente ›
            </button>
        </li>
    `;
}


function cambiarPaginaFacturas(pagina) {
    const totalPaginas = Math.ceil(facturasGlobal.length / facturasPorPagina);

    if (pagina < 1 || pagina > totalPaginas) return;

    paginaActualFacturas = pagina;
    mostrarFacturasPaginadas();
    renderizarPaginacionFacturas();
}

    ventana.document.close();
    ventana.focus();
    ventana.print();
}