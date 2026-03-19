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