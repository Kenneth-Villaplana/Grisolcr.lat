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

});