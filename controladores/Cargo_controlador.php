<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Cargo.php';

class Cargo_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Cargo($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodos();
    }

    public function buscar($cod) {
        return $this->modelo->buscarPorId($cod);
    }

    public function obtenerNiveles() {
        return $this->modelo->obtenerNiveles();
    }

    public function crear($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel) {
        $errores = $this->validar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel)) {
            return "OK";
        }
        return ["Error al crear el cargo."];
    }

    public function actualizar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel) {
        $errores = $this->validar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel)) {
            return "OK";
        }
        return ["Error al actualizar el cargo."];
    }

    public function eliminar($cod) {
        if ($this->modelo->eliminar($cod)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel) {
        $errores = [];
        if (empty(trim($cod_cargo))) {
            $errores[] = "El código del cargo es obligatorio.";
        }
        if (strlen(trim($cod_cargo)) > 10) {
            $errores[] = "El código no puede tener más de 10 caracteres.";
        }
        if (empty(trim($nombre_cargo))) {
            $errores[] = "El nombre del cargo es obligatorio.";
        }
        if (!is_numeric($banda_min) || $banda_min < 0) {
            $errores[] = "La banda mínima debe ser un número positivo.";
        }
        if (!is_numeric($banda_max) || $banda_max < 0) {
            $errores[] = "La banda máxima debe ser un número positivo.";
        }
        if (is_numeric($banda_min) && is_numeric($banda_max) && $banda_max < $banda_min) {
            $errores[] = "La banda máxima no puede ser menor que la mínima.";
        }
        if (empty($cod_nivel)) {
            $errores[] = "Debe seleccionar un nivel jerárquico.";
        }
        return $errores;
    }
}
?>