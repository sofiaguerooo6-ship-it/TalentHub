<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Localidad.php';

class Localidad_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Localidad($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodas();
    }

    public function buscar($cod) {
        return $this->modelo->buscarPorId($cod);
    }

    public function obtenerProvincias() {
        return $this->modelo->obtenerProvincias();
    }

    public function crear($cod_postal, $nombre_ciudad, $cod_provincia) {
        $errores = $this->validar($cod_postal, $nombre_ciudad, $cod_provincia);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->crear($cod_postal, $nombre_ciudad, $cod_provincia)) {
            return "OK";
        }
        return ["Error al crear la localidad."];
    }

    public function actualizar($cod_postal, $nombre_ciudad, $cod_provincia) {
        $errores = $this->validar($cod_postal, $nombre_ciudad, $cod_provincia);
        if (!empty($errores)) {
            return $errores;
        }
        if ($this->modelo->actualizar($cod_postal, $nombre_ciudad, $cod_provincia)) {
            return "OK";
        }
        return ["Error al actualizar la localidad."];
    }

    public function eliminar($cod) {
        if ($this->modelo->eliminar($cod)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($cod_postal, $nombre_ciudad, $cod_provincia) {
        $errores = [];
        if (empty(trim($cod_postal))) {
            $errores[] = "El código postal es obligatorio.";
        }
        if (strlen(trim($cod_postal)) > 10) {
            $errores[] = "El código postal no puede tener más de 10 caracteres.";
        }
        if (empty(trim($nombre_ciudad))) {
            $errores[] = "El nombre de la ciudad es obligatorio.";
        }
        if (strlen(trim($nombre_ciudad)) > 100) {
            $errores[] = "El nombre no puede tener más de 100 caracteres.";
        }
        if (empty($cod_provincia)) {
            $errores[] = "Debe seleccionar una provincia.";
        }
        return $errores;
    }
}
?>