// Variables globales
window.cart = window.cart || [];
let cart = window.cart;
window.productos = window.productos || [];

// Variables DOM
let productosContainer, cartSubtotal, cartDiscount, cartTax, cartTotal;
let btnFinalizar, metodoPagoSelect, cedulaInput, nombreClienteSpan, searchInput;
let montoAbonoInput;

let facturarEmpresaCheckbox, datosEmpresaDiv, empresaNombreInput, empresaIdentificacionInput;

const CONTROLLER_PATH = "/OptiGestion/Controller/puntoVentaController.php";


document.addEventListener("DOMContentLoaded", () => {

    productosContainer = document.getElementById("productos-container");
    cartSubtotal       = document.getElementById("cart-subtotal");
    cartDiscount       = document.getElementById("cart-discount");
    cartTax            = document.getElementById("cart-tax");
    cartTotal          = document.getElementById("cart-total");

    btnFinalizar       = document.getElementById("btnFinalizar");
    metodoPagoSelect   = document.getElementById("metodoPago");
    cedulaInput        = document.getElementById("cedulaCliente");
    nombreClienteSpan  = document.getElementById("nombreCliente");
    searchInput        = document.getElementById("searchInput");

    montoAbonoInput    = document.getElementById("montoAbono");

    facturarEmpresaCheckbox = document.getElementById("facturarEmpresa");
    datosEmpresaDiv         = document.getElementById("datosEmpresa");
    empresaNombreInput      = document.getElementById("empresaNombre");
    empresaIdentificacionInput = document.getElementById("empresaIdentificacion");

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

    // API para empresas
    if (empresaIdentificacionInput) {
        empresaIdentificacionInput.addEventListener("input", () => {
            const ced = empresaIdentificacionInput.value.trim();
            if (ced.length >= 9) consultarEmpresaPorCedula(ced);
            if (ced.length === 0) empresaNombreInput.value = "";
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
                        <strong class="card-title text-dark">${producto.nombre}</strong>
                        <p class="card-text fw-bold text-primary mt-2">₡${producto.precio.toLocaleString()}</p>
                        <button class="btn btn-primary-custom w-100 mt-auto" onclick="agregarAlCarrito(${producto.id})">Agregar</button>
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

    const iva   = (subtotal - totalDescuento) * 0.13;
    const total = subtotal - totalDescuento + iva;

    cartSubtotal.textContent = subtotal.toFixed(2);
    cartDiscount.textContent = totalDescuento.toFixed(2);
    cartTax.textContent      = iva.toFixed(2);
    cartTotal.textContent    = total.toFixed(2);
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
        div.className = "cart-item-modern shadow-sm p-3 rounded";

        div.innerHTML = `
            <div class="d-flex justify-content-between">
                <strong class="fs-6 text-dark">${item.nombre}</strong>
                <button class="btn btn-sm text-danger" onclick="eliminarProducto(${item.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">

                <div class="input-group input-group-sm" style="width:100px;">
                    <span class="input-group-text">Cant.</span>
                    <input type="number" min="1" value="${item.cantidad}" 
                        class="form-control" onchange="actualizarCantidad(${item.id}, this.value)">
                </div>

               <div class="input-group input-group-sm input-descuento">
    <span class="input-group-text">Desc.</span>
    <input type="number" min="0" max="100" value="${item.descuento}" 
           class="form-control"
           onchange="actualizarDescuento(${item.id}, this.value)">
    <span class="input-group-text">%</span>
</div>

                <div class="fw-bold text-end">₡${totalProducto.toFixed(2)}</div>

            </div>
        `;

        container.appendChild(div);
    });

    calcularTotales();
}




async function buscarCliente() {
    const ced = cedulaInput.value.trim();

    if (ced.length < 6) {
        nombreClienteSpan.textContent = "Nombre del cliente aparecerá aquí";
        nombreClienteSpan.dataset.id = "";
        nombreClienteSpan.dataset.nombre = "";

        // ocultar teléfono
        document.getElementById("telefonoClienteDiv").style.display = "none";

        return;
    }

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "obtenerCliente", cedula: ced })
    });

    const data = await res.json();


    /** si existe en bd **/
    if (data?.PacienteId) {
        nombreClienteSpan.textContent = data.NombreCompleto;
        nombreClienteSpan.dataset.id = data.PacienteId;
        nombreClienteSpan.dataset.nombre = data.NombreCompleto;
        nombreClienteSpan.dataset.telefono = data.Telefono || "";

        // mostrar teléfono
        const telDiv = document.getElementById("telefonoClienteDiv");
        const telInput = document.getElementById("telefonoCliente");

        telDiv.style.display = "block";
        telInput.value = data.Telefono || "";

        return;
    }

    /** Si no esta registrado busca en Api**/
    let nombreAPI = "";

    try {
        const apiRes = await fetch(`https://apis.gometa.org/cedulas/${encodeURIComponent(ced)}`);
        const apiData = await apiRes.json();

        if (apiData?.results?.length > 0) {
            const p = apiData.results[0];

            nombreAPI = `${p.firstname || ""} ${p.lastname1 || ""} ${p.lastname2 || ""}`.trim();
            if (!nombreAPI && p.fullname) nombreAPI = p.fullname;
            if (!nombreAPI && p.nombre)   nombreAPI = p.nombre;
        }

    } catch (error) {
        console.error("Error consultando API:", error);
    }

    nombreClienteSpan.innerHTML = `
        <span class="fw-semibold text-primary">${nombreAPI || "Cliente no encontrado"}</span><br>
        <button class="btn btn-sm btn-outline-primary mt-2" onclick="registrarClientePOS()">
            Registrar cliente
        </button>
    `;

    nombreClienteSpan.dataset.id = "";
    nombreClienteSpan.dataset.nombre = nombreAPI;

    // permitir digitar teléfono manual
    const telDiv = document.getElementById("telefonoClienteDiv");
    const telInput = document.getElementById("telefonoCliente");

    telDiv.style.display = "block";
    telInput.value = "";
}


function registrarClientePOS() {
    const ced = cedulaInput.value.trim();
    if (!ced) return alert("Debe ingresar una cédula válida.");
    window.location.href = `/OptiGestion/View/registrarClientePOS.php?cedula=${ced}`;
}



function manejarToggleFacturarEmpresa() {
    const activo = facturarEmpresaCheckbox.checked;
    const filaCedulaCliente = document.getElementById("cedulaCliente")?.closest(".mb-3");

    if (activo) {
        datosEmpresaDiv.style.display = "block";

        cedulaInput.value = "";
        cedulaInput.disabled = true;

        nombreClienteSpan.textContent = "Cliente no registrado";
        nombreClienteSpan.dataset.id = "";
        nombreClienteSpan.dataset.nombre = "";

        document.getElementById("telefonoClienteDiv").style.display = "none";

        if (filaCedulaCliente) filaCedulaCliente.style.display = "none";

    } else {
        datosEmpresaDiv.style.display = "none";

        empresaNombreInput.value = "";
        empresaIdentificacionInput.value = "";

        cedulaInput.disabled = false;

        if (filaCedulaCliente) filaCedulaCliente.style.display = "";
    }
}




async function consultarEmpresaPorCedula(ced) {
    try {
        const res = await fetch(`https://apis.gometa.org/cedulas/${encodeURIComponent(ced)}`);
        const data = await res.json();

        if (!data?.results || data.results.length === 0) return;

        const p = data.results[0];

        let nombre = `${p.firstname || ""} ${p.lastname1 || ""} ${p.lastname2 || ""}`.trim();
        if (!nombre && p.fullname) nombre = p.fullname;
        if (!nombre && p.nombre)   nombre = p.nombre;

        empresaNombreInput.value = nombre;

    } catch (e) {
        console.error("Error API empresa:", e);
    }
}





async function finalizarVenta() {

    if (cart.length === 0) {
        alert("Debe agregar productos.");
        return;
    }

    const subtotal = parseFloat(cartSubtotal.textContent);
    const descuento = parseFloat(cartDiscount.textContent);
    const iva = parseFloat(cartTax.textContent);
    const total = parseFloat(cartTotal.textContent);

    const montoAbono = parseFloat(montoAbonoInput?.value || 0);

    if (montoAbono < 0) {
        alert("El abono no puede ser negativo.");
        return;
    }

    if (montoAbono > total) {
        alert("El abono no puede ser mayor al total de la factura.");
        return;
    }

    const facturarEmpresa = facturarEmpresaCheckbox.checked;

    const telefono = document.getElementById("telefonoCliente")?.value || "";

    const payload = {
        action: "generarVenta",

        clienteId:     facturarEmpresa ? 0 : (nombreClienteSpan.dataset.id || 0),
        clienteNombre: facturarEmpresa 
            ? "" 
            : (nombreClienteSpan.dataset.nombre || nombreClienteSpan.textContent || ""),

        metodoPago: metodoPagoSelect.value,

        telefono: telefono,  

        facturarEmpresa: facturarEmpresa ? 1 : 0,
        empresaNombre: facturarEmpresa ? empresaNombreInput.value : "",
        empresaIdentificacion: facturarEmpresa ? empresaIdentificacionInput.value : "",

        cedulaIngresada: facturarEmpresa
            ? empresaIdentificacionInput.value
            : cedulaInput.value,    
    
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

    mostrarFacturaTicket(result.factura);

    window.cart = [];
    cart = window.cart;
    renderCarrito();

    cedulaInput.value = "";
    nombreClienteSpan.textContent = "Nombre del cliente aparecerá aquí";
    nombreClienteSpan.dataset.id = "";
    nombreClienteSpan.dataset.nombre = "";

    montoAbonoInput.value = "";

    document.getElementById("telefonoClienteDiv").style.display = "none";
}



function mostrarFacturaTicket(factura) {

    const encabezado = factura?.encabezado || factura || {};
    const detalle = factura?.detalle || [];

    const modalBody = document.getElementById("modalFacturaBody");

    const empresaNombreEnc =
        encabezado.EmpresaNombre || encabezado.Empresa || "";

    const empresaIdentEnc =
        encabezado.EmpresaIdentificacion || encabezado.IdentificacionEmpresa || "";

    const esEmpresa = !!empresaNombreEnc;

    modalBody.innerHTML = `
        <div id="ticketFactura" style="font-size:14px;">

            <h5 class="text-center fw-bold">Óptica Grisol</h5>
            <small class="text-center d-block">Venta al detalle</small>
            <hr>

            <strong>Factura #:</strong> ${encabezado.Id}<br>
            <strong>Fecha:</strong> ${encabezado.Fecha}<br>
            <strong>Pago:</strong> ${encabezado.MetodoPago}<br>

            ${
                esEmpresa
                    ? `
                        <strong>Empresa:</strong> ${empresaNombreEnc}<br>
                        <strong>Identificación:</strong> ${empresaIdentEnc}<br>
                    `
                    : (
                        encabezado.Cliente
                            ? `<strong>Cliente:</strong> ${encabezado.Cliente}<br>
                               <strong>Teléfono:</strong> ${encabezado.Telefono || ""}<br>`
                            : ""
                    )
            }

            <hr>

            <table class="ticket-table mt-3">
                <thead>
                    <tr>
                        <th style="width:40%">Producto</th>
                        <th style="width:15%">Cant</th>
                        <th style="width:15%">Desc</th>
                        <th style="width:30%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${detalle.map(item => `
                        <tr>
                            <td>${item.Nombre}</td>
                            <td>${item.Cantidad}</td>
                            <td>${item.Descuento}%</td>
                            <td>₡${parseFloat(item.Total).toFixed(2)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>

            <hr>
            <strong>Subtotal:</strong> ₡${encabezado.Subtotal}<br>
            <strong>Descuento:</strong> -₡${encabezado.Descuento}<br>
            <strong>IVA (13%):</strong> ₡${encabezado.IVA}<br>
            <hr>
${
    (parseFloat(encabezado.SaldoPendiente) > 0)
        ? `
            <strong>Total factura:</strong> ₡${encabezado.Total}<br>
            <strong>Abono realizado:</strong> ₡${encabezado.Abono}<br>
            <strong>Pendiente:</strong> ₡${encabezado.SaldoPendiente}<br>
        `
        : `
            <h5 class="fw-bold">TOTAL: ₡${encabezado.Total}</h5>
        `
}

<hr>
            <p class="text-center">¡Gracias por su compra!</p>
        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-outline-secondary" id="btnImprimirTicket">Imprimir ticket</button>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById("modalFactura"));
    modal.show();

    document.getElementById("btnImprimirTicket").onclick = () => {
        const ticketHTML = document.getElementById("ticketFactura").outerHTML;
        const ventana = window.open("", "_blank", "width=300,height=600");

        ventana.document.write(`
            <html>
                <head>
                    <style>
                        body { font-family: monospace; margin:0; padding:10px; }
                        #ticketFactura { width: 200px; }
                        .ticket-table { width: 100%; font-size: 12px; }
                        .ticket-table th, .ticket-table td { text-align:left; padding-right:5px; }
                    </style>
                </head>
                <body>${ticketHTML}</body>
            </html>
        `);

        ventana.print();

        setTimeout(() => modal.hide(), 300);
    };
}
    document.getElementById("toggleDarkMode").addEventListener("click", () => {
    document.body.classList.toggle("modo-oscuro");

    localStorage.setItem("darkModePOS", 
        document.body.classList.contains("modo-oscuro") ? "1" : "0"
    );
});

// Mantener modo oscuro cuando recarga
if (localStorage.getItem("darkModePOS") === "1") {
    document.body.classList.add("modo-oscuro");
}
