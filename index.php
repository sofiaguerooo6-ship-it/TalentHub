<?php
require_once 'auth/verificar_sesion.php';
require_once 'config/Conexion.php';
require_once 'includes/header.php';

$conexion = Conexion::conectar();

/* ──────────────────────────────────────────
   DATOS DEL DASHBOARD
────────────────────────────────────────── */
if ($_SESSION["rol"] == "SUPERVISOR") {

    $stmt = $conexion->prepare(
        "SELECT
            (SELECT COUNT(DISTINCT h.legajo)
             FROM historial_cargo h
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ? AND h.fecha_fin IS NULL) AS total_empleados,

            (SELECT COUNT(DISTINCT h.legajo)
             FROM historial_cargo h
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ? AND h.fecha_fin IS NULL
             AND h.legajo NOT IN (
                 SELECT sl.legajo FROM solicitud_licencia sl
                 JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
                 WHERE es.nombre_estado = 'APROBADA'
                 AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin
             )) AS empleados_presentes,

            (SELECT COUNT(DISTINCT sl.legajo)
             FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             JOIN historial_cargo h ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ? AND es.nombre_estado = 'APROBADA'
             AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin) AS empleados_licencia,

            (SELECT COUNT(*)
             FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             JOIN historial_cargo h ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ? AND es.nombre_estado = 'PENDIENTE') AS licencias_pendientes,

            (SELECT COUNT(*)
             FROM evaluacion_desempeno ed
             JOIN historial_cargo h ON ed.legajo_evaluado = h.legajo AND h.fecha_fin IS NULL
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ?) AS total_evaluaciones"
    );
    $stmt->bind_param("iiiii",
        $_SESSION["id_usuario"], $_SESSION["id_usuario"],
        $_SESSION["id_usuario"], $_SESSION["id_usuario"],
        $_SESSION["id_usuario"]
    );
    $stmt->execute();
    $dashboard = $stmt->get_result()->fetch_assoc();

    // Obtener legajo del supervisor para queries de detalle
    $stmtLeg = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmtLeg->bind_param("i", $_SESSION["id_usuario"]);
    $stmtLeg->execute();
    $rowLeg = $stmtLeg->get_result()->fetch_assoc();
    $legajo_sup = $rowLeg["legajo"] ?? 0;

    // MODAL: Empleados del equipo
    $stmtEq = $conexion->prepare(
        "SELECT DISTINCT e.legajo, e.nombre, e.apellido, c.nombre_cargo, d.nombre_depto
         FROM empleado e
         JOIN historial_cargo h ON e.legajo = h.legajo AND h.fecha_fin IS NULL
         JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
         JOIN cargo c ON h.cod_cargo = c.cod_cargo
         JOIN departamento d ON h.cod_depto = d.cod_depto
         WHERE sh.legajo_supervisor = ?
         ORDER BY e.apellido"
    );
    $stmtEq->bind_param("i", $legajo_sup);
    $stmtEq->execute();
    $modal_equipo = $stmtEq->get_result()->fetch_all(MYSQLI_ASSOC);

    // MODAL: Licencias hoy (equipo)
    $stmtLicHoy = $conexion->prepare(
        "SELECT e.nombre, e.apellido, tl.nombre AS tipo, sl.fecha_inicio, sl.fecha_fin
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         JOIN empleado e ON sl.legajo = e.legajo
         JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
         JOIN historial_cargo h ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
         JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
         WHERE sh.legajo_supervisor = ?
         AND es.nombre_estado = 'APROBADA'
         AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin"
    );
    $stmtLicHoy->bind_param("i", $legajo_sup);
    $stmtLicHoy->execute();
    $modal_licencias_hoy = $stmtLicHoy->get_result()->fetch_all(MYSQLI_ASSOC);

    // MODAL: Solicitudes pendientes (equipo)
    $stmtPend = $conexion->prepare(
        "SELECT e.nombre, e.apellido, tl.nombre AS tipo, sl.fecha_inicio, sl.fecha_fin, sl.fecha_solicitud
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         JOIN empleado e ON sl.legajo = e.legajo
         JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
         JOIN historial_cargo h ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
         JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
         WHERE sh.legajo_supervisor = ?
         AND es.nombre_estado = 'PENDIENTE'
         ORDER BY sl.fecha_solicitud DESC"
    );
    $stmtPend->bind_param("i", $legajo_sup);
    $stmtPend->execute();
    $modal_pendientes = $stmtPend->get_result()->fetch_all(MYSQLI_ASSOC);

    // MODAL: Evaluaciones (equipo)
    $stmtEval = $conexion->prepare(
        "SELECT CONCAT(ev.nombre,' ',ev.apellido) AS evaluado,
                CONCAT(er.nombre,' ',er.apellido) AS evaluador,
                ed.periodo, ed.puntuacion, ed.comentario
         FROM evaluacion_desempeno ed
         JOIN empleado ev ON ed.legajo_evaluado = ev.legajo
         JOIN empleado er ON ed.legajo_evaluador = er.legajo
         JOIN historial_cargo h ON ed.legajo_evaluado = h.legajo AND h.fecha_fin IS NULL
         JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
         WHERE sh.legajo_supervisor = ?
         ORDER BY ed.periodo DESC LIMIT 30"
    );
    $stmtEval->bind_param("i", $legajo_sup);
    $stmtEval->execute();
    $modal_evaluaciones = $stmtEval->get_result()->fetch_all(MYSQLI_ASSOC);

} elseif ($_SESSION["rol"] == "ADMINISTRADOR") {

    $dashboard = $conexion->query(
        "SELECT
            (SELECT COUNT(*) FROM empleado) AS total_empleados,
            (SELECT COUNT(*) FROM empleado e
             WHERE e.legajo NOT IN (
                 SELECT sl.legajo FROM solicitud_licencia sl
                 JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
                 WHERE es.nombre_estado = 'APROBADA'
                 AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin
             )) AS empleados_presentes,
            (SELECT COUNT(DISTINCT sl.legajo) FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             WHERE es.nombre_estado = 'APROBADA'
             AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin) AS empleados_licencia,
            (SELECT COUNT(*) FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             WHERE es.nombre_estado = 'PENDIENTE') AS licencias_pendientes,
            (SELECT COUNT(*) FROM evaluacion_desempeno) AS total_evaluaciones,
            (SELECT COUNT(*) FROM departamento) AS total_departamentos"
    )->fetch_assoc();

    // MODAL: Todos los empleados (con cargo actual)
    $modal_equipo = $conexion->query(
        "SELECT e.legajo, e.nombre, e.apellido, c.nombre_cargo, d.nombre_depto
         FROM empleado e
         LEFT JOIN historial_cargo h ON e.legajo = h.legajo AND h.fecha_fin IS NULL
         LEFT JOIN cargo c ON h.cod_cargo = c.cod_cargo
         LEFT JOIN departamento d ON h.cod_depto = d.cod_depto
         ORDER BY e.apellido, e.nombre"
    )->fetch_all(MYSQLI_ASSOC);

    // MODAL: Licencias hoy
    $modal_licencias_hoy = $conexion->query(
        "SELECT e.nombre, e.apellido, tl.nombre AS tipo, sl.fecha_inicio, sl.fecha_fin
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         JOIN empleado e ON sl.legajo = e.legajo
         JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
         WHERE es.nombre_estado = 'APROBADA'
         AND CURDATE() BETWEEN sl.fecha_inicio AND sl.fecha_fin"
    )->fetch_all(MYSQLI_ASSOC);

    // MODAL: Pendientes
    $modal_pendientes = $conexion->query(
        "SELECT e.nombre, e.apellido, tl.nombre AS tipo, sl.fecha_inicio, sl.fecha_fin, sl.fecha_solicitud
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         JOIN empleado e ON sl.legajo = e.legajo
         JOIN tipo_licencia tl ON sl.cod_tipo = tl.cod_tipo
         WHERE es.nombre_estado = 'PENDIENTE'
         ORDER BY sl.fecha_solicitud DESC"
    )->fetch_all(MYSQLI_ASSOC);

    // MODAL: Evaluaciones
    $modal_evaluaciones = $conexion->query(
        "SELECT CONCAT(ev.nombre,' ',ev.apellido) AS evaluado,
                CONCAT(er.nombre,' ',er.apellido) AS evaluador,
                ed.periodo, ed.puntuacion, ed.comentario
         FROM evaluacion_desempeno ed
         JOIN empleado ev ON ed.legajo_evaluado = ev.legajo
         JOIN empleado er ON ed.legajo_evaluador = er.legajo
         ORDER BY ed.periodo DESC LIMIT 30"
    )->fetch_all(MYSQLI_ASSOC);

    // MODAL: Departamentos
    $modal_departamentos = $conexion->query(
        "SELECT d.nombre_depto,
                COUNT(DISTINCT c.cod_cargo) AS total_cargos,
                COUNT(DISTINCT h.legajo) AS total_empleados
         FROM departamento d
         LEFT JOIN historial_cargo h ON d.cod_depto = h.cod_depto AND h.fecha_fin IS NULL
         LEFT JOIN cargo c ON h.cod_cargo = c.cod_cargo
         GROUP BY d.cod_depto, d.nombre_depto
         ORDER BY d.nombre_depto"
    )->fetch_all(MYSQLI_ASSOC);
}

/* Fecha en español */
$dias   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$meses  = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$fecha  = $dias[date('w')] . ', ' . date('d') . ' de ' . $meses[date('n')-1] . ' de ' . date('Y');
?>

<!-- ══════════════════════════════════════════════
     DASHBOARD ADMINISTRADOR / SUPERVISOR
══════════════════════════════════════════════ -->
<?php if ($_SESSION["rol"] == "ADMINISTRADOR" || $_SESSION["rol"] == "SUPERVISOR") { ?>

<style>
    /* STRIP BIENVENIDA */
    .dash-welcome {
        background: linear-gradient(135deg, var(--verde-dark) 0%, var(--verde) 55%, var(--sage) 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .dash-welcome::after {
        content: '';
        position: absolute;
        width: 260px; height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        right: -60px; top: -80px;
    }
    .dash-welcome h3 { font-size: 1.4rem; font-weight: 800; margin: 0; }
    .dash-welcome p  { margin: 5px 0 0; opacity: 0.70; font-size: 0.84rem; }
    .dash-date { font-size: 0.80rem; opacity: 0.55; text-align: right; }

    /* SECCIÓN LABEL */
    .dash-section {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 2.5px;
        color: var(--sage);
        font-weight: 700;
        margin: 28px 0 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .dash-section::after { content: ''; flex: 1; height: 1px; background: var(--tea); }

    /* TARJETAS DE STAT */
    .stat-card {
        background: white;
        border-radius: 18px;
        padding: 22px 20px 20px;
        box-shadow: 0 3px 16px rgba(130,140,106,0.10);
        transition: transform 0.22s, box-shadow 0.22s;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: none;
        text-align: left;
        width: 100%;
        display: block;
        text-decoration: none;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(130,140,106,0.20);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 5px; height: 100%;
        border-radius: 18px 0 0 18px;
    }
    .stat-card.c-verde::before  { background: var(--verde); }
    .stat-card.c-sage::before   { background: var(--sage); }
    .stat-card.c-tea::before    { background: var(--tea); }
    .stat-card.c-coral::before  { background: var(--coral); }
    .stat-card.c-mix::before    { background: linear-gradient(180deg, var(--verde), var(--coral)); }

    .stat-icon-bg {
        position: absolute;
        right: 14px; bottom: 10px;
        font-size: 3.2rem;
        opacity: 0.06;
        color: #3d3d2e;
        line-height: 1;
    }

    .stat-num {
        font-size: 2.6rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 6px;
    }
    .stat-label {
        font-size: 0.70rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #a09888;
        font-weight: 700;
    }
    .stat-hint {
        font-size: 0.74rem;
        color: var(--sage);
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* COLOR DE NÚMEROS */
    .n-verde  { color: var(--verde); }
    .n-sage   { color: var(--sage); }
    .n-tea    { color: #c8906e; }
    .n-coral  { color: var(--coral); }
    .n-dark   { color: var(--verde-dark); }

    /* MODALES */
    .modal-header {
        background: var(--verde);
        color: white;
        border-bottom: none;
        border-radius: 14px 14px 0 0;
    }
    .modal-header .btn-close { filter: invert(1) brightness(2); }
    .modal-content { border-radius: 14px; border: none; }
    .modal-table th {
        background: var(--lavender);
        color: var(--verde-dark);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border-color: var(--tea);
    }
    .modal-table td {
        padding: 9px 14px;
        font-size: 0.87rem;
        border-color: #f0e8e8;
        vertical-align: middle;
    }
    .modal-table tr:hover td { background: #fdf8f8; }

    .badge-pend  { background: var(--tea);   color: #7a4030; }
    .badge-apro  { background: var(--sage);  color: white; }
    .badge-rech  { background: var(--coral); color: white; }

    /* PUNTAJE ESTRELLAS */
    .stars { color: var(--tea); font-size: 0.85rem; }

    /* TARJETAS PERSONALES (supervisor) */
    .emp-card {
        background: white; border-radius: 18px; padding: 28px 26px;
        box-shadow: 0 3px 16px rgba(130,140,106,0.10); height: 100%;
        transition: transform 0.22s, box-shadow 0.22s; position: relative; overflow: hidden;
    }
    .emp-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(130,140,106,0.18); }
    .emp-card-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin-bottom: 16px;
    }
    .emp-card-icon.ic-verde { background: #eaf0e0; color: var(--verde); }
    .emp-card-icon.ic-tea   { background: #fdf0ec; color: #c8906e; }
    .emp-card-icon.ic-coral { background: #fcecea; color: var(--coral); }
    .emp-card h5 { font-weight: 700; color: var(--text-dark); margin-bottom: 8px; font-size: 1.05rem; }
    .emp-card p  { color: #a09888; font-size: 0.85rem; margin-bottom: 18px; line-height: 1.55; }
    .emp-card .card-bg-icon {
        position: absolute; right: 14px; bottom: 10px;
        font-size: 4rem; opacity: 0.04; color: #2a2a1e;
    }
</style>

<!-- Bienvenida -->
<div class="dash-welcome">
    <div>
        <h3>Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h3>
        <p>
            <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                Resumen general del sistema de recursos humanos
            <?php } else { ?>
                Resumen de tu equipo — <?php echo $fecha; ?>
            <?php } ?>
        </p>
    </div>
    <div class="dash-date"><?php echo $fecha; ?></div>
</div>

<!-- Sección: Asistencia -->
<div class="dash-section">Asistencia hoy</div>
<div class="row g-3 mb-2">
    <div class="col-md-4">
        <a class="stat-card c-verde" data-bs-toggle="modal" data-bs-target="#modalEquipo">
            <div class="stat-num n-verde"><?php echo $dashboard["total_empleados"]; ?></div>
            <div class="stat-label"><?php echo $_SESSION["rol"]=="SUPERVISOR" ? "En mi equipo" : "Total empleados"; ?></div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Ver listado</div>
            <div class="stat-icon-bg"><i class="bi bi-people"></i></div>
        </a>
    </div>
    <div class="col-md-4">
        <a class="stat-card c-sage" data-bs-toggle="modal" data-bs-target="#modalPresentes">
            <div class="stat-num n-sage"><?php echo $dashboard["empleados_presentes"]; ?></div>
            <div class="stat-label">Presentes hoy</div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Ver detalle</div>
            <div class="stat-icon-bg"><i class="bi bi-person-check"></i></div>
        </a>
    </div>
    <div class="col-md-4">
        <a class="stat-card c-tea" data-bs-toggle="modal" data-bs-target="#modalLicenciasHoy">
            <div class="stat-num n-tea"><?php echo $dashboard["empleados_licencia"]; ?></div>
            <div class="stat-label">De licencia hoy</div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Ver quiénes</div>
            <div class="stat-icon-bg"><i class="bi bi-person-x"></i></div>
        </a>
    </div>
</div>

<!-- Sección: Gestión -->
<div class="dash-section">Solicitudes y evaluaciones</div>
<div class="row g-3">
    <div class="col-md-<?php echo isset($dashboard["total_departamentos"]) ? '4' : '6'; ?>">
        <a class="stat-card c-coral" data-bs-toggle="modal" data-bs-target="#modalPendientes">
            <div class="stat-num n-coral"><?php echo $dashboard["licencias_pendientes"]; ?></div>
            <div class="stat-label">Solicitudes pendientes</div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Revisar ahora</div>
            <div class="stat-icon-bg"><i class="bi bi-hourglass-split"></i></div>
        </a>
    </div>
    <div class="col-md-<?php echo isset($dashboard["total_departamentos"]) ? '4' : '6'; ?>">
        <a class="stat-card c-verde" data-bs-toggle="modal" data-bs-target="#modalEvaluaciones">
            <div class="stat-num n-dark"><?php echo $dashboard["total_evaluaciones"]; ?></div>
            <div class="stat-label">Evaluaciones registradas</div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Ver evaluaciones</div>
            <div class="stat-icon-bg"><i class="bi bi-bar-chart-line"></i></div>
        </a>
    </div>
    <?php if (isset($dashboard["total_departamentos"])) { ?>
    <div class="col-md-4">
        <a class="stat-card c-mix" data-bs-toggle="modal" data-bs-target="#modalDepartamentos">
            <div class="stat-num n-verde"><?php echo $dashboard["total_departamentos"]; ?></div>
            <div class="stat-label">Departamentos</div>
            <div class="stat-hint"><i class="bi bi-arrow-right-circle"></i> Ver departamentos</div>
            <div class="stat-icon-bg"><i class="bi bi-building"></i></div>
        </a>
    </div>
    <?php } ?>
</div>


<!-- ══ MODALES ══ -->

<!-- MODAL: Equipo completo -->
<div class="modal fade" id="modalEquipo" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-people me-2"></i>
                    <?php echo $_SESSION["rol"]=="SUPERVISOR" ? "Mi Equipo" : "Todos los Empleados"; ?>
                    <span class="badge bg-light text-dark ms-2" style="font-size:0.75rem;"><?php echo count($modal_equipo); ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($modal_equipo)) { ?>
                    <p class="text-center text-muted py-4">No hay empleados registrados.</p>
                <?php } else { ?>
                <table class="table modal-table mb-0">
                    <thead><tr>
                        <th>Legajo</th><th>Nombre</th><th>Cargo</th><th>Departamento</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($modal_equipo as $emp) { ?>
                        <tr>
                            <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);">#<?php echo $emp["legajo"]; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($emp["apellido"] . ', ' . $emp["nombre"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($emp["nombre_cargo"] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($emp["nombre_depto"] ?? '—'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/talenthub2/vistas/empleados/listar.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Ir a empleados
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Presentes hoy -->
<div class="modal fade" id="modalPresentes" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-check me-2"></i>Presentes hoy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php
                // Presentes = equipo - los que están de licencia hoy
                $presentes = array_filter($modal_equipo, function($emp) use ($modal_licencias_hoy) {
                    foreach ($modal_licencias_hoy as $lic) {
                        if ($lic["nombre"] == $emp["nombre"] && $lic["apellido"] == $emp["apellido"]) return false;
                    }
                    return true;
                });
                if (empty($presentes)) { ?>
                    <p class="text-center text-muted py-4">No hay datos disponibles.</p>
                <?php } else { ?>
                <table class="table modal-table mb-0">
                    <thead><tr><th>Legajo</th><th>Nombre</th><th>Cargo</th><th>Departamento</th></tr></thead>
                    <tbody>
                    <?php foreach ($presentes as $emp) { ?>
                        <tr>
                            <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);">#<?php echo $emp["legajo"]; ?></span></td>
                            <td><strong><?php echo htmlspecialchars($emp["apellido"] . ', ' . $emp["nombre"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($emp["nombre_cargo"] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($emp["nombre_depto"] ?? '—'); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Licencias hoy -->
<div class="modal fade" id="modalLicenciasHoy" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar3 me-2"></i>De licencia hoy
                    <span class="badge bg-light text-dark ms-2" style="font-size:0.75rem;"><?php echo count($modal_licencias_hoy); ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($modal_licencias_hoy)) { ?>
                    <p class="text-center text-muted py-4">Ningún empleado está de licencia hoy.</p>
                <?php } else { ?>
                <table class="table modal-table mb-0">
                    <thead><tr><th>Empleado</th><th>Tipo</th><th>Desde</th><th>Hasta</th></tr></thead>
                    <tbody>
                    <?php foreach ($modal_licencias_hoy as $lic) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($lic["apellido"] . ', ' . $lic["nombre"]); ?></strong></td>
                            <td><span class="badge badge-apro"><?php echo htmlspecialchars($lic["tipo"]); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($lic["fecha_inicio"])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($lic["fecha_fin"])); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/talenthub2/vistas/licencias/listar.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Ir a licencias
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Solicitudes pendientes -->
<div class="modal fade" id="modalPendientes" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-hourglass-split me-2"></i>Solicitudes pendientes
                    <span class="badge bg-light text-dark ms-2" style="font-size:0.75rem;"><?php echo count($modal_pendientes); ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($modal_pendientes)) { ?>
                    <p class="text-center text-muted py-4">No hay solicitudes pendientes.</p>
                <?php } else { ?>
                <table class="table modal-table mb-0">
                    <thead><tr><th>Empleado</th><th>Tipo</th><th>Desde</th><th>Hasta</th><th>Solicitado</th></tr></thead>
                    <tbody>
                    <?php foreach ($modal_pendientes as $p) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p["apellido"] . ', ' . $p["nombre"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($p["tipo"]); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p["fecha_inicio"])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p["fecha_fin"])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p["fecha_solicitud"])); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/talenthub2/vistas/licencias/listar.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Gestionar licencias
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Evaluaciones -->
<div class="modal fade" id="modalEvaluaciones" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bar-chart-line me-2"></i>Evaluaciones registradas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($modal_evaluaciones)) { ?>
                    <p class="text-center text-muted py-4">No hay evaluaciones registradas.</p>
                <?php } else { ?>
                <table class="table modal-table mb-0">
                    <thead><tr><th>Evaluado</th><th>Evaluador</th><th>Período</th><th>Puntaje</th></tr></thead>
                    <tbody>
                    <?php foreach ($modal_evaluaciones as $ev) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($ev["evaluado"]); ?></strong></td>
                            <td><?php echo htmlspecialchars($ev["evaluador"]); ?></td>
                            <td><?php echo htmlspecialchars($ev["periodo"]); ?></td>
                            <td>
                                <span class="stars">
                                <?php
                                $p = intval($ev["puntuacion"]);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= $p ? '★' : '☆';
                                }
                                ?>
                                </span>
                                <small class="text-muted ms-1"><?php echo $p; ?>/5</small>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } ?>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/talenthub2/vistas/evaluaciones/listar.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Ver todas
                </a>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Departamentos (solo ADMINISTRADOR) -->
<?php if (isset($modal_departamentos)) { ?>
<div class="modal fade" id="modalDepartamentos" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Departamentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table modal-table mb-0">
                    <thead><tr><th>Departamento</th><th>Empleados activos</th></tr></thead>
                    <tbody>
                    <?php foreach ($modal_departamentos as $dep) { ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($dep["nombre_depto"]); ?></strong></td>
                            <td>
                                <span class="badge" style="background:var(--lavender);color:var(--verde-dark);">
                                    <?php echo $dep["total_empleados"]; ?>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer border-0 pt-0">
                <a href="/talenthub2/vistas/departamentos/listar.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Ver departamentos
                </a>
            </div>
        </div>
    </div>
</div>
<?php } ?>


<!-- Sección personal del SUPERVISOR -->
<?php if ($_SESSION["rol"] == "SUPERVISOR") { ?>
<div class="dash-section" style="margin-top:32px;">Mi espacio personal</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-verde"><i class="bi bi-person-circle"></i></div>
            <h5>Mi Perfil</h5>
            <p>Revisá tu información personal, cargo actual y quién es tu supervisor.</p>
            <a href="/talenthub2/vistas/empleados/mi_perfil.php" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver mi perfil
            </a>
            <div class="card-bg-icon"><i class="bi bi-person"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-tea"><i class="bi bi-calendar3"></i></div>
            <h5>Mis Licencias</h5>
            <p>Solicitá licencias y consultá el estado de las tuyas como empleado.</p>
            <a href="/talenthub2/vistas/licencias/listar.php?personal=1" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver mis licencias
            </a>
            <div class="card-bg-icon"><i class="bi bi-calendar3"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-coral"><i class="bi bi-bar-chart"></i></div>
            <h5>Mis Evaluaciones</h5>
            <p>Consultá las evaluaciones de desempeño que recibiste como empleado.</p>
            <a href="/talenthub2/vistas/evaluaciones/listar.php?personal=1" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver mis evaluaciones
            </a>
            <div class="card-bg-icon"><i class="bi bi-bar-chart"></i></div>
        </div>
    </div>
</div>
<?php } ?>

<!-- ══════════════════════════════════════════════
     DASHBOARD EMPLEADO
══════════════════════════════════════════════ -->
<?php } else { ?>

<?php
// Cargar datos del perfil del empleado
$stmtP = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
$stmtP->bind_param("i", $_SESSION["id_usuario"]);
$stmtP->execute();
$rowP = $stmtP->get_result()->fetch_assoc();
$mi_legajo = $rowP["legajo"] ?? null;

$mi_cargo = null;
if ($mi_legajo) {
    $stmtC = $conexion->prepare(
        "SELECT c.nombre_cargo, d.nombre_depto
         FROM historial_cargo h
         JOIN cargo c ON h.cod_cargo = c.cod_cargo
         JOIN departamento d ON h.cod_depto = d.cod_depto
         WHERE h.legajo = ? AND h.fecha_fin IS NULL LIMIT 1"
    );
    $stmtC->bind_param("i", $mi_legajo);
    $stmtC->execute();
    $mi_cargo = $stmtC->get_result()->fetch_assoc();
}
?>

<style>
    .emp-welcome {
        background: linear-gradient(135deg, var(--verde-dark) 0%, var(--verde) 50%, var(--sage) 100%);
        border-radius: 20px;
        padding: 32px 36px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    .emp-welcome::before {
        content: '';
        position: absolute;
        width: 300px; height: 300px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        right: -80px; bottom: -100px;
    }
    .emp-welcome h3 { font-size: 1.6rem; font-weight: 800; margin-bottom: 5px; }
    .emp-welcome .cargo-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.15);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.82rem;
        margin-bottom: 8px;
    }
    .emp-welcome p { opacity: 0.70; font-size: 0.88rem; margin: 0; }

    .emp-card {
        background: white;
        border-radius: 18px;
        padding: 28px 26px;
        box-shadow: 0 3px 16px rgba(130,140,106,0.10);
        height: 100%;
        transition: transform 0.22s, box-shadow 0.22s;
        position: relative;
        overflow: hidden;
    }
    .emp-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(130,140,106,0.18);
    }
    .emp-card-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
    }
    .emp-card-icon.ic-verde  { background: #eaf0e0; color: var(--verde); }
    .emp-card-icon.ic-tea    { background: #fdf0ec; color: #c8906e; }
    .emp-card-icon.ic-coral  { background: #fcecea; color: var(--coral); }
    .emp-card h5 { font-weight: 700; color: var(--text-dark); margin-bottom: 8px; font-size: 1.05rem; }
    .emp-card p  { color: #a09888; font-size: 0.85rem; margin-bottom: 18px; line-height: 1.55; }
    .emp-card .card-bg-icon {
        position: absolute;
        right: 14px; bottom: 10px;
        font-size: 4rem;
        opacity: 0.04;
        color: #2a2a1e;
    }
</style>

<div class="emp-welcome">
    <h3>Hola, <?php echo htmlspecialchars($_SESSION["nombre"]); ?> <?php echo htmlspecialchars($_SESSION["apellido"] ?? ''); ?></h3>
    <?php if ($mi_cargo) { ?>
    <div class="cargo-tag">
        <i class="bi bi-briefcase"></i>
        <?php echo htmlspecialchars($mi_cargo["nombre_cargo"]); ?> &mdash; <?php echo htmlspecialchars($mi_cargo["nombre_depto"]); ?>
    </div>
    <?php } ?>
    <p><?php echo $fecha; ?></p>
</div>

<div class="dash-section">Mi espacio</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-verde"><i class="bi bi-person-circle"></i></div>
            <h5>Mi Perfil</h5>
            <p>Revisá tu información personal, cargo actual y supervisores asignados.</p>
            <a href="/talenthub2/vistas/empleados/mi_perfil.php" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver perfil
            </a>
            <div class="card-bg-icon"><i class="bi bi-person"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-tea"><i class="bi bi-calendar3"></i></div>
            <h5>Mis Licencias</h5>
            <p>Consultá el estado de tus solicitudes de licencia o pedí una nueva.</p>
            <a href="/talenthub2/vistas/licencias/listar.php" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver licencias
            </a>
            <div class="card-bg-icon"><i class="bi bi-calendar3"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="emp-card">
            <div class="emp-card-icon ic-coral"><i class="bi bi-bar-chart"></i></div>
            <h5>Mis Evaluaciones</h5>
            <p>Consultá tus evaluaciones de desempeño registradas por tus supervisores.</p>
            <a href="/talenthub2/vistas/evaluaciones/listar.php" class="btn btn-primary">
                <i class="bi bi-arrow-right"></i> Ver evaluaciones
            </a>
            <div class="card-bg-icon"><i class="bi bi-bar-chart"></i></div>
        </div>
    </div>
</div>

<?php } ?>

<style>
    .dash-section { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 2.5px;
        color: var(--sage); font-weight: 700; margin: 28px 0 14px;
        display: flex; align-items: center; gap: 10px; }
    .dash-section::after { content: ''; flex: 1; height: 1px; background: var(--tea); }
</style>

<?php require_once 'includes/footer.php'; ?>
