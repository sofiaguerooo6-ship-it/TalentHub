<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Nivel_jerarquico.php';

class Nivel_jerarquico_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Nivel_jerarquico($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodos();
    }

    public function buscar($cod) {
        return $this->modelo->buscarPorId($cod);
    }

    public function crear($cod, $nombre, $descripcion) {
        $errores = $this->validar($cod, $nombre);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($cod, $nombre, $descripcion)) {
            return "OK";
        }
        return ["Error al crear el nivel jerárquico."];
    }

    public function actualizar($cod, $nombre, $descripcion) {
        $errores = $this->validar($cod, $nombre);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($cod, $nombre, $descripcion)) {
            return "OK";
        }
        return ["Error al actualizar el nivel jerárquico."];
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
        if (strlen(trim($nombre)) > 80) {
            $errores[] = "El nombre no puede tener más de 80 caracteres.";
        }
        return $errores;
    }
}
?>