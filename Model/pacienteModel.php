<?php
include_once __DIR__ . '/../Model/baseDatos.php';

class PacienteModel {

    private $db;

    public function __construct() {
        $this->db = new BaseDatos();
    }

    public function buscarPorCedula($cedula) {
        $sql = "SELECT PacienteId, nombre, apellido, apellidoDos
                FROM pacientes 
                WHERE cedula = :cedula";
        $stmt = $this->db->conectar()->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
}
?>
