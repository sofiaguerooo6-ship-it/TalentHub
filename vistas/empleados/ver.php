<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../config/Conexion.php';
require_once '../../controladores/Empleado_controlador.php';
require_once '../../controladores/Empleado_direccion_controlador.php';
require_once '../../includes/header.php';

$controlador     = new Empleado_controlador();
$dir_controlador = new Empleado_direccion_controlador();
$conexion        = Conexion::conectar();

if (!isset($_GET["legajo"])) {
    header("Location: listar.php");
    exit;
}

$legajo    = intval($_GET["legajo"]);
$resultado = $controlador->buscar($legajo);
$datos     = $resultado->fetch_assoc();

if (!$datos) { ?>
    <?php require_once '../../includes/header.php'; ?>
    <div class="alert alert-danger">Empleado no encontrado.</div>
    <?php require_once '../../includes/footer.php'; ?>
<?php exit; }

// Dirección principal (v_empleado_detallado)
$stmtDir = $conexion->prepare("SELECT calle, altura, ciudad, provincia FROM v_empleado_detallado WHERE legajo = ?");
$stmtDir->bind_param("i", $legajo);
$stmtDir->execute();
$dir_principal = $stmtDir->get_result()->fetch_assoc();

// Puesto actual + supervisores + localidad del depto (ve_organigramaactual + join depto_localidad)
$stmtOrg = $conexion->prepare(
    "SELECT o.cargo, o.nivel, o.departamento, o.supervisores,
            l.nombre_ciudad AS depto_ciudad, p.nombre_provincia AS depto_provincia
     FROM ve_organigramaactual o
     JOIN historial_cargo h ON h.legajo = o.legajo AND h.fecha_fin IS NULL
     LEFT JOIN departamento_localidad dl ON h.cod_depto = dl.cod_depto AND dl.es_sede_principal = 1
     LEFT JOIN localidad l ON dl.cod_postal = l.cod_postal
     LEFT JOIN provincia p ON l.cod_provincia = p.cod_provincia
     WHERE o.legajo = ?"
);
$stmtOrg->bind_param("i", $legajo);
$stmtOrg->execute();
$org = $stmtOrg->get_result()->fetch_assoc();

// Rol actual del empleado
$stmtRol = $conexion->prepare(
    "SELECT r.nombre_rol FROM usuario u JOIN rol r ON u.id_rol = r.id_rol WHERE u.legajo = ? LIMIT 1"
);
$stmtRol->bind_param("i", $legajo);
$stmtRol->execute();
$rol_actual = $stmtRol->get_result()->fetch_assoc()["nombre_rol"] ?? null;

// Funciones de BD: antigüedad y promedio de evaluaciones
$stmtFn = $conexion->prepare(
    "SELECT fn_antiguedad_anios(?) AS antiguedad, fn_promedio_evaluacion(?) AS promedio_eval"
);
$stmtFn->bind_param("ii", $legajo, $legajo);
$stmtFn->execute();
$fn = $stmtFn->get_result()->fetch_assoc();

// Todas las direcciones
$direcciones = $dir_controlador->listarPorLegajo($legajo);
$dirs_array  = [];
while ($dir = $direcciones->fetch_assoc()) $dirs_array[] = $dir;

// Historial de puestos anteriores (fecha_fin IS NOT NULL).
// El rol en cada puesto se infiere desde supervisor_historial:
//   COUNT(sh.id_hist) > 0 → alguien lo supervisaba → era EMPLEADO
//   COUNT(sh.id_hist) = 0 → nadie lo supervisaba → era SUPERVISOR
$stmtHist = $conexion->prepare(
    "SELECT c.nombre_cargo AS cargo, n.nombre_nivel AS nivel,
            d.nombre_depto AS departamento,
            l.nombre_ciudad AS depto_ciudad, p.nombre_provincia AS depto_provincia,
            h.fecha_inicio, h.fecha_fin,
            GROUP_CONCAT(CONCAT(s.nombre, ' ', s.apellido) SEPARATOR ', ') AS supervisores,
            CASE WHEN COUNT(sh.id_hist) > 0 THEN 'EMPLEADO' ELSE 'SUPERVISOR' END AS rol_en_puesto
     FROM historial_cargo h
     JOIN cargo c ON h.cod_cargo = c.cod_cargo
     JOIN nivel_jerarquico n ON c.cod_nivel = n.cod_nivel
     JOIN departamento d ON h.cod_depto = d.cod_depto
     LEFT JOIN departamento_localidad dl ON h.cod_depto = dl.cod_depto AND dl.es_sede_principal = 1
     LEFT JOIN localidad l ON dl.cod_postal = l.cod_postal
     LEFT JOIN provincia p ON l.cod_provincia = p.cod_provincia
     LEFT JOIN supervisor_historial sh ON h.id_hist = sh.id_hist
     LEFT JOIN empleado s ON sh.legajo_supervisor = s.legajo
     WHERE h.legajo = ? AND h.fecha_fin IS NOT NULL
     GROUP BY h.id_hist, c.nombre_cargo, n.nombre_nivel, d.nombre_depto,
              l.nombre_ciudad, p.nombre_provincia, h.fecha_inicio, h.fecha_fin
     ORDER BY h.fecha_inicio DESC"
);
$stmtHist->bind_param("i", $legajo);
$stmtHist->execute();
$historial_anterior = $stmtHist->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .detail-label { font-weight: 600; color: #888; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .detail-value { font-size: 0.93rem; color: var(--text-dark); margin-top: 2px; }
    .detail-row   { padding: 10px 0; border-bottom: 1px solid #f4eeee; }
    .detail-row:last-child { border-bottom: none; }
    .section-title {
        color: var(--verde-dark); font-weight: 700; font-size: 0.88rem;
        text-transform: uppercase; letter-spacing: 1px;
        border-bottom: 2px solid var(--tea); padding-bottom: 10px; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .emp-hero {
        background: linear-gradient(135deg, var(--verde-dark), var(--sage));
        border-radius: 16px; padding: 24px 28px; color: white;
        display: flex; align-items: center; gap: 20px; margin-bottom: 24px;
    }
    .emp-avatar-lg {
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(255,255,255,0.2); color: white;
        font-weight: 900; font-size: 1.4rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .emp-hero h3 { font-size: 1.3rem; font-weight: 800; margin: 0 0 4px; }
    .emp-hero small { opacity: 0.7; font-size: 0.82rem; }
    .emp-hero .legajo-badge {
        background: rgba(255,255,255,0.18); border-radius: 20px;
        padding: 3px 12px; font-size: 0.78rem; font-weight: 700;
        display: inline-block; margin-top: 6px;
    }
</style>

<div class="page-header">
    <h2>Detalle del Empleado</h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

<!-- Hero -->
<div class="emp-hero">
    <div class="emp-avatar-lg">
        <?php echo strtoupper(mb_substr($datos["nombre"], 0, 1) . mb_substr($datos["apellido"], 0, 1)); ?>
    </div>
    <div>
        <h3><?php echo htmlspecialchars($datos["apellido"] . ', ' . $datos["nombre"]); ?></h3>
        <?php if ($org) { ?>
            <small><?php echo htmlspecialchars($org["cargo"]); ?> — <?php echo htmlspecialchars($org["departamento"]); ?></small>
        <?php } ?>
        <div class="legajo-badge">Legajo #<?php echo $datos["legajo"]; ?></div>
    </div>
    <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
    <div class="ms-auto">
        <a href="editar.php?legajo=<?php echo $datos["legajo"]; ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Editar
        </a>
    </div>
    <?php } ?>
</div>

<div class="row g-4">

    <!-- Datos personales -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="section-title"><i class="bi bi-person"></i> Datos personales</div>
            <div class="detail-row">
                <div class="detail-label">DNI</div>
                <div class="detail-value"><?php echo htmlspecialchars($datos["dni"]); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?php echo htmlspecialchars($datos["email"]); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Teléfono</div>
                <div class="detail-value"><?php echo htmlspecialchars($datos["telefono"] ?: '—'); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Fecha de ingreso</div>
                <div class="detail-value"><?php echo date('d/m/Y', strtotime($datos["fecha_ingreso"])); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Antigüedad</div>
                <div class="detail-value"><?php echo $fn["antiguedad"]; ?> año<?php echo $fn["antiguedad"] != 1 ? 's' : ''; ?></div>
            </div>
            <?php if ($fn["promedio_eval"] > 0) { ?>
            <div class="detail-row">
                <div class="detail-label">Promedio de evaluaciones</div>
                <div class="detail-value">
                    <?php
                    $prom = $fn["promedio_eval"];
                    $color = $prom >= 4 ? 'var(--verde)' : ($prom >= 3 ? '#e6a817' : 'var(--coral)');
                    ?>
                    <span style="font-weight:700;color:<?php echo $color; ?>">
                        <?php echo number_format($prom, 1); ?> / 5
                    </span>
                </div>
            </div>
            <?php } ?>
            <?php if ($dir_principal && $dir_principal["ciudad"]) { ?>
            <div class="detail-row">
                <div class="detail-label">Localidad</div>
                <div class="detail-value"><?php echo htmlspecialchars($dir_principal["ciudad"]); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Provincia</div>
                <div class="detail-value"><?php echo htmlspecialchars($dir_principal["provincia"]); ?></div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Puesto actual -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="section-title"><i class="bi bi-briefcase"></i> Puesto actual</div>
            <?php if ($org) { ?>
            <?php if ($rol_actual) { ?>
            <div class="detail-row">
                <div class="detail-label">Rol</div>
                <div class="detail-value">
                    <span class="badge" style="background:<?php echo $rol_actual === 'SUPERVISOR' ? 'var(--verde)' : 'var(--sage)'; ?>;color:white;font-size:0.78rem;">
                        <?php echo htmlspecialchars($rol_actual); ?>
                    </span>
                </div>
            </div>
            <?php } ?>
            <div class="detail-row">
                <div class="detail-label">Cargo</div>
                <div class="detail-value"><?php echo htmlspecialchars($org["cargo"]); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nivel jerárquico</div>
                <div class="detail-value"><?php echo htmlspecialchars($org["nivel"]); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Departamento</div>
                <div class="detail-value"><?php echo htmlspecialchars($org["departamento"]); ?>
                    <?php if ($org["depto_ciudad"]) { ?>
                        <div style="font-size:0.78rem;color:#a09888;margin-top:2px;">
                            <?php echo htmlspecialchars($org["depto_ciudad"] . ', ' . $org["depto_provincia"]); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Supervisor<?php echo strpos($org["supervisores"], ',') !== false ? 'es' : ''; ?></div>
                <div class="detail-value"><?php echo htmlspecialchars($org["supervisores"] ?: '—'); ?></div>
            </div>
            <?php } else { ?>
                <p class="text-muted" style="font-size:0.88rem;">Sin puesto asignado actualmente.</p>
            <?php } ?>
        </div>
    </div>

    <!-- Direcciones -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="section-title"><i class="bi bi-geo-alt"></i> Direcciones</div>
            <?php if (!empty($dirs_array)) { ?>
                <?php foreach ($dirs_array as $dir) { ?>
                <div class="detail-row">
                    <div class="detail-value">
                        <?php echo htmlspecialchars($dir["calle"] . ' ' . $dir["altura"]); ?>
                        <?php if ($dir["es_principal"]) { ?>
                            <span class="badge ms-1" style="background:var(--sage);color:white;font-size:0.68rem;">Principal</span>
                        <?php } ?>
                    </div>
                    <div class="detail-label mt-1">
                        <?php echo htmlspecialchars($dir["nombre_ciudad"] . ', ' . $dir["nombre_provincia"]); ?>
                    </div>
                </div>
                <?php } ?>
                <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                <div class="mt-3">
                    <a href="editar.php?legajo=<?php echo $datos["legajo"]; ?>" class="btn btn-sm btn-secondary">
                        <i class="bi bi-pencil"></i> Editar direcciones
                    </a>
                </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-muted" style="font-size:0.88rem;">Sin direcciones registradas.</p>
            <?php } ?>
        </div>
    </div>

</div>

<?php if (!empty($historial_anterior)) { ?>
<div class="mt-4">
    <div class="section-title" style="margin-bottom:16px;"><i class="bi bi-clock-history"></i> Historial de puestos anteriores</div>
    <div class="card p-0" style="overflow:hidden;">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>Cargo</th>
                    <th>Nivel</th>
                    <th>Departamento</th>
                    <th>Supervisor/es</th>
                    <th>Desde</th>
                    <th>Hasta</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($historial_anterior as $h) { ?>
                <tr>
                    <td>
                        <span class="badge" style="background:<?php echo $h['rol_en_puesto'] === 'SUPERVISOR' ? 'var(--verde)' : 'var(--sage)'; ?>;color:white;font-size:0.74rem;">
                            <?php echo htmlspecialchars($h["rol_en_puesto"]); ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($h["cargo"]); ?></strong></td>
                    <td><?php echo htmlspecialchars($h["nivel"]); ?></td>
                    <td>
                        <?php echo htmlspecialchars($h["departamento"]); ?>
                        <?php if ($h["depto_ciudad"]) { ?>
                            <div style="font-size:0.78rem;color:#a09888;">
                                <?php echo htmlspecialchars($h["depto_ciudad"] . ', ' . $h["depto_provincia"]); ?>
                            </div>
                        <?php } ?>
                    </td>
                    <td style="font-size:0.84rem;"><?php echo htmlspecialchars($h["supervisores"] ?: '—'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($h["fecha_inicio"])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($h["fecha_fin"])); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

<?php require_once '../../includes/footer.php'; ?>
