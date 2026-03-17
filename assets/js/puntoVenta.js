/* =====================================================
   VARIABLES GLOBALES
===================================================== */

window.cart = window.cart || [];
let cart = window.cart;
window.productos = window.productos || [];

let productosContainer,
    cartSubtotal,
    cartDiscount,
    cartTax,
    cartTotal,
    btnFinalizar,
    metodoPagoSelect,
    cedulaInput,
    nombreClienteSpan,
    searchInput,
    montoAbonoInput;

let facturarEmpresaCheckbox,
    datosEmpresaDiv,
    empresaNombreInput,
    empresaIdentificacionInput;

const CONTROLLER_PATH = "../Controller/puntoVentaController.php";


/* =====================================================
   INIT
===================================================== */

document.addEventListener("DOMContentLoaded", () => {

    productosContainer = document.getElementById("productos-container");

    cartSubtotal = document.getElementById("cart-subtotal");
    cartDiscount = document.getElementById("cart-discount");
    cartTax = document.getElementById("cart-tax");
    cartTotal = document.getElementById("cart-total");

    btnFinalizar = document.getElementById("btnFinalizar");
    metodoPagoSelect = document.getElementById("metodoPago");

    cedulaInput = document.getElementById("cedulaCliente");
    nombreClienteSpan = document.getElementById("nombreCliente");

    searchInput = document.getElementById("searchInput");
    montoAbonoInput = document.getElementById("montoAbono");

    facturarEmpresaCheckbox = document.getElementById("facturarEmpresa");
    datosEmpresaDiv = document.getElementById("datosEmpresa");
    empresaNombreInput = document.getElementById("empresaNombre");
    empresaIdentificacionInput = document.getElementById("empresaIdentificacion");

    validarEstadoCaja();
    cargarProductos();

    if (btnFinalizar) btnFinalizar.addEventListener("click", finalizarVenta);

    if (cedulaInput) {

        cedulaInput.addEventListener("input", () => {
            const ced = cedulaInput.value.trim();
            if (ced.length >= 6) buscarCliente();
        });

        cedulaInput.addEventListener("keyup", (e) => {
            if (e.key === "Enter") buscarCliente();
        });

        cedulaInput.addEventListener("blur", buscarCliente);
    }

    if (searchInput)
        searchInput.addEventListener("input", renderProductos);

    if (facturarEmpresaCheckbox) {

        facturarEmpresaCheckbox.addEventListener(
            "change",
            manejarToggleFacturarEmpresa
        );

        manejarToggleFacturarEmpresa();
    }

});


/* =====================================================
   PRODUCTOS
===================================================== */

function cargarProductos() {

    fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "obtenerProductos" })
    })
        .then(res => res.json())
        .then(data => {

            window.productos = data.map(p => ({
                id: parseInt(p.ProductoId),
                nombre: p.Nombre,
                precio: parseFloat(p.Precio),
                descripcion: p.Descripcion || ""
            }));

            renderProductos();
        });

}

function renderProductos() {

    productosContainer.innerHTML = "";

    const filtro = (searchInput?.value || "").toLowerCase();

    window.productos
        .filter(p => p.nombre.toLowerCase().includes(filtro))
        .forEach(producto => {

            const card = document.createElement("div");
            card.className = "col-md-4 mb-3";

            card.innerHTML = `
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <strong>${producto.nombre}</strong>
                        <p class="fw-bold text-primary mt-2">
                            ₡${producto.precio.toLocaleString()}
                        </p>

                        <button
                            class="btn btn-primary-custom"
                            onclick="agregarAlCarrito(${producto.id})"
                        >
                            Agregar
                        </button>

                    </div>
                </div>
            `;

            productosContainer.appendChild(card);

        });

}


/* =====================================================
   CARRITO
===================================================== */

function agregarAlCarrito(productId) {

    const producto = window.productos.find(p => p.id === productId);

    const existente = cart.find(i => i.id === productId);

    if (existente)
        existente.cantidad++;
    else
        cart.push({ ...producto, cantidad: 1, descuento: 0 });

    renderCarrito();
}

function actualizarCantidad(id, cantidad) {

    const item = cart.find(i => i.id === id);

    item.cantidad = parseInt(cantidad) || 1;

    renderCarrito();
}

function actualizarDescuento(id, descuento) {

    const item = cart.find(i => i.id === id);

    item.descuento = parseFloat(descuento) || 0;

    renderCarrito();
}

function eliminarProducto(id) {

    cart = cart.filter(i => i.id !== id);
    window.cart = cart;

    renderCarrito();
}


function calcularTotales() {

    let subtotal = 0;
    let descuento = 0;

    cart.forEach(item => {

        const totalProducto = item.precio * item.cantidad;

        subtotal += totalProducto;

        descuento += totalProducto * (item.descuento / 100);

    });

    const iva = (subtotal - descuento) * 0.13;

    const total = subtotal - descuento + iva;

    cartSubtotal.textContent = subtotal.toFixed(2);
    cartDiscount.textContent = descuento.toFixed(2);
    cartTax.textContent = iva.toFixed(2);
    cartTotal.textContent = total.toFixed(2);
}


function renderCarrito() {

    const container = document.getElementById("cart-items");

    container.innerHTML = "";

    if (cart.length === 0) {

        container.innerHTML =
            `<p class="text-muted">No hay productos agregados.</p>`;

        calcularTotales();
        return;
    }

    cart.forEach(item => {

        const totalProducto =
            item.precio *
            item.cantidad *
            (1 - item.descuento / 100);

        const div = document.createElement("div");

        div.innerHTML = `

        <div class="cart-item-modern shadow-sm p-3 rounded">

            <div class="item-header">

                <strong>${item.nombre}</strong>

                <button onclick="eliminarProducto(${item.id})">
                    🗑
                </button>

            </div>

            <div class="item-controls">

                <input
                    type="number"
                    min="1"
                    value="${item.cantidad}"
                    onchange="actualizarCantidad(${item.id},this.value)"
                >

                <input
                    type="number"
                    min="0"
                    max="100"
                    value="${item.descuento}"
                    onchange="actualizarDescuento(${item.id},this.value)"
                >

                <div>
                    ₡${totalProducto.toFixed(2)}
                </div>

            </div>

        </div>
        `;

        container.appendChild(div);

    });

    calcularTotales();
}


/* =====================================================
   CLIENTES
===================================================== */

async function buscarCliente() {

    const ced = cedulaInput.value.trim();

    if (ced.length < 6) {

        nombreClienteSpan.textContent =
            "Nombre del cliente aparecerá aquí";

        nombreClienteSpan.dataset.id = "";
        nombreClienteSpan.dataset.nombre = "";

        return;
    }

    try {

        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                action: "obtenerCliente",
                cedula: ced
            })
        });

        const data = await res.json();

        if (data?.PacienteId) {

            nombreClienteSpan.textContent = data.NombreCompleto;
            nombreClienteSpan.dataset.id = data.PacienteId;
            nombreClienteSpan.dataset.nombre = data.NombreCompleto;

            return;
        }

    } catch (e) {

        console.error("Error buscando cliente", e);

    }

}


/* =====================================================
   EMPRESAS
===================================================== */

function manejarToggleFacturarEmpresa() {

    const activo = facturarEmpresaCheckbox.checked;

    if (activo) {

        datosEmpresaDiv.style.display = "block";

        cedulaInput.value = "";
        cedulaInput.disabled = true;

        nombreClienteSpan.textContent = "Cliente no registrado";

    } else {

        datosEmpresaDiv.style.display = "none";

        cedulaInput.disabled = false;
    }

}


/* =====================================================
   CAJA
===================================================== */

async function validarEstadoCaja() {

    try {

        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "estadoCaja" })
        });

        const data = await res.json();

        if (data.cerrada) {

            btnFinalizar.disabled = true;

        } else {

            btnFinalizar.disabled = false;
        }

    } catch (e) {

        console.error("Error validando caja", e);

    }

}


/* =====================================================
   VENTA
===================================================== */

async function finalizarVenta() {

    if (cart.length === 0) {

        mostrarAlertaPOS("Debe agregar productos.");
        return;
    }

    const total = parseFloat(cartTotal.textContent);

    const montoAbono =
        parseFloat(montoAbonoInput?.value || 0);

    if (montoAbono > total) {

        mostrarAlertaPOS("El abono no puede ser mayor al total.");
        return;
    }

    const payload = {

        action: "generarVenta",

        clienteId: nombreClienteSpan.dataset.id || 0,

        clienteNombre:
            nombreClienteSpan.dataset.nombre ||
            nombreClienteSpan.textContent,

        metodoPago: metodoPagoSelect.value,

        montoAbono: montoAbono,

        productos: cart.map(i => ({
            productoId: i.id,
            descripcion: i.nombre,
            cantidad: i.cantidad,
            precioUnitario: i.precio,
            descuento: i.descuento
        }))

    };

    const res = await fetch(CONTROLLER_PATH, {

        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)

    });

    const result = await res.json();

    console.log("FACTURA COMPLETA:", result);

    if (result.error === "CAJA_CERRADA") {

        new bootstrap.Modal(
            document.getElementById("modalCajaCerrada")
        ).show();

        return;
    }

    mostrarFacturaTicket(result.factura);

    cart = [];
    window.cart = cart;

    renderCarrito();
}


/* =====================================================
   ALERTAS
===================================================== */

function mostrarAlertaPOS(mensaje) {

    document.getElementById(
        "modalAlertaPOSBody"
    ).textContent = mensaje;

    new bootstrap.Modal(
        document.getElementById("modalAlertaPOS")
    ).show();

}


/* =====================================================
   TICKET
===================================================== */

function mostrarFacturaTicket(factura) {

    if (!factura) {
        console.error("Factura vacía");
        return;
    }

    const enc = factura.encabezado;
    const det = factura.detalle;

    const qrData = `
Factura:${enc.Id}
Cliente:${enc.Cliente || ""}
Total:${enc.Total}
Fecha:${enc.Fecha}
    `;

    const html = `
    <html>
    <head>
        <style>
            body {
                font-family: monospace;
                padding: 10px;
                width: 260px;
            }

            .logo {
                text-align: center;
                margin-bottom: 10px;
            }

            .logo img {
                width: 80px;
            }

            h5 {
                text-align: center;
                margin: 0;
            }

            hr {
                margin: 5px 0;
            }

            .item {
                display: flex;
                justify-content: space-between;
                font-size: 12px;
            }

            .total {
                font-weight: bold;
                text-align: center;
                margin-top: 10px;
                font-size: 14px;
            }

            .qr {
                text-align: center;
                margin-top: 10px;
            }

        </style>
    </head>

    <body>

        <div class="logo">
            <img src="/img/logo-grisol.png" />
        </div>

        <h5>Óptica Grisol</h5>
        <div style="text-align:center;">Venta al detalle</div>

        <hr>

        Factura: ${enc.Id}<br>
        Fecha: ${enc.Fecha}<br>
        Pago: ${enc.MetodoPago}<br>

        ${enc.Cliente ? `Cliente: ${enc.Cliente}<br>` : ""}
        ${enc.Empresa ? `Empresa: ${enc.Empresa}<br>` : ""}

        <hr>

        ${det.map(d => `
            <div class="item">
                <span>${d.Nombre} x${d.Cantidad}</span>
                <span>₡${parseFloat(d.Total).toFixed(2)}</span>
            </div>
        `).join("")}

        <hr>

        Subtotal: ₡${enc.Subtotal}<br>
        Descuento: ₡${enc.Descuento}<br>
        IVA: ₡${enc.IVA}<br>

        <div class="total">
            TOTAL: ₡${enc.Total}
        </div>

        <div class="qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(qrData)}">
        </div>

        <div style="text-align:center; font-size:11px;">
            ¡Gracias por su compra!
        </div>

    </body>
    </html>
    `;

    const w = window.open("", "_blank", "width=300,height=600");

    w.document.write(html);
    w.document.close();

    setTimeout(() => {
        w.print();
    }, 500);
}


/* =====================================================
   DARK MODE
===================================================== */

if (localStorage.getItem("darkModePOS") === "1") {
    document.body.classList.add("modo-oscuro");
}