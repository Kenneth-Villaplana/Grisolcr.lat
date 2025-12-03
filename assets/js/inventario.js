document.addEventListener('DOMContentLoaded', () => {

    // filtro id
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const inputCodigo = document.getElementById('codigoInput');

    if (btnBuscar && inputCodigo) {
        btnBuscar.addEventListener('click', () => {
            const codigo = inputCodigo.value.trim();

            if (codigo === '') {
                alert('Ingrese un ID para buscar.');
                return;
            }

            const basePath =
                window.location.pathname.substring(
                    window.location.pathname.lastIndexOf('/') + 1
                ) || 'inventario.php';

            window.location.href = `${basePath}?idProducto=${encodeURIComponent(codigo)}`;
        });

        inputCodigo.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                btnBuscar.click();
            }
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', () => {
            const basePath =
                window.location.pathname.substring(
                    window.location.pathname.lastIndexOf('/') + 1
                ) || 'inventario.php';

            window.location.href = basePath;
        });
    }

    // filtro nombre
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const filtro = this.value.toLowerCase().trim();
            const productos = document.querySelectorAll('#listaProductos .producto');

            productos.forEach(producto => {
                const nombreEl = producto.querySelector('.card-title');
                if (!nombreEl) return;

                const nombre = nombreEl.textContent.toLowerCase();

                producto.style.display = nombre.includes(filtro) ? '' : 'none';
            });
        });
    }

   
    //  confirmar eliminar
   
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

    
    const btnAbrirModalEditar = document.getElementById('btnAbrirModalEditar');
    const btnConfirmarCambios = document.getElementById('btnConfirmarCambios');
    const formEditar = document.getElementById('formEditarProducto');

    if (btnAbrirModalEditar && btnConfirmarCambios && formEditar) {

        btnAbrirModalEditar.addEventListener('click', () => {
            const modal = new bootstrap.Modal(
                document.getElementById('modalConfirmarEdicion')
            );
            modal.show();
        });

        btnConfirmarCambios.addEventListener('click', () => {
            formEditar.submit();
        });
    }
    const textarea = document.querySelector("textarea.auto-grow");

    if (textarea) {
        
        textarea.style.height = textarea.scrollHeight + "px";

        
        textarea.addEventListener("input", () => {
            textarea.style.height = "auto";
            textarea.style.height = textarea.scrollHeight + "px";
        });
    }
});