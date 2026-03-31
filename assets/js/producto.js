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

    const uploadBox = document.getElementById("uploadBox");
    const inputImagen = document.getElementById("Imagen");
    const previewContainer = document.getElementById("previewContainer");
    const previewImagen = document.getElementById("previewImagen");
    const uploadPlaceholder = document.getElementById("uploadPlaceholder");
    const mensajeImagen = document.getElementById("mensajeImagen");
    const btnSeleccionarImagen = document.getElementById("btnSeleccionarImagen");
    const btnQuitarImagen = document.getElementById("btnQuitarImagen");

    console.log("uploadBox:", uploadBox);
    console.log("inputImagen:", inputImagen);
    console.log("btnSeleccionarImagen:", btnSeleccionarImagen);

    if (!uploadBox || !inputImagen || !btnSeleccionarImagen) {
        console.log("Falta algún elemento del bloque de imagen");
        return;
    }

    const extensionesPermitidas = ["jpg", "jpeg", "png", "webp"];

    function limpiarError() {
        uploadBox.classList.remove("error");
        mensajeImagen.style.display = "none";
        mensajeImagen.textContent = "";
    }

    function mostrarError(mensaje) {
        uploadBox.classList.add("error");
        mensajeImagen.style.display = "block";
        mensajeImagen.textContent = mensaje;
    }

    function mostrarPreview(file) {
        limpiarError();

        const extension = file.name.split(".").pop().toLowerCase();

        if (!extensionesPermitidas.includes(extension)) {
            limpiarSeleccion();
            mostrarError("Formato no permitido. Solo se aceptan JPG, JPEG, PNG y WEBP.");
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            previewImagen.src = e.target.result;
            uploadPlaceholder.classList.add("d-none");
            previewContainer.classList.remove("d-none");
        };

        reader.readAsDataURL(file);
    }

    function limpiarSeleccion() {
        inputImagen.value = "";
        previewImagen.src = "";
        previewContainer.classList.add("d-none");
        uploadPlaceholder.classList.remove("d-none");
        limpiarError();
    }

    btnSeleccionarImagen.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        console.log("CLICK en botón imagen");
        inputImagen.click();
    });

    btnQuitarImagen?.addEventListener("click", (e) => {
        e.stopPropagation();
        limpiarSeleccion();
    });

    uploadBox.addEventListener("click", () => inputImagen.click());

    inputImagen.addEventListener("change", () => {
        const file = inputImagen.files[0];
        if (file) mostrarPreview(file);
    });

    uploadBox.addEventListener("dragover", (e) => {
        e.preventDefault();
        uploadBox.classList.add("dragover");
    });

    uploadBox.addEventListener("dragleave", () => {
        uploadBox.classList.remove("dragover");
    });

    uploadBox.addEventListener("drop", (e) => {
        e.preventDefault();
        uploadBox.classList.remove("dragover");

        const file = e.dataTransfer.files[0];
        if (file) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            inputImagen.files = dataTransfer.files;
            mostrarPreview(file);
        }
    });

});
