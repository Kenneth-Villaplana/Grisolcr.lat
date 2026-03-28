// Variables globales
window.cart = window.cart || [];
let cart = window.cart;
window.productos = window.productos || [];
let stockCache = {};
// Variables DOM
let productosContainer, cartSubtotal, cartDiscount, cartTax, cartTotal;
let btnFinalizar, metodoPagoSelect, cedulaInput, nombreClienteSpan, searchInput;
let montoAbonoInput;

let montoEfectivoInput, cambioTexto, bloqueEfectivo;


let facturarEmpresaCheckbox, datosEmpresaDiv, empresaNombreInput, empresaIdentificacionInput;

const CONTROLLER_PATH = "../Controller/puntoVentaController.php";


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

    montoEfectivoInput = document.getElementById("montoEfectivo");
    cambioTexto        = document.getElementById("cambioTexto");
    bloqueEfectivo     = document.getElementById("bloqueEfectivo");

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

    // API para empresas
    if (empresaIdentificacionInput) {
        empresaIdentificacionInput.addEventListener("input", () => {
            const ced = empresaIdentificacionInput.value.trim();
            if (ced.length >= 9) consultarEmpresaPorCedula(ced);
            if (ced.length === 0) empresaNombreInput.value = "";
        });
    }
    
    
const telefonoInput = document.getElementById("telefonoCliente");
const telefonoError = document.getElementById("telefonoError");

if (telefonoInput && telefonoError) {
    telefonoInput.addEventListener("input", () => {
        const valor = telefonoInput.value.replace(/\D/g, ""); // solo números
        telefonoInput.value = valor;

        if (valor.length > 0 && valor.length < 8) {
            telefonoError.classList.remove("d-none");
        } else {
            telefonoError.classList.add("d-none");
        }
    });
}

//cambio 

 if (metodoPagoSelect) {
        metodoPagoSelect.addEventListener("change", manejarMetodoPago);
    }

    if (montoEfectivoInput) {
        montoEfectivoInput.addEventListener("input", calcularCambio);
    }

    
    if (montoAbonoInput) {
        montoAbonoInput.addEventListener("input", calcularCambio);
    }

    manejarMetodoPago();
});

async function obtenerStock(productId) {

    if (stockCache[productId] !== undefined) {
        return stockCache[productId];
    }

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "obtenerStock",
            productoId: productId
        })
    });

    const data = await res.json();
    stockCache[productId] = parseInt(data.Stock) || 0;

    return stockCache[productId];
}

function cargarProductos() {
    fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "obtenerProductos" })
    })
        .then(res => res.json())
        .then(data => {
            window.productos = data
            .filter(p => parseInt(p.Cantidad) > 0)
            .map(p => ({
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


async function agregarAlCarrito(productId) {

    const producto = window.productos.find(p => p.id === productId);
    const existente = cart.find(i => i.id === productId);

    const stock = await obtenerStock(productId);
    const cantidadActual = existente ? existente.cantidad : 0;

    
    if (cantidadActual >= stock) {


        if (existente) {
            renderCarrito();

            setTimeout(() => {
                mostrarMensajeStock(productId);
            }, 50);
        }

        return;
    }

    if (existente) {
        existente.cantidad++;
    } else {
        cart.push({ ...producto, cantidad: 1, descuento: 0, impuesto: 0 });
    }

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

function actualizarImpuesto(id, impuesto) {
    const item = cart.find(i => i.id === id);
    item.impuesto = parseFloat(impuesto) || 0;
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
    let totalImpuesto = 0;

    cart.forEach(item => {
        const totalProducto = item.precio * item.cantidad;
        const descuentoLinea = totalProducto * ((parseFloat(item.descuento) || 0) / 100);
        const baseLinea = totalProducto - descuentoLinea;
        const impuestoLinea = baseLinea * ((parseFloat(item.impuesto) || 0) / 100);


        subtotal += totalProducto;
        totalDescuento += descuentoLinea;
        totalImpuesto += impuestoLinea;
    });

    const total = subtotal - totalDescuento + totalImpuesto;

    cartSubtotal.textContent = subtotal.toFixed(2);
    cartDiscount.textContent = totalDescuento.toFixed(2);
    cartTax.textContent      = totalImpuesto.toFixed(2);
    cartTotal.textContent    = total.toFixed(2);

    calcularCambio();
}

function manejarMetodoPago() {

    if (!bloqueEfectivo) return;

    if (metodoPagoSelect.value === "efectivo") {
        bloqueEfectivo.style.display = "block";
    } else {
        bloqueEfectivo.style.display = "none";

        if (montoEfectivoInput) montoEfectivoInput.value = "";
        if (cambioTexto) cambioTexto.textContent = "Cambio: ₡0.00";
    }
}

function calcularCambio() {

    if (!montoEfectivoInput || !cambioTexto || !cartTotal) return;

    const efectivo = parseFloat(montoEfectivoInput.value) || 0;
    const abono = parseFloat(montoAbonoInput?.value || 0);
    const total = parseFloat(cartTotal.textContent) || 0;

    let cambio = 0;

    if (abono > 0) {
        // con abono
        cambio = efectivo - abono;
    } else {
        //sin abono
        cambio = efectivo - total;
    }

    if (cambio < 0) cambio = 0;

    cambioTexto.textContent = "Cambio: ₡" + cambio.toFixed(2);
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
        const subtotalLinea = item.precio * item.cantidad;
        const descuentoLinea = subtotalLinea * ((parseFloat(item.descuento) || 0) / 100);
        const baseLinea = subtotalLinea - descuentoLinea;
        const impuestoLinea = baseLinea * ((parseFloat(item.impuesto) || 0) / 100);
        const totalProducto = baseLinea + impuestoLinea;

        const div = document.createElement("div");
        div.className = "";

        div.innerHTML = `
            <div class="cart-item-modern shadow-sm p-3 rounded">

                <div class="item-header">
                    <strong class="item-title">${item.nombre}</strong>

                    <button class="delete-btn" onclick="eliminarProducto(${item.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="item-controls">
                    
                 <div class="input-group input-group-sm" style="width:100px; flex-direction:column; align-items:flex-start;">
    
                        <div class="d-flex w-100">
                            <span class="input-group-text">Cant.</span>

                            <input type="number" 
                                min="1" 
                                value="${item.cantidad}" 
                                class="form-control input-cantidad"
                                data-id="${item.id}"
                                oninput="validarCantidadTiempoReal(this)">
                        </div>

                   
                       <small class="text-danger d-none stock-error" style="margin-left:10px;">
                            Cantidad maxima
                        </small>

                    </div>
                      <div class="input-group input-group-sm input-descuento">
                        <span class="input-group-text">Desc.</span>

                        <input type="number" 
                               min="0" 
                               max="100" 
                               value="${item.descuento}" 
                               class="form-control"
                               onchange="actualizarDescuento(${item.id}, this.value)">

                        <span class="input-group-text">%</span>
                    </div>
                    
                    <div class="input-group input-group-sm input-impuesto">
                        <span class="input-group-text">Imp.</span>

    <                          input type="number" 
                               min="0" 
                               max="100" 
                               value="${item.impuesto || 0}" 
                               class="form-control"
                               onchange="actualizarImpuesto(${item.id}, this.value)">

                         <span class="input-group-text">%</span>
                     </div>

                    <div class="item-total">₡${totalProducto.toFixed(2)}</div>
                </div>

            </div>
        `;

        container.appendChild(div);
    });

    calcularTotales();
}

async function validarCantidadTiempoReal(input) {

    const id = parseInt(input.dataset.id);
    const item = cart.find(i => i.id === id);

    let valor = parseInt(input.value) || 1;
    const stock = await obtenerStock(id);

   const contenedor = input.closest(".input-group");
    const mensaje = contenedor.querySelector(".stock-error");

    if (valor > stock) {
        input.value = stock;
        item.cantidad = stock;

        if (mensaje) {
            mensaje.classList.remove("d-none");
        }

        setTimeout(() => {
            if (mensaje) mensaje.classList.add("d-none");
        }, 2000);

    } else {
        item.cantidad = valor;

        if (mensaje) {
            mensaje.classList.add("d-none");
        }
    }

    calcularTotales();
}

// ================= MENSAJE =================
function mostrarMensajeStock(productId) {

    const inputs = document.querySelectorAll(".input-cantidad");

    inputs.forEach(input => {

        if (parseInt(input.dataset.id) === productId) {

            const contenedor = input.closest(".input-group");
            if (!contenedor) return;

            const mensaje = contenedor.querySelector(".stock-error");
            if (!mensaje) return;

           
            mensaje.classList.remove("d-none");

            setTimeout(() => {
                mensaje.classList.add("d-none");
            }, 2000);
        }
    });
}

async function buscarCliente() {
    const ced = cedulaInput.value.trim();

    if (ced.length < 6) {
        nombreClienteSpan.textContent = "Nombre del cliente aparecerá aquí";
        nombreClienteSpan.dataset.id = "";
        nombreClienteSpan.dataset.nombre = "";

        // ocultar teléfono
        document.getElementById("telefonoClienteDiv").style.display = "none";

        sessionStorage.removeItem("clientePOS");

        return;
    }
try{
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

          sessionStorage.setItem("clientePOS", JSON.stringify(data));
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
            <span class="fw-semibold text-primary">
                ${nombreAPI || "Cliente no encontrado"}
            </span><br>

            <a href="/View/registrarClientePOS.php?cedula=${encodeURIComponent(ced)}&origen=POS"
               class="btn btn-sm btn-outline-primary mt-2">
                Registrar cliente
            </a>
        `;

    nombreClienteSpan.dataset.id = "";
    nombreClienteSpan.dataset.nombre = nombreAPI;

    // permitir digitar teléfono manual
    const telDiv = document.getElementById("telefonoClienteDiv");
    const telInput = document.getElementById("telefonoCliente");

    telDiv.style.display = "block";
    telInput.value = "";
     sessionStorage.removeItem("clientePOS");

    } catch (error) {

        console.error("Error buscando cliente:", error);

        nombreClienteSpan.textContent = "Error al buscar cliente";

        sessionStorage.removeItem("clientePOS");
    }
}


function registrarClientePOS() {
    const ced = cedulaInput.value.trim();
    if (!ced) return alert("Debe ingresar una cédula válida.");
    window.location.href = `/View/registrarClientePOS.php?cedula=${ced}`;
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
async function validarEstadoCaja() {
    try {
        const res = await fetch(CONTROLLER_PATH, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "estadoCaja" })
        });

        const data = await res.json();

        const btn = document.getElementById("btnFinalizar");
        const alerta = document.getElementById("alertaCajaCerrada");

        if (data.cerrada) {

            if (btn) {
                btn.disabled = true;
                btn.classList.add("disabled");
                btn.title = "Caja cerrada";
            }

            if (alerta) {
                alerta.classList.remove("d-none");
            }

        } else {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove("disabled");
                btn.title = "";
            }

            if (alerta) {
                alerta.classList.add("d-none");
            }
        }

    } catch (e) {
        console.error("Error validando estado de caja", e);
    }
}


async function finalizarVenta() {
    try{

    if (cart.length === 0) {
        mostrarAlertaPOS("Debe agregar productos.");
        return;
    }

    const subtotal = parseFloat(cartSubtotal.textContent);
    const descuento = parseFloat(cartDiscount.textContent);
    const iva = parseFloat(cartTax.textContent);
    const total = parseFloat(cartTotal.textContent);

    const montoAbono = parseFloat(montoAbonoInput?.value || 0);


        if (isNaN(total)) {
            mostrarAlertaPOS("Error calculando el total.");
            return;
        }

    if (montoAbono < 0) {
        mostrarAlertaPOS("El abono no puede ser negativo.");
        return;
    }

    if (montoAbono > total) {
        mostrarAlertaPOS("El abono no puede ser mayor al total de la factura.");
        return;
    }


    //  VALIDACIÓN DE TELÉFONO (mínimo 8 dígitos)

    const telefono = document.getElementById("telefonoCliente")?.value || "";
    const soloNumeros = telefono.replace(/\D/g, "");

    if (soloNumeros.length > 0 && soloNumeros.length < 8) {
        document.getElementById("telefonoError").classList.remove("d-none");
        mostrarAlertaPOS("El número de teléfono debe tener mínimo 8 dígitos.");
        return;
    }
 


const facturarEmpresa = facturarEmpresaCheckbox.checked;
const metodoPago = metodoPagoSelect.value;

let efectivo = 0;
let cambio = 0;

if (metodoPago === "efectivo") {

    efectivo = parseFloat(montoEfectivoInput?.value || 0);

    if (efectivo <= 0) {
        mostrarAlertaPOS("Debe ingresar el efectivo recibido.");
        return;
    }

    // con abono
    if (montoAbono > 0) {

        if (efectivo < montoAbono) {
            mostrarAlertaPOS("El efectivo no cubre el abono.");
            return;
        }

        cambio = efectivo - montoAbono;

    } 
    //  sin abono
    else {

        if (efectivo < total) {
            mostrarAlertaPOS("El efectivo es insuficiente.");
            return;
        }

        cambio = efectivo - total;
    }
}
    const payload = {
        action: "generarVenta",
        efectivo: efectivo,
        cambio: cambio,
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
            descuento: i.descuento,
            impuesto: i.impuesto || 0
        }))
    };

    const res = await fetch(CONTROLLER_PATH, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    });

    const result = await res.json();

if (result.error === "CAJA_CERRADA") {
    const modal = new bootstrap.Modal(
        document.getElementById("modalCajaCerrada")
    );
    modal.show();
    return;
}
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

        } catch (error) {

        console.error("Error en finalizarVenta:", error);
        mostrarAlertaPOS("Ocurrió un error al procesar la venta.");

    }
}


function mostrarAlertaPOS(mensaje) {
    const body = document.getElementById("modalAlertaPOSBody");
    body.textContent = mensaje;

    const modal = new bootstrap.Modal(document.getElementById("modalAlertaPOS"));
    modal.show();
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
                        encabezado.MetodoPago === "efectivo"
                            ? `
                                <strong>Recibido:</strong> ₡${encabezado.Efectivo || "0.00"}<br>
                            `
                            : ""
                    }
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
                        <th style="width:15%">Imp</th>
                        <th style="width:30%">Total</th>

                    </tr>
                </thead>
                <tbody>
                    ${detalle.map(item => `
                        <tr>
                            <td>${item.Nombre}</td>
                            <td>${item.Cantidad}</td>
                            <td>${item.Descuento}%</td>
                            <td>${item.Impuesto || 0}%</td>
                            <td>₡${parseFloat(item.Total).toFixed(2)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>

            <hr>
            <strong>Subtotal:</strong> ₡${encabezado.Subtotal}<br>
            <strong>Descuento:</strong> -₡${encabezado.Descuento}<br>
            <strong>Impuesto:</strong> ₡${encabezado.IVA}<br>
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
   document.addEventListener("DOMContentLoaded", () => {

    const btnFinalizar = document.getElementById("btnFinalizar");

    if (btnFinalizar) {
        btnFinalizar.addEventListener("click", finalizarVenta);
    } else {
        console.warn("btnFinalizar no existe en el DOM");
    }

    validarEstadoCaja();
});
// Mantener modo oscuro cuando recarga
if (localStorage.getItem("darkModePOS") === "1") {
    document.body.classList.add("modo-oscuro");
} 