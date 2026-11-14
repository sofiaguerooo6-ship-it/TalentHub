<?php
class Historial_cargo {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT h.id_hist,
                       CONCAT(e.nombre, ' ', e.apellido) AS empleado,
                       c.nombre_cargo AS cargo,
                       d.nombre_depto AS departamento,
                       h.fecha_inicio, h.fecha_fin,
                       GROUP_CONCAT(CONCAT(s.nombre, ' ', s.apellido) SEPARATOR ', ') AS supervisores
                FROM historial_cargo h
                JOIN empleado e ON h.legajo = e.legajo
                JOIN cargo c ON h.cod_cargo = c.cod_cargo
                JOIN departamento d ON h.cod_depto = d.cod_depto
                LEFT JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
                LEFT JOIN empleado s ON sh.legajo_supervisor = s.legajo
                GROUP BY h.id_hist, e.nombre, e.apellido, c.nombre_cargo, 
                         d.nombre_depto, h.fecha_inicio, h.fecha_fin
                ORDER BY h.fecha_inicio DESC";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($id) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM historial_cargo WHERE id_hist = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function obtenerSupervisores($id_hist) {
        $stmt = $this->conexion->prepare(
            "SELECT legajo_supervisor FROM supervisor_historial WHERE id_hist = ?"
        );
        $stmt->bind_param("i", $id_hist);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin, $supervisores) {
        // Transacción: historial_cargo + supervisor_historial deben crearse juntos.
        // Si uno falla, se deshace el otro (ROLLBACK).
        // El trigger trg_validar_cargo_unico_activo se dispara en el INSERT y cancela si ya hay cargo activo.
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO historial_cargo (legajo, cod_cargo, cod_depto, fecha_inicio, fecha_fin)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issss", $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin);
            $stmt->execute();
            $id_hist = $this->conexion->insert_id; // ID del historial recién creado

            foreach ($supervisores as $legajo_supervisor) {
                $stmt2 = $this->conexion->prepare(
                    "INSERT INTO supervisor_historial (id_hist, legajo_supervisor) VALUES (?, ?)"
                );
                $stmt2->bind_param("ii", $id_hist, $legajo_supervisor);
                $stmt2->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    public function actualizar($id_hist, $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin) {
        $stmt = $this->conexion->prepare(
            "UPDATE historial_cargo
             SET legajo = ?, cod_cargo = ?, cod_depto = ?, fecha_inicio = ?, fecha_fin = ?
             WHERE id_hist = ?"
        );
        $fecha_fin_val = ($fecha_fin === "" || $fecha_fin === null) ? null : $fecha_fin;
        $stmt->bind_param("issssi", $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin_val, $id_hist);
        return $stmt->execute();
    }

    public function actualizarSupervisores($id_hist, $supervisores) {
        // Transacción: borrar todos los supervisores viejos e insertar los nuevos.
        // Sin transacción, si falla el INSERT quedan sin supervisores.
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM supervisor_historial WHERE id_hist = ?"
            );
            $stmt->bind_param("i", $id_hist);
            $stmt->execute();

            foreach ($supervisores as $legajo_supervisor) {
                $stmt2 = $this->conexion->prepare(
                    "INSERT INTO supervisor_historial (id_hist, legajo_supervisor) VALUES (?, ?)"
                );
                $stmt2->bind_param("ii", $id_hist, $legajo_supervisor);
                $stmt2->execute();
            }

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    public function cerrarPuesto($id_hist, $fecha_fin) {
        $stmt = $this->conexion->prepare(
            "UPDATE historial_cargo SET fecha_fin = ? WHERE id_hist = ?"
        );
        $stmt->bind_param("si", $fecha_fin, $id_hist);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare(
                "DELETE FROM supervisor_historial WHERE id_hist = ?"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $stmt2 = $this->conexion->prepare(
                "DELETE FROM historial_cargo WHERE id_hist = ?"
            );
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    public function obtenerEmpleados() {
        return $this->conexion->query(
            "SELECT legajo, CONCAT(nombre, ' ', apellido) AS nombre_completo
             FROM empleado ORDER BY apellido"
        );
    }

    public function obtenerSupervisoresUsuarios() {
        return $this->conexion->query(
            "SELECT u.legajo,
                    COALESCE(CONCAT(e.nombre, ' ', e.apellido), u.nombre_usuario) AS nombre_completo,
                    r.nombre_rol
             FROM usuario u
             JOIN rol r ON u.id_rol = r.id_rol
             LEFT JOIN empleado e ON e.legajo = u.legajo
             WHERE r.nombre_rol IN ('SUPERVISOR', 'ADMINISTRADOR')
               AND u.estado = 1
             ORDER BY r.nombre_rol DESC, nombre_completo"
        );
    }

    public function actualizarFechaFin($id_hist, $fecha_fin) {
        $stmt = $this->conexion->prepare(
            "UPDATE historial_cargo SET fecha_fin = ? WHERE id_hist = ?"
        );
        $fecha_fin_val = ($fecha_fin === "" || $fecha_fin === null) ? null : $fecha_fin;
        $stmt->bind_param("si", $fecha_fin_val, $id_hist);
        return $stmt->execute();
    }

    public function obtenerCargos() {
        return $this->conexion->query(
            "SELECT cod_cargo, nombre_cargo FROM cargo ORDER BY nombre_cargo"
        );
    }

    public function obtenerDepartamentos() {
        return $this->conexion->query(
            "SELECT d.cod_depto,
                    CONCAT(d.nombre_depto,
                           IFNULL(CONCAT(' — ', l.nombre_ciudad, ', ', p.nombre_provincia), '')) AS nombre_depto
             FROM departamento d
             LEFT JOIN departamento_localidad dl ON d.cod_depto = dl.cod_depto AND dl.es_sede_principal = 1
             LEFT JOIN localidad l ON dl.cod_postal = l.cod_postal
             LEFT JOIN provincia p ON l.cod_provincia = p.cod_provincia
             ORDER BY d.nombre_depto"
        );
    }

    public function listarPorSupervisor($legajo_supervisor) {
        $sql = "SELECT h.id_hist,
                       CONCAT(e.nombre, ' ', e.apellido) AS empleado,
                       c.nombre_cargo AS cargo,
                       d.nombre_depto AS departamento,
                       h.fecha_inicio, h.fecha_fin,
                       GROUP_CONCAT(CONCAT(s.nombre, ' ', s.apellido) SEPARATOR ', ') AS supervisores
                FROM historial_cargo h
                JOIN empleado e ON h.legajo = e.legajo
                JOIN cargo c ON h.cod_cargo = c.cod_cargo
                JOIN departamento d ON h.cod_depto = d.cod_depto
                JOIN supervisor_historial sh ON h.id_hist = sh.id_hist AND sh.legajo_supervisor = ?
                LEFT JOIN supervisor_historial sh2 ON h.id_hist = sh2.id_hist
                LEFT JOIN empleado s ON sh2.legajo_supervisor = s.legajo
                GROUP BY h.id_hist, e.nombre, e.apellido, c.nombre_cargo, 
                         d.nombre_depto, h.fecha_inicio, h.fecha_fin
                ORDER BY h.fecha_inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $legajo_supervisor);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>