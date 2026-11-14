<?php
class Solicitud_licencia {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT sl.nro_solicitud, 
                       CONCAT(e.nombre, ' ', e.apellido) AS empleado,
                       tl.nombre AS tipo_licencia,
                       es.nombre_estado AS estado,
                       sl.fecha_solicitud, sl.fecha_inicio, sl.fecha_fin,
                       (DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + 1) - FLOOR((DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + WEEKDAY(sl.fecha_inicio) + 1) / 7) AS dias,
                       sl.observacion
                FROM solicitud_licencia sl
                JOIN empleado e ON sl.legajo = e.legajo
                JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
                JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
                ORDER BY sl.fecha_solicitud DESC";
        return $this->conexion->query($sql);
    }

    public function buscarPorId($nro) {
        $stmt = $this->conexion->prepare(
            "SELECT * FROM solicitud_licencia WHERE nro_solicitud = ?"
        );
        $stmt->bind_param("i", $nro);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function crear($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin, $observacion) {
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare(
                "INSERT INTO solicitud_licencia
                 (legajo, cod_tipo, cod_estado, fecha_solicitud, fecha_inicio, fecha_fin, observacion)
                 VALUES (?, ?, 'PEND', ?, ?, ?, ?)"
            );
            $stmt->bind_param("isssss", $legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin, $observacion);
            $stmt->execute();
            $nro = $this->conexion->insert_id;
            $this->conexion->commit();
            return $nro;
        } catch (Exception $e) {
            $this->conexion->rollback();
            return false;
        }
    }

    public function cambiarEstado($nro_solicitud, $cod_estado_nuevo, $id_usuario, $observacion) {
        // Paso 1: buscar el legajo del responsable. El ADMIN tiene legajo=NULL en usuario.
        $stLeg = $this->conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
        $stLeg->bind_param("i", $id_usuario);
        $stLeg->execute();
        $legRow      = $stLeg->get_result()->fetch_assoc();
        $legajo_resp = $legRow["legajo"] ?? null;
        // Cerrar obligatorio: conexión singleton — "Commands out of sync" si no se cierra
        $stLeg->close();

        // Paso 2: MySQLi bind_param("i", null) convierte null→0, rompiendo la FK.
        // Solución: asignar a variable de sesión MySQL y referenciarla en el CALL.
        $leg_sql = ($legajo_resp === null) ? 'NULL' : intval($legajo_resp);
        $this->conexion->query("SET @legajo_resp = $leg_sql");

        // Paso 3: llamar al SP — hace UPDATE solicitud_licencia + INSERT auditoria_estado en transacción
        $stmt = $this->conexion->prepare(
            "CALL sp_cambiar_estado_licencia(?, ?, @legajo_resp, ?, @res)"
        );
        $stmt->bind_param("iss", $nro_solicitud, $cod_estado_nuevo, $observacion);
        $stmt->execute();
        $stmt->close();

        // Paso 4: leer el parámetro OUT del SP via variable de sesión MySQL
        $resultado = $this->conexion->query("SELECT @res AS res")->fetch_assoc()["res"];
        return $resultado === "OK";
    }

    public function eliminar($nro) {
        $stmt = $this->conexion->prepare(
            "DELETE FROM solicitud_licencia WHERE nro_solicitud = ?"
        );
        $stmt->bind_param("i", $nro);
        return $stmt->execute();
    }

    public function obtenerEmpleados() {
        return $this->conexion->query(
            "SELECT legajo, CONCAT(nombre, ' ', apellido) AS nombre_completo 
             FROM empleado ORDER BY apellido"
        );
    }

    public function obtenerTiposLicencia() {
        return $this->conexion->query(
            "SELECT cod_tipo, nombre, dias_max, requiere_certificado FROM tipo_licencia ORDER BY nombre"
        );
    }

    public function obtenerEstados() {
        return $this->conexion->query(
            "SELECT cod_estado, nombre_estado FROM estado_solicitud ORDER BY nombre_estado"
        );
    }
    public function listarPorLegajo($legajo) {
        $sql = "SELECT sl.nro_solicitud, 
                       CONCAT(e.nombre, ' ', e.apellido) AS empleado,
                       tl.nombre AS tipo_licencia,
                       es.nombre_estado AS estado,
                       sl.fecha_solicitud, sl.fecha_inicio, sl.fecha_fin,
                       (DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + 1) - FLOOR((DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + WEEKDAY(sl.fecha_inicio) + 1) / 7) AS dias,
                       sl.observacion
                FROM solicitud_licencia sl
                JOIN empleado e ON sl.legajo = e.legajo
                JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
                JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
                WHERE sl.legajo = ?
                ORDER BY sl.fecha_solicitud DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $legajo);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function listarPorSupervisor($legajo_supervisor) {
    $sql = "SELECT sl.nro_solicitud,
                   CONCAT(e.nombre, ' ', e.apellido) AS empleado,
                   tl.nombre AS tipo_licencia,
                   es.nombre_estado AS estado,
                   sl.fecha_solicitud, sl.fecha_inicio, sl.fecha_fin,
                   (DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + 1) - FLOOR((DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + WEEKDAY(sl.fecha_inicio) + 1) / 7) AS dias,
                   sl.observacion
            FROM solicitud_licencia sl
            JOIN empleado e ON sl.legajo = e.legajo
            JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
            JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
            JOIN historial_cargo h ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
            JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
            WHERE sh.legajo_supervisor = ?
            ORDER BY sl.fecha_solicitud DESC";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("i", $legajo_supervisor);
    $stmt->execute();
    return $stmt->get_result();
}
public function agregarDocumento($nro_solicitud, $cod_tipo_doc, $nombre_archivo) {
    $stmt = $this->conexion->prepare(
        "INSERT INTO documento_adjunto (nro_solicitud, cod_tipo_doc, nombre_archivo, fecha_carga)
         VALUES (?, ?, ?, CURDATE())"
    );
    $stmt->bind_param("iss", $nro_solicitud, $cod_tipo_doc, $nombre_archivo);
    return $stmt->execute();
}

public function listarDocumentos($nro_solicitud) {
    $stmt = $this->conexion->prepare(
        "SELECT da.id_doc, da.nombre_archivo, da.fecha_carga, td.nombre AS tipo
         FROM documento_adjunto da
         JOIN tipo_documento td ON da.cod_tipo_doc = td.cod_tipo_doc
         WHERE da.nro_solicitud = ?"
    );
    $stmt->bind_param("i", $nro_solicitud);
    $stmt->execute();
    return $stmt->get_result();
}

public function eliminarDocumento($id_doc) {
    $stmt = $this->conexion->prepare(
        "DELETE FROM documento_adjunto WHERE id_doc = ?"
    );
    $stmt->bind_param("i", $id_doc);
    return $stmt->execute();
}

public function obtenerTiposDocumento() {
    return $this->conexion->query(
        "SELECT cod_tipo_doc, nombre FROM tipo_documento ORDER BY nombre"
    );
}
// Días laborales (lunes a sábado) entre dos fechas, inclusive
private function contarDiasLaborales($fecha_inicio, $fecha_fin) {
    $dias = 0;
    $d = strtotime($fecha_inicio);
    $f = strtotime($fecha_fin);
    while ($d <= $f) {
        if (date('N', $d) != 7) $dias++; // N=7 es domingo
        $d += 86400;
    }
    return $dias;
}

public function obtenerDiasTotalesUsados($legajo) {
    $anio = date("Y");
    // Días laborales (sin domingos): fórmula SQL
    // total_dias - sundays, donde sundays = FLOOR((DATEDIFF+WEEKDAY(inicio)+1)/7)
    $stmt = $this->conexion->prepare(
        "SELECT COALESCE(SUM(
             (DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + 1)
             - FLOOR((DATEDIFF(sl.fecha_fin, sl.fecha_inicio) + WEEKDAY(sl.fecha_inicio) + 1) / 7)
         ), 0) AS dias_usados
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
         WHERE sl.legajo = ?
         AND es.nombre_estado = 'APROBADA'
         AND YEAR(sl.fecha_inicio) = ?
         AND (
             tl.requiere_certificado = 0
             OR NOT EXISTS (
                 SELECT 1 FROM documento_adjunto da
                 WHERE da.nro_solicitud = sl.nro_solicitud
             )
         )"
    );
    $stmt->bind_param("ii", $legajo, $anio);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado["dias_usados"];
}

public function obtenerDiasUsadosPorTipo($legajo, $cod_tipo) {
    $anio = date("Y");
    $stmt = $this->conexion->prepare(
        "SELECT COALESCE(SUM(
             (DATEDIFF(fecha_fin, fecha_inicio) + 1)
             - FLOOR((DATEDIFF(fecha_fin, fecha_inicio) + WEEKDAY(fecha_inicio) + 1) / 7)
         ), 0) AS dias_usados
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         WHERE sl.legajo = ? AND sl.cod_tipo = ?
         AND es.nombre_estado = 'APROBADA'
         AND YEAR(sl.fecha_inicio) = ?"
    );
    $stmt->bind_param("isi", $legajo, $cod_tipo, $anio);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    return $resultado["dias_usados"];
}

public function obtenerResumenDisponibilidad($legajo) {
    $TOTAL_ANUAL = 30;
    $dias_usados_total = $this->obtenerDiasTotalesUsados($legajo);
    $dias_disponibles_total = $TOTAL_ANUAL - $dias_usados_total;

    $tipos = $this->conexion->query(
        "SELECT cod_tipo, nombre, dias_max, requiere_certificado FROM tipo_licencia ORDER BY nombre"
    );
    $detalle_tipos = [];
    while ($tipo = $tipos->fetch_assoc()) {
        $usados_tipo = $this->obtenerDiasUsadosPorTipo($legajo, $tipo["cod_tipo"]);
        $detalle_tipos[] = [
            "cod_tipo" => $tipo["cod_tipo"],
            "nombre" => $tipo["nombre"],
            "dias_max" => $tipo["dias_max"],
            "dias_usados" => $usados_tipo,
            "dias_disponibles_tipo" => $tipo["dias_max"] - $usados_tipo,
            "requiere_certificado" => $tipo["requiere_certificado"]
        ];
    }

    return [
        "total_anual" => $TOTAL_ANUAL,
        "usados_total" => $dias_usados_total,
        "disponibles_total" => $dias_disponibles_total,
        "tipos" => $detalle_tipos
    ];
}

public function validarDiasDisponibles($legajo, $cod_tipo, $fecha_inicio, $fecha_fin) {
    // Verificar solapamiento con licencias existentes (no rechazadas ni canceladas)
    $stSol = $this->conexion->prepare(
        "SELECT COUNT(*) AS cnt
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         WHERE sl.legajo = ?
           AND es.nombre_estado NOT IN ('RECHAZADA','CANCELADA')
           AND sl.fecha_inicio <= ? AND sl.fecha_fin >= ?"
    );
    $stSol->bind_param("iss", $legajo, $fecha_fin, $fecha_inicio);
    $stSol->execute();
    $solape = $stSol->get_result()->fetch_assoc();
    if ($solape["cnt"] > 0) {
        return "Ya tenés una licencia aprobada o pendiente en ese período. No se pueden solapar fechas.";
    }

    $dias_solicitados = $this->contarDiasLaborales($fecha_inicio, $fecha_fin);

    // Verificar límite del tipo específico
    $stmt = $this->conexion->prepare(
        "SELECT nombre, dias_max, requiere_certificado FROM tipo_licencia WHERE cod_tipo = ?"
    );
    $stmt->bind_param("s", $cod_tipo);
    $stmt->execute();
    $tipo = $stmt->get_result()->fetch_assoc();

    $usados_tipo = $this->obtenerDiasUsadosPorTipo($legajo, $cod_tipo);
    $disponibles_tipo = max(0, $tipo["dias_max"] - $usados_tipo);

    if ($dias_solicitados > $disponibles_tipo) {
        $nombre_tipo = $tipo['nombre'] ?? $cod_tipo;
        if ($disponibles_tipo == 0) {
            return "Ya agotaste los días disponibles para \"$nombre_tipo\" este año (máximo {$tipo['dias_max']} días). Solicitaste $dias_solicitados día" . ($dias_solicitados != 1 ? 's' : '') . " laborales.";
        }
        return "Solo te quedan $disponibles_tipo día" . ($disponibles_tipo != 1 ? 's' : '') . " disponibles para \"$nombre_tipo\" este año (máximo {$tipo['dias_max']} días). Solicitaste $dias_solicitados día" . ($dias_solicitados != 1 ? 's' : '') . " laborales.";
    }

    // Verificar límite total anual.
    // Tipos con certificado: cuentan al crear (aún no hay doc adjunto),
    // pero si luego adjuntan el certificado, dejan de contar automáticamente.
    $usados_total = $this->obtenerDiasTotalesUsados($legajo);
    $disponibles_total = max(0, 30 - $usados_total);
    if ($dias_solicitados > $disponibles_total) {
        if ($tipo["requiere_certificado"] == 1) {
            if ($disponibles_total == 0) {
                return "Agotaste tu total anual de 30 días. Si adjuntás el certificado correspondiente, esta licencia no se descontará del total.";
            }
            return "Solo te quedan $disponibles_total día" . ($disponibles_total != 1 ? 's' : '') . " de tu total anual. Si adjuntás el certificado, esta licencia no se descontará del total.";
        }
        if ($disponibles_total == 0) {
            return "Agotaste tu total anual de 30 días de licencia.";
        }
        return "Solo te quedan $disponibles_total día" . ($disponibles_total != 1 ? 's' : '') . " de tu total anual de 30 días.";
    }

    return "OK";
}
}
?>