<?php
class Departamento {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT d.cod_depto, d.nombre_depto,
                       GROUP_CONCAT(l.nombre_ciudad SEPARATOR ', ') AS localidades
                FROM departamento d
                LEFT JOIN departamento_localidad dl ON d.cod_depto = dl.cod_depto
                LEFT JOIN localidad l ON dl.cod_postal = l.cod_postal
                GROUP BY d.cod_depto, d.nombre_depto
                ORDER BY d.nombre_depto";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM departamento WHERE cod_depto = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerLocalidadesDepto($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT cod_postal FROM departamento_localidad WHERE cod_depto = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($cod_depto, $nombre_depto, $localidades, $sede_principal) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO departamento (cod_depto, nombre_depto) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $cod_depto, $nombre_depto);
        if (!$stmt->execute()) return false;

        foreach ($localidades as $cod_postal) {
            $es_sede = ($cod_postal == $sede_principal) ? 1 : 0;
            $stmt2 = $this->conexion->prepare(
                "INSERT INTO departamento_localidad (cod_depto, cod_postal, es_sede_principal)
                 VALUES (?, ?, ?)"
            );
            $stmt2->bind_param("ssi", $cod_depto, $cod_postal, $es_sede);
            $stmt2->execute();
        }
        return true;
    }

    public function actualizar($cod_depto, $nombre_depto, $localidades, $sede_principal) {
        $stmt = $this->conexion->prepare(
            "UPDATE departamento SET nombre_depto = ? WHERE cod_depto = ?"
        );
        $stmt->bind_param("ss", $nombre_depto, $cod_depto);
        if (!$stmt->execute()) return false;

        $stmt2 = $this->conexion->prepare(
            "DELETE FROM departamento_localidad WHERE cod_depto = ?"
        );
        $stmt2->bind_param("s", $cod_depto);
        $stmt2->execute();

        foreach ($localidades as $cod_postal) {
            $es_sede = ($cod_postal == $sede_principal) ? 1 : 0;
            $stmt3 = $this->conexion->prepare(
                "INSERT INTO departamento_localidad (cod_depto, cod_postal, es_sede_principal)
                 VALUES (?, ?, ?)"
            );
            $stmt3->bind_param("ssi", $cod_depto, $cod_postal, $es_sede);
            $stmt3->execute();
        }
        return true;
    }

    public function eliminar($cod) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM departamento_localidad WHERE cod_depto = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();

        $stmt2 = $this->conexion->prepare(
            "DELETE FROM departamento WHERE cod_depto = ?"
        );
        $stmt2->bind_param("s", $cod);
        return $stmt2->execute();
    }

    public function obtenerLocalidades() {
        return $this->conexion->query(
            "SELECT cod_postal, nombre_ciudad FROM localidad ORDER BY nombre_ciudad"
        );
    }
}
?>