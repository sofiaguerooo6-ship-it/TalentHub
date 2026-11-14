<?php
class Cargo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT c.cod_cargo, c.nombre_cargo, c.banda_min, 
                       c.banda_max, n.nombre_nivel
                FROM cargo c
                JOIN nivel_jerarquico n ON c.cod_nivel = n.cod_nivel
                ORDER BY c.nombre_cargo";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($cod) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM cargo WHERE cod_cargo = ?"
        );
        $stmt->bind_param("s", $cod);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO cargo (cod_cargo, nombre_cargo, banda_min, banda_max, cod_nivel)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdds", $cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel);
        return $stmt->execute();
    }

    public function actualizar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel) {
        $stmt = $this->conexion->prepare(
            "UPDATE cargo SET nombre_cargo=?, banda_min=?, banda_max=?, cod_nivel=?
             WHERE cod_cargo=?"
        );
        $stmt->bind_param("sddss", $nombre_cargo, $banda_min, $banda_max, $cod_nivel, $cod_cargo);
        return $stmt->execute();
    }

    public function eliminar($cod) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM cargo WHERE cod_cargo = ?"
        );
        $stmt->bind_param("s", $cod);
        return $stmt->execute();
    }

    public function obtenerNiveles() {
        return $this->conexion->query(
            "SELECT cod_nivel, nombre_nivel FROM nivel_jerarquico ORDER BY nombre_nivel"
        );
    }
}
?>