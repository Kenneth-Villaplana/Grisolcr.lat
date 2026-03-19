
function cargarReceta(idExpediente) {
    fetch("/View/recetaParaDoctor.php?IdExpediente=" + idExpediente)
        .then(response => response.text())
        .then(html => {
            document.getElementById("contenedorReceta").innerHTML = html;

            const modal = new bootstrap.Modal(document.getElementById("modalImprimir"));
            modal.show();
        })
        .catch(error => console.error("Error cargando la receta:", error));
}

function imprimirReceta() {
    const contenedor = document.getElementById("contenedorReceta");

    if (!contenedor || contenedor.innerHTML.trim() === "") {
        alert("No hay contenido para imprimir");
        return;
    }

    const ventana = window.open('', '', 'width=900,height=700');

    ventana.document.write(`
        <html>
        <head>
            <title>Receta</title>
            <link rel="stylesheet" href="/assets/css/POS.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        </head>
        <body>
            ${contenedor.innerHTML}
        </body>
        </html>
    `);

    ventana.document.close();

    ventana.onload = function () {
        ventana.print();
    };
}
