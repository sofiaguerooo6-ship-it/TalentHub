<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Evaluacion_desempeno.php';

class Evaluacion_desempeno_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Evaluacion_desempeno($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodas();
    }

    public function buscar($id) {
        return $this->modelo->buscarPorId($id);
    }

    public function obtenerEmpleados() {
        return $this->modelo->obtenerEmpleados();
    }

    public function crear($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario) {
        $errores = $this->validar($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario)) {
            return "OK";
        }
        return ["Error al crear la evaluación."];
    }

    public function actualizar($id, $legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario) {
        $errores = $this->validar($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($id, $legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario)) {
            return "OK";
        }
        return ["Error al actualizar la evaluación."];
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion) {
        $errores = [];
        if (empty($legajo_evaluado)) {
            $errores[] = "Debe seleccionar el empleado evaluado.";
        }
        if (empty($legajo_evaluador)) {
            $errores[] = "Debe seleccionar el evaluador.";
        }
        if ($legajo_evaluado == $legajo_evaluador) {
            $errores[] = "El evaluado y el evaluador no pueden ser la misma persona.";
        }
        if (empty(trim($periodo))) {
            $errores[] = "El período es obligatorio.";
        }
        if (!is_numeric($puntuacion) || $puntuacion < 0 || $puntuacion > 100) {
            $errores[] = "La puntuación debe ser un número entre 0 y 100.";
        }
        return $errores;
    }
    public function listarPorLegajo($legajo) {
    return $this->modelo->listarPorLegajo($legajo);
}
public function listarPorSupervisor($legajo_supervisor) {
    return $this->modelo->listarPorSupervisor($legajo_supervisor);
}
}
?>