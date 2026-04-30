document.addEventListener('DOMContentLoaded', () => {

    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const inputCodigo = document.getElementById('codigoInput');
    const searchInput = document.getElementById('searchInput');

    const productos = document.querySelectorAll('#listaProductos .producto');

    const mensajeDiv = document.getElementById('mensajeError');

    function mostrarError(msg) {
        if (!mensajeDiv) return;
        mensajeDiv.textContent = msg;
        mensajeDiv.classList.remove('d-none');
    }

    function ocultarError() {
        if (!mensajeDiv) return;
        mensajeDiv.classList.add('d-none');
    }
    const params = new URLSearchParams(window.location.search);
    const error = params.get("error");

    if (error) {
        mostrarError(error);
    }
    /* ============================= */
    /* FILTRO EN TIEMPO REAL */
    /* ============================= */

    function filtrarProductos() {

        const texto = searchInput ? searchInput.value.toLowerCase().trim() : "";
        const codigo = inputCodigo ? inputCodigo.value.trim() : "";

        productos.forEach(producto => {

            const nombreEl = producto.querySelector('.card-title');
            const idEl = producto.querySelector('.inv-id');

            if (!nombreEl || !idEl) return;

            const nombre = nombreEl.textContent.toLowerCase();
            const idProducto = idEl.textContent.replace('ID:', '').trim();

            const coincideNombre = nombre.includes(texto);
            const coincideId = codigo === "" || idProducto === codigo;

            producto.style.display = (coincideNombre && coincideId) ? "" : "none";

        });
    }

    if (searchInput) {
        searchInput.addEventListener('keyup', filtrarProductos);
    }

    if (inputCodigo) {
        inputCodigo.addEventListener('keyup', filtrarProductos);
    }

    /* ============================= */
    /* BUSCAR POR ID (RECARGA) */
    /* ============================= */

    if (btnBuscar && inputCodigo) {

        btnBuscar.addEventListener('click', () => {

            const codigo = inputCodigo.value.trim();

            if (codigo === '') {
                mostrarError('Ingrese un ID para buscar.');
                return;
            }

            const basePath =
                window.location.pathname.substring(
                    window.location.pathname.lastIndexOf('/') + 1
                ) || 'inventario.php';

            window.location.href = `${basePath}?id=${encodeURIComponent(codigo)}`;
        });

        inputCodigo.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                btnBuscar.click();
            }
        });
    }

    /* ============================= */
    /* LIMPIAR FILTROS */
    /* ============================= */

    if (btnLimpiar) {

        btnLimpiar.addEventListener('click', () => {

            if (searchInput) searchInput.value = "";
            if (inputCodigo) inputCodigo.value = "";

            filtrarProductos();

            const basePath =
                window.location.pathname.substring(
                    window.location.pathname.lastIndexOf('/') + 1
                ) || 'inventario.php';

            window.location.href = basePath;
        });
    }

    /* ============================= */
    /* CONFIRMAR ELIMINAR */
    /* ============================= */

    const botonesEliminar = document.querySelectorAll('.btn-confirmar-eliminar');
    const textoModal = document.getElementById('textoModalEliminar');
    const enlaceEliminar = document.getElementById('enlaceEliminar');

    if (botonesEliminar && textoModal && enlaceEliminar) {

        botonesEliminar.forEach(boton => {

            boton.addEventListener('click', () => {

                const idProducto = boton.getAttribute('data-id');
                const nombre = boton.getAttribute('data-nombre');

                textoModal.innerText =
                    `¿Estás seguro de eliminar el producto "${nombre}" (ID: ${idProducto})?`;

                enlaceEliminar.href =
                    `../Controller/productoController.php?eliminarProducto=${idProducto}`;
            });
        });
    }

    /* ============================= */
    /*  validar precio y cantidad */
    /* ============================= */

    const inputPrecio = document.getElementById('Precio');
    const inputCantidad = document.getElementById('Cantidad');
    const formAgregar = document.getElementById('formAgregarProducto');



    /* ============================= */
    /* MODAL EDITAR */
    /* ============================= */

    const btnAbrirModalEditar = document.getElementById('btnAbrirModalEditar');
    const btnConfirmarCambios = document.getElementById('btnConfirmarCambios');
    const formEditar = document.getElementById('formEditarProducto');

    if (btnAbrirModalEditar && btnConfirmarCambios && formEditar) {

       btnAbrirModalEditar.addEventListener('click', () => {

        const inputImagen = document.getElementById('Imagen');

    if (inputImagen && inputImagen.dataset.imagenInvalida === "true") {
    mostrarError("La imagen es muy pesada. Máximo permitido: 1 MB.");
    return;
    }

    if (inputPrecio && !inputPrecio.checkValidity()) {
        inputPrecio.reportValidity(); 
        return;
    }

    if (inputCantidad && !inputCantidad.checkValidity()) {
        inputCantidad.reportValidity(); 
        return;
    }

    const modal = new bootstrap.Modal(
        document.getElementById('modalConfirmarEdicion')
    );

    modal.show();
});

        btnConfirmarCambios.addEventListener('click', () => {

    if (!formEditar.checkValidity()) {
        formEditar.reportValidity(); 
        return;
    }

    formEditar.requestSubmit();
});
    }

    /* ============================= */
    /* AUTO GROW TEXTAREA */
    /* ============================= */

    const textarea = document.querySelector("textarea.auto-grow");

    if (textarea) {

        textarea.style.height = textarea.scrollHeight + "px";

        textarea.addEventListener("input", () => {

            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";

        });
    }

});