function cargarReceta(idExpediente) {
    const url = "/View/recetaParaDoctor.php?IdExpediente=" + idExpediente;

    const ventana = window.open(url, "_blank");

    ventana.onload = function () {
        ventana.print();
    };
}