// Variables globales
window.cart = window.cart || [];
let cart = window.cart;
window.productos = window.productos || [];

// Variables DOM
let productosContainer, cartSubtotal, cartDiscount, cartTax, cartTotal;
let btnFinalizar, metodoPagoSelect, cedulaInput, nombreClienteSpan, searchInput;
let montoAbonoInput;

let facturarEmpresaCheckbox, datosEmpresaDiv, empresaNombreInput, empresaIdentificacionInput;

const CONTROLLER_PATH = "../Controller/puntoVentaController.php";

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

    if (searchInput) searchInput.addEventListener("input", renderProductos);

    if (facturarEmpresaCheckbox) {
        facturarEmpresaCheckbox.addEventListener("change", manejarToggleFacturarEmpresa);
        manejarToggleFacturarEmpresa();
    }

    if (empresaIdentificacionInput) {
        empresaIdentificacionInput.addEventListener("input", () => {
            const ced = empresaIdentificacionInput.value.trim();
            if (ced.length >= 9) consultarEmpresaPorCedula(ced);
            if (ced.length === 0) empresaNombreInput.value = "";
        });
    }

    const telefonoInput = document.getElementById("telefonoCliente");
    const telefonoError = document.getElementById("telefonoError");

    if (telefonoInput) {
        telefonoInput.addEventListener("input", () => {

            const valor = telefonoInput.value.replace(/\D/g, "");
            telefonoInput.value = valor;

            if (valor.length > 0 && valor.length < 8) {
                telefonoError.classList.remove("d-none");
            } else {
                telefonoError.classList.add("d-none");
            }

        });
    }

});

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
                        <p class="fw-bold text-primary mt-2">₡${producto.precio.toLocaleString()}</p>
                        <button class="btn btn-primary-custom mt-auto w-100"
                            onclick="agregarAlCarrito(${producto.id})">
                            Agregar
                        </button>
                    </div>
                </div>
            `;

            productosContainer.appendChild(card);

        });

}

function agregarAlCarrito(productId) {

    const producto = window.productos.find(p => p.id === productId);
    const existente = cart.find(i => i.id === productId);

    if (existente) existente.cantidad++;
    else cart.push({ ...producto, cantidad: 1, descuento: 0 });

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
    let totalDescuento = 0;

    cart.forEach(item => {

        const totalProducto = item.precio * item.cantidad;

        subtotal += totalProducto;
        totalDescuento += totalProducto * (item.descuento / 100);

    });

    const iva = (subtotal - totalDescuento) * 0.13;
    const total = subtotal - totalDescuento + iva;

    cartSubtotal.textContent = subtotal.toFixed(2);
    cartDiscount.textContent = totalDescuento.toFixed(2);
    cartTax.textContent = iva.toFixed(2);
    cartTotal.textContent = total.toFixed(2);

}

function renderCarrito() {

    const container = document.getElementById("cart-items");
    container.innerHTML = "";

    if (cart.length === 0) {

        container.innerHTML = `<p class="text-muted">No hay productos agregados.</p>`;
        calcularTotales();
        return;

    }

    cart.forEach(item => {

        const totalProducto = item.precio * item.cantidad * (1 - item.descuento / 100);

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

                    <input type="number" min="1"
                        value="${item.cantidad}"
                        onchange="actualizarCantidad(${item.id},this.value)">

                    <input type="number" min="0" max="100"
                        value="${item.descuento}"
                        onchange="actualizarDescuento(${item.id},this.value)">

                    <div>₡${totalProducto.toFixed(2)}</div>

                </div>

            </div>
        `;

        container.appendChild(div);

    });

    calcularTotales();

}

async function validarEstadoCaja() {

    try {

        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "estadoCaja" })
        });

        const data = await res.json();

        const alerta = document.getElementById("alertaCajaCerrada");

        if (data.cerrada) {

            btnFinalizar.disabled = true;

            if (alerta) alerta.classList.remove("d-none");

        } else {

            btnFinalizar.disabled = false;

            if (alerta) alerta.classList.add("d-none");

        }

    } catch (e) {

        console.error("Error validando caja", e);

    }

}

async function finalizarVenta() {

    if (cart.length === 0) {
        mostrarAlertaPOS("Debe agregar productos.");
        return;
    }

    const total = parseFloat(cartTotal.textContent);
    const montoAbono = parseFloat(montoAbonoInput?.value || 0);

    if (montoAbono > total) {
        mostrarAlertaPOS("El abono no puede ser mayor al total.");
        return;
    }

    const telefono = document.getElementById("telefonoCliente")?.value || "";
    const facturarEmpresa = facturarEmpresaCheckbox.checked;

    const payload = {

        action: "generarVenta",

        clienteId: facturarEmpresa ? 0 : (nombreClienteSpan.dataset.id || 0),
        clienteNombre: facturarEmpresa ? "" : nombreClienteSpan.dataset.nombre,

        metodoPago: metodoPagoSelect.value,
        telefono: telefono,

        facturarEmpresa: facturarEmpresa ? 1 : 0,
        empresaNombre: empresaNombreInput?.value || "",
        empresaIdentificacion: empresaIdentificacionInput?.value || "",

        cedulaIngresada: facturarEmpresa ? empresaIdentificacionInput.value : cedulaInput.value,

        facturaElectronica: document.getElementById("facturaElectronica")?.checked ? 1 : 0,
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

    if (result.error === "CAJA_CERRADA") {

        new bootstrap.Modal(
            document.getElementById("modalCajaCerrada")
        ).show();

        return;

    }

    mostrarFacturaTicket(result.factura);

    window.cart = [];
    cart = window.cart;

    renderCarrito();

}

function mostrarAlertaPOS(mensaje) {

    document.getElementById("modalAlertaPOSBody").textContent = mensaje;

    new bootstrap.Modal(
        document.getElementById("modalAlertaPOS")
    ).show();

}

function mostrarFacturaTicket(factura) {

    const encabezado = factura?.encabezado || factura || {};
    const detalle = factura?.detalle || [];

    const facturaId = encabezado.Id || encabezado.FacturaId || "-";
    const fecha = encabezado.Fecha || new Date().toLocaleString();

    const total = encabezado.Total || 0;
    const subtotal = encabezado.Subtotal || 0;
    const descuento = encabezado.Descuento || 0;
    const iva = encabezado.IVA || 0;

    const abono = encabezado.Abono || encabezado.Abonado || 0;
    const pendiente = encabezado.SaldoPendiente || encabezado.Pendiente || 0;

    const modalBody = document.getElementById("modalFacturaBody");

    modalBody.innerHTML = `
    <div id="ticketFactura">

        <h5 style="text-align:center;">Óptica Grisol</h5>

        <strong>Factura:</strong> ${facturaId}<br>
        <strong>Fecha:</strong> ${fecha}<br>

        <hr>

        <table style="width:100%">

            ${detalle.map(d => `
                <tr>
                    <td>${d.Nombre}</td>
                    <td>${d.Cantidad}</td>
                    <td>${d.Descuento}%</td>
                    <td>₡${parseFloat(d.Total).toFixed(2)}</td>
                </tr>
            `).join("")}

        </table>

        <hr>

        Subtotal: ₡${subtotal}<br>
        Descuento: ₡${descuento}<br>
        IVA: ₡${iva}<br>

        <hr>

        Total: ₡${total}<br>

        ${pendiente > 0 ? `
            Abono: ₡${abono}<br>
            Pendiente: ₡${pendiente}
        ` : ""}

    </div>
    `;

    new bootstrap.Modal(
        document.getElementById("modalFactura")
    ).show();

}

if (localStorage.getItem("darkModePOS") === "1") {
    document.body.classList.add("modo-oscuro");
}