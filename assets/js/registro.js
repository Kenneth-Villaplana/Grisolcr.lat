function ConsultarNombre() {
    let cedula = $("#Cedula").val().trim();

    // Limpia los campos antes de buscar
    $("#Nombre").val("");
    $("#Apellido").val("");
    $("#ApellidoDos").val("");

    if (cedula.length >= 9) {
        $.ajax({
            url: "https://apis.gometa.org/cedulas/" + cedula,
            method: "GET",
            dataType: "json",
            success: function (data) {
                console.log("Respuesta API:", data);

                if (data && data.results && data.results.length > 0) {
                    let persona = data.results[0];

                   
                    const nombre = persona.firstname || "";
                    const apellido1 = persona.lastname1 || "";
                    const apellido2 = persona.lastname2 || "";

                    $("#Nombre").val(nombre.trim());
                    $("#Apellido").val(apellido1.trim());
                    $("#ApellidoDos").val(apellido2.trim());
                } else {
                    console.warn("⚠️ No se encontró información para esta cédula.");
                }
            },
            error: function (xhr, status, error) {
                console.error("⚠️ Error al conectar con la API:", error);
            }
        });
    }
}