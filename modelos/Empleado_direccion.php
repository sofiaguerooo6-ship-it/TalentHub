<?php
class Empleado_direccion {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarPorLegajo($legajo) {
        $stmt = $this->conexion->prepare(
            "SELECT ed.id_direccion, ed.calle, ed.altura, ed.es_principal,
                    l.nombre_ciudad, p.nombre_provincia
             FROM empleado_direccion ed
             JOIN localidad l ON ed.cod_postal = l.cod_postal
             JOIN provincia p ON l.cod_provincia = p.cod_provincia
             WHERE ed.legajo = ?
             ORDER BY ed.es_principal DESC"
        );
        $stmt->bind_param("i", $legajo);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($legajo, $cod_postal, $calle, $altura, $es_principal) {
        if ($es_principal) {
            $stmt = $this->conexion->prepare(
                "UPDATE empleado_direccion SET es_principal = 0 WHERE legajo = ?"
            );
            $stmt->bind_param("i", $legajo);
            $stmt->execute();
        }
        $stmt = $this->conexion->prepare(
            "INSERT INTO empleado_direccion (legajo, cod_postal, calle, altura, es_principal)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssi", $legajo, $cod_postal, $calle, $altura, $es_principal);
        return $stmt->execute();
    }

    public function eliminar($id_direccion) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM empleado_direccion WHERE id_direccion = ?"
        );
        $stmt->bind_param("i", $id_direccion);
        return $stmt->execute();
    }

    public function obtenerLocalidades() {
        return $this->conexion->query(
            "SELECT cod_postal, nombre_ciudad FROM localidad ORDER BY nombre_ciudad"
        );
    }
}
?>