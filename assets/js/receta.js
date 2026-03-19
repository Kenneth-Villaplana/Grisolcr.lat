function cargarReceta(idExpediente) {
    fetch("/View/recetaParaDoctor.php?IdExpediente=" + idExpediente)
        .then(response => response.text())
        .then(html => {
            document.getElementById("contenedorReceta").innerHTML = html;
            new bootstrap.Modal(document.getElementById("modalImprimir")).show();
        })
        .catch(error => console.error("Error cargando la receta:", error));
}