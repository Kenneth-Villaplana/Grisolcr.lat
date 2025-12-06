<?php
include_once __DIR__ . '/../Model/baseDatos.php';

class PacienteModel {

    private $db;

    public function __construct() {
        $this->db = new BaseDatos();
    }

    public function buscarPorCedula($cedula) {
        $dbConn = $this->db->conectar();

        
        $stmt = $dbConn->prepare("CALL BuscarPacientePorCedulaUsuario(:cedula)");
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        return $resultado ?: null;
    }
}
?>