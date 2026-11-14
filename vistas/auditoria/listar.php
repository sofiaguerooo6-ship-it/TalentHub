<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$conexion = Conexion::conectar();

// Filtro por solicitud
$filtro_nro = isset($_GET["nro"]) ? (int)$_GET["nro"] : 0;

// LEFT JOIN en estado_solicitud ea: cod_estado_anterior puede ser NULL (registro de creación)
// LEFT JOIN en empleado e_resp: legajo_responsable puede ser NULL (admin no tiene legajo)
// COALESCE: si e_resp es NULL (admin), muestra 'Administrador' en lugar del nombre
if ($filtro_nro) {
    $stmt = $conexion->prepare(
        "SELECT a.id_auditoria, a.nro_solicitud,
                CONCAT(e_sol.nombre, ' ', e_sol.apellido) AS empleado_solicitud,
                ea.nombre_estado AS estado_anterior,
                en.nombre_estado AS estado_nuevo,
                COALESCE(CONCAT(e_resp.nombre, ' ', e_resp.apellido), 'Administrador') AS responsable,
                a.fecha_cambio, a.observacion
         FROM auditoria_estado a
         JOIN solicitud_licencia sl  ON a.nro_solicitud = sl.nro_solicitud
         JOIN empleado e_sol         ON sl.legajo = e_sol.legajo
         LEFT JOIN estado_solicitud ea ON a.cod_estado_anterior = ea.cod_estado
         JOIN estado_solicitud en    ON a.cod_estado_nuevo = en.cod_estado
         LEFT JOIN empleado e_resp   ON a.legajo_responsable = e_resp.legajo
         WHERE a.nro_solicitud = ?
         ORDER BY a.fecha_cambio DESC"
    );
    $stmt->bind_param("i", $filtro_nro);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conexion->query(
        "SELECT a.id_auditoria, a.nro_solicitud,
                CONCAT(e_sol.nombre, ' ', e_sol.apellido) AS empleado_solicitud,
                ea.nombre_estado AS estado_anterior,
                en.nombre_estado AS estado_nuevo,
                COALESCE(CONCAT(e_resp.nombre, ' ', e_resp.apellido), 'Administrador') AS responsable,
                a.fecha_cambio, a.observacion
         FROM auditoria_estado a
         JOIN solicitud_licencia sl  ON a.nro_solicitud = sl.nro_solicitud
         JOIN empleado e_sol         ON sl.legajo = e_sol.legajo
         LEFT JOIN estado_solicitud ea ON a.cod_estado_anterior = ea.cod_estado
         JOIN estado_solicitud en    ON a.cod_estado_nuevo = en.cod_estado
         LEFT JOIN empleado e_resp   ON a.legajo_responsable = e_resp.legajo
         ORDER BY a.fecha_cambio DESC"
    );
}

$registros = $res->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-header">
    <h2><i class="bi bi-journal-text me-2"></i>Auditoría de estados</h2>
</div>

<?php if ($filtro_nro): ?>
<div class="d-flex align-items-center gap-3 mb-3"
     style="background:white;border-radius:10px;padding:10px 16px;box-shadow:0 2px 8px rgba(130,140,106,0.10);">
    <i class="bi bi-funnel-fill" style="color:var(--coral);"></i>
    <span style="font-size:0.87rem;color:var(--verde-dark);font-weight:600;">
        Mostrando cambios de la solicitud <strong>#<?php echo $filtro_nro; ?></strong>
    </span>
    <a href="listar.php" class="btn btn-secondary btn-sm ms-auto">
        <i class="bi bi-x"></i> Ver todos
    </a>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Solicitud</th>
                    <th>Empleado</th>
                    <th>Estado anterior</th>
                    <th>Estado nuevo</th>
                    <th>Responsable</th>
                    <th>Fecha y hora</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($registros)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No hay registros de auditoría<?php echo $filtro_nro ? " para esta solicitud" : ""; ?>.
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($registros as $r): ?>
                <tr>
                    <td style="font-size:0.82rem;color:#aaa;"><?php echo $r["id_auditoria"]; ?></td>
                    <td>
                        <a href="../licencias/ver.php?nro=<?php echo $r["nro_solicitud"]; ?>"
                           style="color:var(--verde-dark);font-weight:600;">
                            #<?php echo $r["nro_solicitud"]; ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($r["empleado_solicitud"]); ?></td>
                    <td>
                        <?php if ($r["estado_anterior"]): ?>
                            <span style="background:#f0ede8;color:#7a6a5a;border-radius:20px;padding:3px 10px;font-size:0.78rem;font-weight:600;">
                                <?php echo htmlspecialchars($r["estado_anterior"]); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:0.8rem;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $e = $r["estado_nuevo"];
                        $style = match($e) {
                            "PENDIENTE"  => "background:#F2D48A;color:#7A5000;",
                            "APROBADA"   => "background:#A8D4AE;color:#2A5C35;",
                            "RECHAZADA"  => "background:#E4A0A0;color:#6B1A1A;",
                            default      => "background:#C8C8C8;color:#444;",
                        };
                        ?>
                        <span style="<?php echo $style; ?>border-radius:20px;padding:3px 10px;font-size:0.78rem;font-weight:600;">
                            <?php echo htmlspecialchars($e); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($r["responsable"]); ?></td>
                    <td style="font-size:0.83rem;white-space:nowrap;">
                        <?php echo date("d/m/Y H:i", strtotime($r["fecha_cambio"])); ?>
                    </td>
                    <td style="font-size:0.83rem;max-width:200px;">
                        <?php echo $r["observacion"] ? htmlspecialchars($r["observacion"]) : '<span class="text-muted">—</span>'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
