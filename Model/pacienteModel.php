<?php
include_once __DIR__ . '/baseDatos.php';

class PacienteModel {

   public function buscarPorCedula($cedula) 
    {
        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL BuscarPacientePorCedulaUsuario(?)");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();
        CerrarBD($conn);

        return $data ?: null;
    }
// ===========================================================
    // NUEVO MÉTODO PARA EL AGENDAMIENTO DE CITAS
    // ===========================================================
    public function buscarPacienteParaCita($cedula) {

        $conn = AbrirBD();

        $stmt = $conn->prepare("CALL p_BuscarPacientePorCedulaUsuario(?)");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $stmt->close();
        CerrarBD($conn);

        return $data ?: null;
    }
}
?>