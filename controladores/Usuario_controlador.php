<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Usuario.php';

class Usuario_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Usuario($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodos();
    }

    public function buscar($id) {
        return $this->modelo->buscarPorId($id);
    }

    public function obtenerRoles() {
        return $this->modelo->obtenerRoles();
    }

    public function obtenerEmpleados() {
        return $this->modelo->obtenerEmpleados();
    }

    public function crear($legajo, $id_rol, $nombre_usuario, $contrasena, $confirmar, $fecha_alta) {
        $errores = $this->validar($nombre_usuario, $contrasena, $confirmar, $id_rol);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($legajo, $id_rol, $nombre_usuario, $contrasena, $fecha_alta)) {
            return "OK";
        }
        return ["Error al crear el usuario. El nombre de usuario ya existe."];
    }

    public function cambiarEstado($id, $estado, $fecha_baja) {
        if ($this->modelo->cambiarEstado($id, $estado, $fecha_baja)) {
            return "OK";
        }
        return "Error al cambiar el estado.";
    }

    public function eliminar($id) {
        if ($this->modelo->eliminar($id)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($nombre_usuario, $contrasena, $confirmar, $id_rol) {
        $errores = [];
        if (empty(trim($nombre_usuario))) {
            $errores[] = "El nombre de usuario es obligatorio.";
        }
        if (strlen(trim($nombre_usuario)) < 4) {
            $errores[] = "El nombre de usuario debe tener al menos 4 caracteres.";
        }
        if (empty($contrasena)) {
            $errores[] = "La contraseña es obligatoria.";
        }
        if (strlen($contrasena) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres.";
        }
        if ($contrasena !== $confirmar) {
            $errores[] = "Las contraseñas no coinciden.";
        }
        if (empty($id_rol)) {
            $errores[] = "Debe seleccionar un rol.";
        }
        return $errores;
    }
}
?>