<?php
class Nivel_jerarquico {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM nivel_jerarquico ORDER BY cod_nivel";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM nivel_jerarquico WHERE cod_nivel = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($cod, $nombre, $descripcion) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO nivel_jerarquico (cod_nivel, nombre_nivel, descripcion)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $cod, $nombre, $descripcion);
        return $stmt->execute();
    }

    public function actualizar($cod, $nombre, $descripcion) {
        $stmt = $this->conexion->prepare(
            "UPDATE nivel_jerarquico SET nombre_nivel = ?, descripcion = ?
             WHERE cod_nivel = ?"
        );
        $stmt->bind_param("sss", $nombre, $descripcion, $cod);
        return $stmt->execute();
    }

    public function eliminar($cod) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM nivel_jerarquico WHERE cod_nivel = ?"
        );
        $stmt->bind_param("s", $cod);
        return $stmt->execute();
    }
}
?>