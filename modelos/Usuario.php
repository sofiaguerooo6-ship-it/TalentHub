<?php
class Usuario {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT u.id_usuario, u.nombre_usuario, u.estado,
                       u.fecha_alta, u.fecha_baja,
                       r.nombre_rol,
                       CONCAT(e.nombre, ' ', e.apellido) AS empleado
                FROM usuario u
                JOIN rol r ON u.id_rol = r.id_rol
                LEFT JOIN empleado e ON u.legajo = e.legajo
                ORDER BY u.nombre_usuario";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($id) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM usuario WHERE id_usuario = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($legajo, $id_rol, $nombre_usuario, $contrasena, $fecha_alta) {
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt = $this->conexion->prepare(
            "INSERT INTO usuario (legajo, id_rol, nombre_usuario, contrasena, estado, fecha_alta)
             VALUES (?, ?, ?, ?, 1, ?)"
        );
        $stmt->bind_param("iisss", $legajo, $id_rol, $nombre_usuario, $hash, $fecha_alta);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado, $fecha_baja) {
        $stmt = $this->conexion->prepare(
            "UPDATE usuario SET estado = ?, fecha_baja = ? WHERE id_usuario = ?"
        );
        $stmt->bind_param("isi", $estado, $fecha_baja, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM usuario WHERE id_usuario = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerRoles() {
        return $this->conexion->query(
            "SELECT id_rol, nombre_rol FROM rol ORDER BY nombre_rol"
        );
    }

    public function obtenerEmpleados() {
        return $this->conexion->query(
            "SELECT e.legajo, CONCAT(e.nombre, ' ', e.apellido) AS nombre_completo
             FROM empleado e
             LEFT JOIN usuario u ON e.legajo = u.legajo
             WHERE u.legajo IS NULL
             ORDER BY e.apellido"
        );
    }
}
?>