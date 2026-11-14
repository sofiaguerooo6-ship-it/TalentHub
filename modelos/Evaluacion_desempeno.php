<?php
class Evaluacion_desempeno {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT ed.id_evaluacion,
                       CONCAT(ev.nombre, ' ', ev.apellido) AS evaluado,
                       COALESCE(CONCAT(er.nombre, ' ', er.apellido), 'Administrador') AS evaluador,
                       ed.periodo, ed.puntuacion, ed.comentario
                FROM evaluacion_desempeno ed
                JOIN empleado ev       ON ed.legajo_evaluado  = ev.legajo
                LEFT JOIN empleado er  ON ed.legajo_evaluador = er.legajo
                ORDER BY ed.periodo DESC";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($id) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM evaluacion_desempeno WHERE id_evaluacion = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario) {
        // legajo_evaluador puede ser NULL si el evaluador es el ADMINISTRADOR.
        // Se usa variable MySQL para evitar que bind_param("i", null) convierta null→0.
        $leg_sql = ($legajo_evaluador === null) ? 'NULL' : intval($legajo_evaluador);
        $this->conexion->query("SET @legajo_eval = $leg_sql");
        $stmt = $this->conexion->prepare(
            "INSERT INTO evaluacion_desempeno
             (legajo_evaluado, legajo_evaluador, periodo, puntuacion, comentario)
             VALUES (?, @legajo_eval, ?, ?, ?)"
        );
        $stmt->bind_param("isis", $legajo_evaluado, $periodo, $puntuacion, $comentario);
        return $stmt->execute();
    }

    public function actualizar($id, $legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario) {
        // Misma técnica: variable MySQL para el legajo nullable
        $leg_sql = ($legajo_evaluador === null) ? 'NULL' : intval($legajo_evaluador);
        $this->conexion->query("SET @legajo_eval = $leg_sql");
        $stmt = $this->conexion->prepare(
            "UPDATE evaluacion_desempeno
             SET legajo_evaluado=?, legajo_evaluador=@legajo_eval, periodo=?, puntuacion=?, comentario=?
             WHERE id_evaluacion=?"
        );
        $stmt->bind_param("isisi", $legajo_evaluado, $periodo, $puntuacion, $comentario, $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM evaluacion_desempeno WHERE id_evaluacion = ?"
        );
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function obtenerEmpleados() {
        return $this->conexion->query(
            "SELECT legajo, CONCAT(nombre, ' ', apellido) AS nombre_completo 
             FROM empleado ORDER BY apellido"
        );
    }
    public function listarPorLegajo($legajo) {
    $sql = "SELECT ed.id_evaluacion,
                   CONCAT(ev.nombre, ' ', ev.apellido) AS evaluado,
                   COALESCE(CONCAT(er.nombre, ' ', er.apellido), 'Administrador') AS evaluador,
                   ed.periodo, ed.puntuacion, ed.comentario
            FROM evaluacion_desempeno ed
            JOIN empleado ev      ON ed.legajo_evaluado  = ev.legajo
            LEFT JOIN empleado er ON ed.legajo_evaluador = er.legajo
            WHERE ed.legajo_evaluado = ?
            ORDER BY ed.periodo DESC";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("i", $legajo);
    $stmt->execute();
    return $stmt->get_result();
}
public function listarPorSupervisor($legajo_supervisor) {
    $sql = "SELECT ed.id_evaluacion,
                   CONCAT(ev.nombre, ' ', ev.apellido) AS evaluado,
                   COALESCE(CONCAT(er.nombre, ' ', er.apellido), 'Administrador') AS evaluador,
                   ed.periodo, ed.puntuacion, ed.comentario
            FROM evaluacion_desempeno ed
            JOIN empleado ev      ON ed.legajo_evaluado  = ev.legajo
            LEFT JOIN empleado er ON ed.legajo_evaluador = er.legajo
            JOIN historial_cargo h ON ed.legajo_evaluado = h.legajo AND h.fecha_fin IS NULL
            JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
            WHERE sh.legajo_supervisor = ?
            ORDER BY ed.periodo DESC";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("i", $legajo_supervisor);
    $stmt->execute();
    return $stmt->get_result();
}
}
?>