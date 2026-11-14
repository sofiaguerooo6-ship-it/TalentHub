<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Provincia.php';

class Provincia_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Provincia($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodas();
    }

    public function buscar($cod) {
        return $this->modelo->buscarPorId($cod);
    }

    public function crear($cod, $nombre) {
        $errores = $this->validar($cod, $nombre);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($cod, $nombre)) {
            return "OK";
        }
        return ["Error al crear la provincia."];
    }

    public function actualizar($cod, $nombre) {
        $errores = $this->validar($cod, $nombre);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($cod, $nombre)) {
            return "OK";
        }
        return ["Error al actualizar la provincia."];
    }

    public function eliminar($cod) {
        if ($this->modelo->eliminar($cod)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($cod, $nombre) {
        $errores = [];
        if (empty(trim($cod))) {
            $errores[] = "El código es obligatorio.";
        }
        if (strlen(trim($cod)) > 10) {
            $errores[] = "El código no puede tener más de 10 caracteres.";
        }
        if (empty(trim($nombre))) {
            $errores[] = "El nombre es obligatorio.";
        }
        if (strlen(trim($nombre)) > 100) {
            $errores[] = "El nombre no puede tener más de 100 caracteres.";
        }
        return $errores;
    }
}
?>