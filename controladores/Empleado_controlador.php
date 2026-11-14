<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Empleado.php';

class Empleado_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Empleado($conexion);
    }

    public function listar($orden = 'apellido_asc') {
        return $this->modelo->listarTodos($orden);
    }

    public function buscar($legajo) {
        return $this->modelo->buscarPorId($legajo);
    }

    public function crear($dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso) {
        $errores = $this->validar($dni, $nombre, $apellido, $email, $fecha_ingreso);
        if (!empty($errores)) {
            return $errores;
        }
        $legajo = $this->modelo->crear($dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso);
        if ($legajo) {
            return $legajo; // devuelve el legajo generado
        }
        return ["Error al crear el empleado. El DNI o email ya existe."];
    }

    public function actualizar($legajo, $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso) {
        $errores = $this->validar($dni, $nombre, $apellido, $email, $fecha_ingreso);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($legajo, $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso)) {
            return "OK";
        }
        return ["Error al actualizar el empleado."];
    }

    public function eliminar($legajo) {
        if ($this->modelo->eliminar($legajo)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($dni, $nombre, $apellido, $email, $fecha_ingreso) {
        $errores = [];
        if (empty(trim($dni))) {
            $errores[] = "El DNI es obligatorio.";
        }
        if (!preg_match('/^[0-9]{7,8}$/', trim($dni))) {
            $errores[] = "El DNI debe tener entre 7 y 8 números sin puntos ni espacios.";
        }
        if (empty(trim($nombre))) {
            $errores[] = "El nombre es obligatorio.";
        }
        if (empty(trim($apellido))) {
            $errores[] = "El apellido es obligatorio.";
        }
        if (empty(trim($email))) {
            $errores[] = "El email es obligatorio.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El email no tiene un formato válido.";
        }
        if (empty($fecha_ingreso)) {
            $errores[] = "La fecha de ingreso es obligatoria.";
        }
        return $errores;
    }
    public function listarPorSupervisor($legajo_supervisor, $orden = 'apellido_asc') {
        return $this->modelo->listarPorSupervisor($legajo_supervisor, $orden);
    }

    public function buscarPorNombre($busqueda, $campo, $orden = 'apellido_asc') {
        return $this->modelo->buscarPorNombre($busqueda, $campo, $orden);
    }

    public function buscarPorSupervisorYNombre($legajo_supervisor, $busqueda, $campo, $orden = 'apellido_asc') {
        return $this->modelo->buscarPorSupervisorYNombre($legajo_supervisor, $busqueda, $campo, $orden);
    }
}
?>