<?php
include_once __DIR__ . '/../Model/recetaModel.php';

class RecetaController {

    public static function listarRecetasPaciente($pacienteId) {
        return RecetaModel::obtenerRecetasPorPaciente($pacienteId);
    }

    public static function verReceta($expedienteId) {
        return RecetaModel::obtenerRecetaPorExpediente($expedienteId);
    }
}
?>