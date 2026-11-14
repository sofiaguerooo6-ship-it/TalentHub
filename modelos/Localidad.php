<?php
class Localidad {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT l.cod_postal, l.nombre_ciudad, p.nombre_provincia
                FROM localidad l
                JOIN provincia p ON l.cod_provincia = p.cod_provincia
                ORDER BY l.nombre_ciudad";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM localidad WHERE cod_postal = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($cod_postal, $nombre_ciudad, $cod_provincia) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO localidad (cod_postal, nombre_ciudad, cod_provincia)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $cod_postal, $nombre_ciudad, $cod_provincia);
        return $stmt->execute();
    }

    public function actualizar($cod_postal, $nombre_ciudad, $cod_provincia) {
        $stmt = $this->conexion->prepare(
            "UPDATE localidad SET nombre_ciudad = ?, cod_provincia = ?
             WHERE cod_postal = ?"
        );
        $stmt->bind_param("sss", $nombre_ciudad, $cod_provincia, $cod_postal);
        return $stmt->execute();
    }

    public function eliminar($cod) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM localidad WHERE cod_postal = ?"
        );
        $stmt->bind_param("s", $cod);
        return $stmt->execute();
    }

    public function obtenerProvincias() {
        return $this->conexion->query(
            "SELECT cod_provincia, nombre_provincia FROM provincia ORDER BY nombre_provincia"
        );
    }
}
?>