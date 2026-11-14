<?php
class Provincia {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT * FROM provincia ORDER BY nombre_provincia";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM provincia WHERE cod_provincia = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($cod, $nombre) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO provincia (cod_provincia, nombre_provincia) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $cod, $nombre);
        return $stmt->execute();
    }

    public function actualizar($cod, $nombre) {
        $stmt = $this->conexion->prepare(
            "UPDATE provincia SET nombre_provincia = ? WHERE cod_provincia = ?"
        );
        $stmt->bind_param("ss", $nombre, $cod);
        return $stmt->execute();
    }

    public function eliminar($cod) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM provincia WHERE cod_provincia = ?"
        );
        $stmt->bind_param("s", $cod);
        return $stmt->execute();
    }
}
?>