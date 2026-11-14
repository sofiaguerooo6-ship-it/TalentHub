<?php
require_once dirname(__DIR__) . '/config/Conexion.php';
require_once dirname(__DIR__) . '/modelos/Solicitud_licencia.php';

class Solicitud_licencia_controlador {
    private $modelo;

    public function __construct() {
        $conexion = Conexion::conectar();
        $this->modelo = new Solicitud_licencia($conexion);
    }

    public function listar() {
        return $this->modelo->listarTodas();
    }

    public function buscar($nro) {
        return $this->modelo->buscarPorId($nro);
    }

    public function obtenerEmpleados() {
        return $this->modelo->obtenerEmpleados();
    }

    public function obtenerTiposLicencia() {
        return $this->modelo->obtenerTiposLicencia();
    }

    public function obtenerEstados() {
        return $this->modelo->obtenerEstados();
    }

    public function crear($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin, $observacion) {
        $errores = $this->validar($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin);
        if (!empty($errores)) {
            return $errores;
        }

        $validacion_dias = $this->modelo->validarDiasDisponibles($legajo, $cod_tipo, $fecha_inicio, $fecha_fin);
        if ($validacion_dias !== "OK") {
            return [$validacion_dias];
        }

        $nro = $this->modelo->crear($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin, $observacion);
        if ($nro) {
            return $nro; // int: nro_solicitud
        }
        return ["Error al crear la solicitud."];
    }

    public function cambiarEstado($nro_solicitud, $cod_estado_nuevo, $legajo_responsable, $observacion) {
        if (empty($cod_estado_nuevo)) {
            return ["Debe seleccionar un estado."];
        }
        if ($this->modelo->cambiarEstado($nro_solicitud, $cod_estado_nuevo, $legajo_responsable, $observacion)) {
            return "OK";
        }
        return ["Error al cambiar el estado."];
    }

    public function eliminar($nro) {
        if ($this->modelo->eliminar($nro)) {
            return "OK";
        }
        return "Error al eliminar.";
    }

    private function validar($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin) {
        $errores = [];
        if (empty($legajo)) {
            $errores[] = "Debe seleccionar un empleado.";
        }
        if (empty($cod_tipo)) {
            $errores[] = "Debe seleccionar un tipo de licencia.";
        }
        if (empty($fecha_solicitud)) {
            $errores[] = "La fecha de solicitud es obligatoria.";
        }
        if (empty($fecha_inicio)) {
            $errores[] = "La fecha de inicio es obligatoria.";
        }
        if (empty($fecha_fin)) {
            $errores[] = "La fecha de fin es obligatoria.";
        }
        if (!empty($fecha_inicio) && !empty($fecha_fin) && $fecha_fin < $fecha_inicio) {
            $errores[] = "La fecha de fin no puede ser menor que la fecha de inicio.";
        }
        if (!empty($fecha_solicitud) && !empty($fecha_inicio)) {
            $min_inicio = date('Y-m-d', strtotime($fecha_solicitud . ' +3 days'));
            if ($fecha_inicio < $min_inicio) {
                $errores[] = "La fecha de inicio debe ser al menos 3 días después de la fecha de solicitud (mínimo: " . date('d/m/Y', strtotime($min_inicio)) . ").";
            }
        }
        $anio_actual = date("Y");
        if (!empty($fecha_inicio) && date("Y", strtotime($fecha_inicio)) != $anio_actual) {
            $errores[] = "La fecha de inicio debe ser del año $anio_actual.";
        }
        if (!empty($fecha_fin) && date("Y", strtotime($fecha_fin)) != $anio_actual) {
            $errores[] = "La fecha de fin debe ser del año $anio_actual.";
        }
        return $errores;
    }
    public function listarPorLegajo($legajo) {
    return $this->modelo->listarPorLegajo($legajo);
}
public function listarPorSupervisor($legajo_supervisor) {
    return $this->modelo->listarPorSupervisor($legajo_supervisor);
}
public function agregarDocumento($nro_solicitud, $cod_tipo_doc, $nombre_archivo) {
    $errores = [];
    if (empty($cod_tipo_doc)) {
        $errores[] = "Debe seleccionar el tipo de documento.";
    }
    if (empty(trim($nombre_archivo))) {
        $errores[] = "El nombre del archivo es obligatorio.";
    }
    if (!empty($errores)) return $errores;
    
    if ($this->modelo->agregarDocumento($nro_solicitud, $cod_tipo_doc, $nombre_archivo)) {
        return "OK";
    }
    return ["Error al agregar el documento."];
}

public function listarDocumentos($nro_solicitud) {
    return $this->modelo->listarDocumentos($nro_solicitud);
}

public function eliminarDocumento($id_doc) {
    if ($this->modelo->eliminarDocumento($id_doc)) {
        return "OK";
    }
    return "Error al eliminar.";
}

public function obtenerTiposDocumento() {
    return $this->modelo->obtenerTiposDocumento();
}
public function obtenerResumenDisponibilidad($legajo) {
    return $this->modelo->obtenerResumenDisponibilidad($legajo);
}

public function validarDiasDisponibles($legajo, $cod_tipo, $fecha_inicio, $fecha_fin) {
    return $this->modelo->validarDiasDisponibles($legajo, $cod_tipo, $fecha_inicio, $fecha_fin);
}
}
?>