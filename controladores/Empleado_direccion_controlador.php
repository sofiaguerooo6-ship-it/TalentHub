<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Empleado_direccion.php';

class Empleado_direccion_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Empleado_direccion($conexion);
    }

    public function listarPorLegajo($legajo) {
        return $this->modelo->listarPorLegajo($legajo);
    }

    public function obtenerLocalidades() {
        return $this->modelo->obtenerLocalidades();
    }

    public function crear($legajo, $cod_postal, $calle, $altura, $es_principal) {
        $errores = [];
        if (empty($cod_postal)) {
            $errores[] = "Debe seleccionar una localidad.";
        }
        if (empty(trim($calle))) {
            $errores[] = "La calle es obligatoria.";
        }
        if (empty(trim($altura))) {
            $errores[] = "La altura es obligatoria.";
        }
        if (!empty($errores)) return $errores;

        if ($this->modelo->crear($legajo, $cod_postal, $calle, $altura, $es_principal)) {
            return "OK";
        }
        return ["Error al agregar la dirección."];
    }

    public function eliminar($id_direccion) {
        if ($this->modelo->eliminar($id_direccion)) {
            return "OK";
        }
        return "Error al eliminar.";
    }
}
?>