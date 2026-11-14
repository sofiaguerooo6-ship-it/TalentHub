<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Historial_cargo.php';

class Historial_cargo_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Historial_cargo($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodos();
    }

    public function buscar($id) {
        return $this->modelo->buscarPorId($id);
    }

    public function obtenerSupervisores($id_hist) {
        return $this->modelo->obtenerSupervisores($id_hist);
    }

    public function obtenerEmpleados() {
        return $this->modelo->obtenerEmpleados();
    }

    public function obtenerSupervisoresUsuarios() {
        return $this->modelo->obtenerSupervisoresUsuarios();
    }

    public function actualizar($id_hist, $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin) {
        $errores = [];
        if (empty($legajo))      $errores[] = "Debe seleccionar un empleado.";
        if (empty($cod_cargo))   $errores[] = "Debe seleccionar un cargo.";
        if (empty($cod_depto))   $errores[] = "Debe seleccionar un departamento.";
        if (empty($fecha_inicio)) $errores[] = "La fecha de inicio es obligatoria.";
        if (!empty($errores)) return $errores;

        if ($this->modelo->actualizar($id_hist, $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin)) {
            return "OK";
        }
        return ["Error al actualizar el registro."];
    }

    public function actualizarFechaFin($id_hist, $fecha_fin) {
        if ($this->modelo->actualizarFechaFin($id_hist, $fecha_fin)) {
            return "OK";
        }
        return ["Error al actualizar la fecha de fin."];
    }

    public function obtenerCargos() {
        return $this->modelo->obtenerCargos();
    }

    public function obtenerDepartamentos() {
        return $this->modelo->obtenerDepartamentos();
    }

    public function crear($legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin, $supervisores) {
        $errores = $this->validar($legajo, $cod_cargo, $cod_depto, $fecha_inicio);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin, $supervisores)) {
            return "OK";
        }
        return ["Error al crear el historial de cargo."];
    }

    public function cerrarPuesto($id_hist, $fecha_fin) {
        if (empty($fecha_fin)) {
            return ["La fecha de fin es obligatoria."];
        }
        if ($this->modelo->cerrarPuesto($id_hist, $fecha_fin)) {
            return "OK";
        }
        return ["Error al cerrar el puesto."];
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($legajo, $cod_cargo, $cod_depto, $fecha_inicio) {
        $errores = [];
        if (empty($legajo)) {
            $errores[] = "Debe seleccionar un empleado.";
        }
        if (empty($cod_cargo)) {
            $errores[] = "Debe seleccionar un cargo.";
        }
        if (empty($cod_depto)) {
            $errores[] = "Debe seleccionar un departamento.";
        }
        if (empty($fecha_inicio)) {
            $errores[] = "La fecha de inicio es obligatoria.";
        }
        return $errores;
    }
    public function listarPorSupervisor($legajo_supervisor) {
    return $this->modelo->listarPorSupervisor($legajo_supervisor);
}
public function actualizarSupervisores($id_hist, $supervisores) {
    if ($this->modelo->actualizarSupervisores($id_hist, $supervisores)) {
        return "OK";
    }
    return "Error al actualizar los supervisores.";
}
}
?>