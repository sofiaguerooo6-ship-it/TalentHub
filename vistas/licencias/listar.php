<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
require_once '../../controladores/Solicitud_licencia_controlador.php';
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$controlador = new Solicitud_licencia_controlador();
$conexion    = Conexion::conectar();

// Filtro por estado desde URL
$filtro = $_GET["estado"] ?? "";
$estados_validos = ["PENDIENTE", "APROBADA", "RECHAZADA", "CANCELADA"];
if (!in_array($filtro, $estados_validos)) $filtro = "";

$modo_personal = false;

if ($_SESSION["rol"] == "EMPLEADO") {
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row    = $stmt->get_result()->fetch_assoc();
    $legajo = $row["legajo"];
    $res    = $controlador->listarPorLegajo($legajo);

    $stmt2 = $conexion->prepare(
        "SELECT COUNT(*) AS total,
                SUM(CASE WHEN es.nombre_estado='PENDIENTE'  THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN es.nombre_estado='APROBADA'   THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN es.nombre_estado='RECHAZADA'  THEN 1 ELSE 0 END) AS rechazadas
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
         WHERE sl.legajo = ?"
    );
    $stmt2->bind_param("i", $legajo);
    $stmt2->execute();
    $stats = $stmt2->get_result()->fetch_assoc();

} elseif ($_SESSION["rol"] == "SUPERVISOR") {
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row               = $stmt->get_result()->fetch_assoc();
    $legajo_supervisor = $row["legajo"];

    $modo_personal = isset($_GET["personal"]) && $_GET["personal"] == "1";

    if ($modo_personal) {
        // Vista personal: sus propias licencias como empleado
        $res = $controlador->listarPorLegajo($legajo_supervisor);
        $stmt2 = $conexion->prepare(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN es.nombre_estado='PENDIENTE'  THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN es.nombre_estado='APROBADA'   THEN 1 ELSE 0 END) AS aprobadas,
                    SUM(CASE WHEN es.nombre_estado='RECHAZADA'  THEN 1 ELSE 0 END) AS rechazadas
             FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             WHERE sl.legajo = ?"
        );
        $stmt2->bind_param("i", $legajo_supervisor);
        $stmt2->execute();
        $stats = $stmt2->get_result()->fetch_assoc();
    } else {
        // Vista equipo: licencias de sus empleados
        $res = $controlador->listarPorSupervisor($legajo_supervisor);
        $stmt2 = $conexion->prepare(
            "SELECT COUNT(*) AS total,
                    SUM(CASE WHEN es.nombre_estado='PENDIENTE'  THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN es.nombre_estado='APROBADA'   THEN 1 ELSE 0 END) AS aprobadas,
                    SUM(CASE WHEN es.nombre_estado='RECHAZADA'  THEN 1 ELSE 0 END) AS rechazadas
             FROM solicitud_licencia sl
             JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado
             JOIN historial_cargo h   ON sl.legajo = h.legajo AND h.fecha_fin IS NULL
             JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
             JOIN usuario u           ON sh.legajo_supervisor = u.legajo
             WHERE u.id_usuario = ?"
        );
        $stmt2->bind_param("i", $_SESSION["id_usuario"]);
        $stmt2->execute();
        $stats = $stmt2->get_result()->fetch_assoc();
    }

} else {
    verificarRol(['ADMINISTRADOR']);
    $res = $controlador->listar();

    $stats = $conexion->query(
        "SELECT COUNT(*) AS total,
                SUM(CASE WHEN es.nombre_estado='PENDIENTE'  THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN es.nombre_estado='APROBADA'   THEN 1 ELSE 0 END) AS aprobadas,
                SUM(CASE WHEN es.nombre_estado='RECHAZADA'  THEN 1 ELSE 0 END) AS rechazadas
         FROM solicitud_licencia sl
         JOIN estado_solicitud es ON sl.cod_estado = es.cod_estado"
    )->fetch_assoc();
}

// Cargar todas las filas y aplicar filtro en PHP
$todas_licencias = [];
while ($fila = $res->fetch_assoc()) {
    $todas_licencias[] = $fila;
}

$licencias = ($filtro != "")
    ? array_values(array_filter($todas_licencias, fn($r) => $r["estado"] == $filtro))
    : $todas_licencias;

// URLs base que preservan ?personal=1 si corresponde
$url_base = "listar.php" . ($modo_personal ? "?personal=1" : "");
$sep      = $modo_personal ? "&" : "?";
?>

<style>
    .lic-card {
        background: white;
        border-radius: 18px;
        padding: 22px 20px;
        box-shadow: 0 3px 16px rgba(130,140,106,0.10);
        transition: transform 0.22s, box-shadow 0.22s;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: block;
        border: 2px solid transparent;
    }
    .lic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 28px rgba(130,140,106,0.18);
    }
    .lic-card.activo {
        border-color: currentColor;
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
    }
    .lic-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 5px; height: 100%;
        border-radius: 18px 0 0 18px;
    }
    .lic-card.c-verde::before { background: var(--verde); }
    .lic-card.c-pend::before  { background: #C4933F; }
    .lic-card.c-apro::before  { background: #5E8A6A; }
    .lic-card.c-rech::before  { background: #B85C5C; }

    .lic-num {
        font-size: 2.4rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 5px;
    }
    .lic-label {
        font-size: 0.70rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #a09888;
        font-weight: 700;
    }
    .lic-hint {
        font-size: 0.73rem;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
        color: #a09888;
    }
    .lic-card.activo .lic-hint { font-weight: 700; }
    .lic-bg-icon {
        position: absolute;
        right: 14px; bottom: 8px;
        font-size: 3rem;
        opacity: 0.06;
        color: #2a2a1e;
        line-height: 1;
    }

    /* BADGES DE ESTADO — semáforo pastel */
    .badge-pen { background:#F2D48A; color:#7A5000; font-size:0.75rem; padding:4px 10px; border-radius:20px; font-weight:600; }
    .badge-apr { background:#A8D4AE; color:#2A5C35; font-size:0.75rem; padding:4px 10px; border-radius:20px; font-weight:600; }
    .badge-rec { background:#E4A0A0; color:#6B1A1A; font-size:0.75rem; padding:4px 10px; border-radius:20px; font-weight:600; }
    .badge-can { background:#C8C8C8; color:#444;    font-size:0.75rem; padding:4px 10px; border-radius:20px; font-weight:600; }

    /* FRANJA FILTRO ACTIVO */
    .filtro-activo-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        background: white;
        border-radius: 12px;
        padding: 10px 16px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(130,140,106,0.10);
        font-size: 0.87rem;
        color: var(--verde-dark);
        font-weight: 600;
    }
</style>

<div class="page-header">
    <h2>
        <?php
        if ($modo_personal) echo "Mis Licencias";
        elseif ($_SESSION["rol"] == "SUPERVISOR") echo "Licencias del equipo";
        else echo "Solicitudes de Licencia";
        ?>
    </h2>
    <?php if ($_SESSION["rol"] == "EMPLEADO" || $modo_personal) { ?>
        <a href="crear.php<?php echo $modo_personal ? '?personal=1' : ''; ?>" class="btn btn-primary">+ Nueva solicitud</a>
    <?php } ?>
</div>

<!-- TARJETAS — actúan como filtros -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a class="lic-card c-verde <?php echo $filtro == '' ? 'activo' : ''; ?>"
           href="<?php echo $url_base; ?>" style="color:var(--verde);">
            <div class="lic-num"><?php echo $stats["total"] ?? 0; ?></div>
            <div class="lic-label">Total solicitudes</div>
            <div class="lic-hint">
                <i class="bi bi-<?php echo $filtro == '' ? 'check2-circle' : 'funnel'; ?>"></i>
                <?php echo $filtro == '' ? 'Mostrando todas' : 'Ver todas'; ?>
            </div>
            <div class="lic-bg-icon"><i class="bi bi-file-text"></i></div>
        </a>
    </div>
    <div class="col-md-3">
        <a class="lic-card c-pend <?php echo $filtro == 'PENDIENTE' ? 'activo' : ''; ?>"
           href="<?php echo $url_base . $sep; ?>estado=PENDIENTE" style="color:#C4933F;">
            <div class="lic-num"><?php echo $stats["pendientes"] ?? 0; ?></div>
            <div class="lic-label">Pendientes</div>
            <div class="lic-hint">
                <i class="bi bi-<?php echo $filtro == 'PENDIENTE' ? 'check2-circle' : 'funnel'; ?>"></i>
                <?php echo $filtro == 'PENDIENTE' ? 'Filtrando' : 'Filtrar pendientes'; ?>
            </div>
            <div class="lic-bg-icon"><i class="bi bi-hourglass-split"></i></div>
        </a>
    </div>
    <div class="col-md-3">
        <a class="lic-card c-apro <?php echo $filtro == 'APROBADA' ? 'activo' : ''; ?>"
           href="<?php echo $url_base . $sep; ?>estado=APROBADA" style="color:#5E8A6A;">
            <div class="lic-num"><?php echo $stats["aprobadas"] ?? 0; ?></div>
            <div class="lic-label">Aprobadas</div>
            <div class="lic-hint">
                <i class="bi bi-<?php echo $filtro == 'APROBADA' ? 'check2-circle' : 'funnel'; ?>"></i>
                <?php echo $filtro == 'APROBADA' ? 'Filtrando' : 'Filtrar aprobadas'; ?>
            </div>
            <div class="lic-bg-icon"><i class="bi bi-check-circle"></i></div>
        </a>
    </div>
    <div class="col-md-3">
        <a class="lic-card c-rech <?php echo $filtro == 'RECHAZADA' ? 'activo' : ''; ?>"
           href="<?php echo $url_base . $sep; ?>estado=RECHAZADA" style="color:#B85C5C;">
            <div class="lic-num"><?php echo $stats["rechazadas"] ?? 0; ?></div>
            <div class="lic-label">Rechazadas</div>
            <div class="lic-hint">
                <i class="bi bi-<?php echo $filtro == 'RECHAZADA' ? 'check2-circle' : 'funnel'; ?>"></i>
                <?php echo $filtro == 'RECHAZADA' ? 'Filtrando' : 'Filtrar rechazadas'; ?>
            </div>
            <div class="lic-bg-icon"><i class="bi bi-x-circle"></i></div>
        </a>
    </div>
</div>

<!-- FRANJA DE FILTRO ACTIVO -->
<?php if ($filtro != "") { ?>
<div class="filtro-activo-bar">
    <i class="bi bi-funnel-fill" style="color:var(--coral);"></i>
    Mostrando solo solicitudes <strong><?php echo $filtro; ?></strong>
    (<?php echo count($licencias); ?> resultado<?php echo count($licencias) != 1 ? 's' : ''; ?>)
    <a href="<?php echo $url_base; ?>" class="btn btn-secondary btn-sm ms-auto">
        <i class="bi bi-x"></i> Limpiar filtro
    </a>
</div>
<?php } ?>

<!-- TABLA -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nro</th>
                    <th>Empleado</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Solicitud</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Días</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($licencias)) { ?>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <?php echo $filtro != "" ? "No hay solicitudes con estado $filtro." : "No hay solicitudes registradas."; ?>
                    </td>
                </tr>
                <?php } ?>
                <?php foreach ($licencias as $fila) { ?>
                <tr>
                    <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);">#<?php echo $fila["nro_solicitud"]; ?></span></td>
                    <td><strong><?php echo htmlspecialchars($fila["empleado"]); ?></strong></td>
                    <td><?php echo htmlspecialchars($fila["tipo_licencia"]); ?></td>
                    <td>
                        <?php
                        $e = $fila["estado"];
                        if ($e == "PENDIENTE")     echo '<span class="badge-pen">PENDIENTE</span>';
                        elseif ($e == "APROBADA")  echo '<span class="badge-apr">APROBADA</span>';
                        elseif ($e == "RECHAZADA") echo '<span class="badge-rec">RECHAZADA</span>';
                        else                        echo '<span class="badge-can">' . htmlspecialchars($e) . '</span>';
                        ?>
                    </td>
                    <td><?php echo $fila["fecha_solicitud"]; ?></td>
                    <td><?php echo $fila["fecha_inicio"]; ?></td>
                    <td><?php echo $fila["fecha_fin"]; ?></td>
                    <td><?php echo $fila["dias"]; ?></td>
                    <td>
                        <a href="ver.php?nro=<?php echo $fila["nro_solicitud"]; ?>" class="btn btn-sm btn-info">Ver</a>
                        <?php if ($_SESSION["rol"] == "ADMINISTRADOR" || ($_SESSION["rol"] == "SUPERVISOR" && !$modo_personal)) { ?>
                            <a href="cambiar_estado.php?nro=<?php echo $fila["nro_solicitud"]; ?>" class="btn btn-sm btn-warning">Estado</a>
                        <?php } ?>
                        <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                            <a href="eliminar.php?nro=<?php echo $fila["nro_solicitud"]; ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Eliminar esta solicitud?')">Eliminar</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
