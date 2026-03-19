function imprimirReceta() {
    const contenido = document.getElementById("contenedorReceta").innerHTML;

    const ventana = window.open('', '', 'width=800,height=600');

    ventana.document.write(`
        <html>
        <head>
            <title>Imprimir Receta</title>
            <link rel="stylesheet" href="/assets/css/POS.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        </head>
        <body>
            ${contenido}
        </body>
        </html>
    `);

    ventana.document.close();

    ventana.onload = function () {
        ventana.print();
        ventana.close();
    };
}