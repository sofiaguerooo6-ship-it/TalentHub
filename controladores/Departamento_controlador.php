<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Departamento.php';

class Departamento_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Departamento($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodos();
    }

    public function buscar($cod) {
        return $this->modelo->buscarPorId($cod);
    }

    public function obtenerLocalidades() {
        return $this->modelo->obtenerLocalidades();
    }

    public function obtenerLocalidadesDepto($cod) {
        return $this->modelo->obtenerLocalidadesDepto($cod);
    }

    public function crear($cod_depto, $nombre_depto, $localidades, $sede_principal) {
        $errores = $this->validar($cod_depto, $nombre_depto, $localidades);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($cod_depto, $nombre_depto, $localidades, $sede_principal)) {
            return "OK";
        }
        return ["Error al crear el departamento."];
    }

    public function actualizar($cod_depto, $nombre_depto, $localidades, $sede_principal) {
        $errores = $this->validar($cod_depto, $nombre_depto, $localidades);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($cod_depto, $nombre_depto, $localidades, $sede_principal)) {
            return "OK";
        }
        return ["Error al actualizar el departamento."];
    }

    public function eliminar($cod) {
        if ($this->modelo->eliminar($cod)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($cod_depto, $nombre_depto, $localidades) {
        $errores = [];
        if (empty(trim($cod_depto))) {
            $errores[] = "El código del departamento es obligatorio.";
        }
        if (strlen(trim($cod_depto)) > 10) {
            $errores[] = "El código no puede tener más de 10 caracteres.";
        }
        if (empty(trim($nombre_depto))) {
            $errores[] = "El nombre del departamento es obligatorio.";
        }
        if (empty($localidades)) {
            $errores[] = "Debe seleccionar al menos una localidad.";
        }
        return $errores;
    }
}
?>