<?php
class Empleado {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    private function ordenSQL($orden, $prefijo = '') {
        $p = $prefijo ? "$prefijo." : '';
        $map = [
            'apellido_asc'  => "{$p}apellido ASC,  {$p}nombre ASC",
            'apellido_desc' => "{$p}apellido DESC, {$p}nombre DESC",
            'nombre_asc'    => "{$p}nombre ASC,    {$p}apellido ASC",
            'nombre_desc'   => "{$p}nombre DESC,   {$p}apellido DESC",
        ];
        return $map[$orden] ?? "{$p}apellido ASC, {$p}nombre ASC";
    }

    public function listarTodos($orden = 'apellido_asc') {
        $order = $this->ordenSQL($orden, 'e');
        $sql = "SELECT e.legajo, e.dni, e.nombre, e.apellido,
                       e.email, e.telefono, e.fecha_ingreso
                FROM empleado e
                ORDER BY $order";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($legajo) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM empleado WHERE legajo = ?"
        );
        $stmt->bind_param("i", $legajo);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso) {
        $stmt = $this->conexion->prepare(
            "INSERT INTO empleado (dni, nombre, apellido, email, telefono, fecha_ingreso)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso);
        if ($stmt->execute()) {
            return $this->conexion->insert_id;
        }
        return false;
    }

    public function actualizar($legajo, $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso) {
        $stmt = $this->conexion->prepare(
            "UPDATE empleado SET dni=?, nombre=?, apellido=?, email=?, 
             telefono=?, fecha_ingreso=? WHERE legajo=?"
        );
        $stmt->bind_param("ssssssi", $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso, $legajo);
        return $stmt->execute();
    }

    public function eliminar($legajo) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM empleado WHERE legajo = ?"
        );
        $stmt->bind_param("i", $legajo);
        return $stmt->execute();
    }

    public function obtenerUltimoLegajo() {
        $resultado = $this->conexion->query("SELECT MAX(legajo) AS ultimo FROM empleado");
        $fila = $resultado->fetch_assoc();
        return $fila["ultimo"];
    }
    public function listarPorSupervisor($legajo_supervisor, $orden = 'apellido_asc') {
        $order = $this->ordenSQL($orden, 'e');
        $sql = "SELECT DISTINCT e.legajo, e.dni, e.nombre, e.apellido,
                       e.email, e.telefono, e.fecha_ingreso
                FROM empleado e
                JOIN historial_cargo h ON e.legajo = h.legajo
                JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
                WHERE sh.legajo_supervisor = ?
                AND h.fecha_fin IS NULL
                ORDER BY $order";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $legajo_supervisor);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function buscarPorNombre($busqueda, $campo, $orden = 'apellido_asc') {
        $like = "$busqueda%";
        $columna = ($campo == "nombre") ? "nombre" : "apellido";
        $order = $this->ordenSQL($orden);
        $sql = "SELECT legajo, dni, nombre, apellido, email, telefono, fecha_ingreso
                FROM empleado
                WHERE $columna LIKE ?
                ORDER BY $order";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("s", $like);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function buscarPorSupervisorYNombre($legajo_supervisor, $busqueda, $campo, $orden = 'apellido_asc') {
        $like = "$busqueda%";
        $columna = ($campo == "nombre") ? "e.nombre" : "e.apellido";
        $order = $this->ordenSQL($orden, 'e');
        $sql = "SELECT DISTINCT e.legajo, e.dni, e.nombre, e.apellido,
                       e.email, e.telefono, e.fecha_ingreso
                FROM empleado e
                JOIN historial_cargo h ON e.legajo = h.legajo
                JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
                WHERE sh.legajo_supervisor = ?
                AND h.fecha_fin IS NULL
                AND $columna LIKE ?
                ORDER BY $order";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $legajo_supervisor, $like);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>