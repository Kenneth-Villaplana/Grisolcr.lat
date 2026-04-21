document.addEventListener("DOMContentLoaded", () => {

    const radios = document.querySelectorAll("input[name='precio']");
    const productos = document.querySelectorAll(".producto-item");

    radios.forEach(radio => {
        radio.addEventListener("change", () => {
            const filtro = radio.value;

            productos.forEach(prod => {
                const precio = parseInt(prod.dataset.precio);
                let mostrar = false;

                switch (filtro) {
                    case "todos":
                        mostrar = true;
                        break;
                    case "1":
                        mostrar = precio >= 5000 && precio <= 30000;
                        break;
                    case "2":
                        mostrar = precio > 30000 && precio <= 80000;
                        break;
                    case "3":
                        mostrar = precio >= 80000;
                        break;
                }

                prod.style.display = mostrar ? "block" : "none";
            });
        });
    });
 const imagenes = document.querySelectorAll(".product-image-clickable");
    const modalElement = document.getElementById("modalImagenProducto");
    const modalTitulo = document.getElementById("modalImagenTitulo");
    const modalImagen = document.getElementById("modalImagenProductoImg");

    if (modalElement && modalTitulo && modalImagen) {
        const modal = new bootstrap.Modal(modalElement);

        imagenes.forEach(img => {
            img.addEventListener("click", function () {
                modalTitulo.textContent = this.dataset.nombre || "Producto";
                modalImagen.src = this.dataset.img || this.src;
                modalImagen.alt = this.dataset.nombre || "Producto";
                modal.show();
            });
        });
    }


});
