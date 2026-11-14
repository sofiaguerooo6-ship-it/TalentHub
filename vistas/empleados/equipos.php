<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$conexion = Conexion::conectar();
$buscar   = trim($_GET["buscar"] ?? "");

// Traer todos los supervisores activos con sus empleados actuales
$sql = "SELECT
            e_sup.legajo        AS legajo_sup,
            e_sup.nombre        AS nombre_sup,
            e_sup.apellido      AS apellido_sup,
            e_emp.legajo        AS legajo_emp,
            e_emp.nombre        AS nombre_emp,
            e_emp.apellido      AS apellido_emp,
            c.nombre_cargo,
            d.nombre_depto
        FROM usuario u
        JOIN rol r       ON u.id_rol  = r.id_rol
        JOIN empleado e_sup ON u.legajo = e_sup.legajo
        LEFT JOIN supervisor_historial sh ON sh.legajo_supervisor = e_sup.legajo
        LEFT JOIN historial_cargo h  ON sh.id_hist = h.id_hist AND h.fecha_fin IS NULL
        LEFT JOIN empleado e_emp     ON h.legajo   = e_emp.legajo
        LEFT JOIN cargo c            ON h.cod_cargo = c.cod_cargo
        LEFT JOIN departamento d     ON h.cod_depto = d.cod_depto
        WHERE r.nombre_rol = 'SUPERVISOR' AND u.estado = 1
        ORDER BY e_sup.apellido, e_sup.nombre, e_emp.apellido, e_emp.nombre";

$resultado = $conexion->query($sql);

// Agrupar por supervisor en PHP
$equipos = [];
while ($fila = $resultado->fetch_assoc()) {
    $key = $fila["legajo_sup"];
    if (!isset($equipos[$key])) {
        $equipos[$key] = [
            "legajo"   => $fila["legajo_sup"],
            "nombre"   => $fila["nombre_sup"] . " " . $fila["apellido_sup"],
            "empleados" => []
        ];
    }
    if ($fila["legajo_emp"]) {
        $equipos[$key]["empleados"][] = [
            "legajo"  => $fila["legajo_emp"],
            "nombre"  => $fila["nombre_emp"] . " " . $fila["apellido_emp"],
            "cargo"   => $fila["nombre_cargo"] ?? "-",
            "depto"   => $fila["nombre_depto"] ?? "-",
        ];
    }
}

// Filtro por nombre de supervisor o empleado
if ($buscar != "") {
    $equipos = array_filter($equipos, fn($eq) =>
        stripos($eq["nombre"], $buscar) !== false ||
        count(array_filter($eq["empleados"], fn($e) =>
            stripos($e["nombre"], $buscar) !== false ||
            stripos($e["cargo"],  $buscar) !== false ||
            stripos($e["depto"],  $buscar) !== false
        )) > 0
    );
}
?>

<style>
    .equipo-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 3px 14px rgba(130,140,106,0.10);
        overflow: hidden;
        height: 100%;
    }
    .equipo-header {
        background: var(--verde);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .sup-avatar {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: var(--coral);
        color: white;
        font-weight: 800;
        font-size: 0.95rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .sup-nombre {
        font-weight: 700;
        font-size: 0.97rem;
        color: white;
    }
    .sup-badge {
        margin-left: auto;
        background: rgba(255,255,255,0.18);
        color: white;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .equipo-body {
        padding: 14px 18px;
    }
    .emp-row {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f4eeee;
    }
    .emp-row:last-child { border-bottom: none; }
    .emp-avatar-sm {
        width: 30px; height: 30px;
        border-radius: 50%;
        background: var(--lavender);
        color: var(--verde-dark);
        font-weight: 700;
        font-size: 0.72rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .emp-nombre { font-size: 0.88rem; font-weight: 600; color: var(--text-dark); }
    .emp-meta   { font-size: 0.76rem; color: #a09888; }
    .emp-link   { margin-left: auto; }
    .sin-empleados {
        color: #bbb;
        font-size: 0.84rem;
        font-style: italic;
        padding: 10px 0;
        text-align: center;
    }
</style>

<div class="page-header">
    <h2>Supervisores y sus equipos</h2>
    <span class="badge" style="background:var(--sage);color:white;font-size:0.82rem;padding:6px 14px;">
        <?php echo count($equipos); ?> supervisor<?php echo count($equipos) != 1 ? 'es' : ''; ?>
    </span>
</div>

<form method="get" class="mb-4">
    <div class="input-group" style="max-width:400px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);">
            <i class="bi bi-search" style="color:var(--sage);"></i>
        </span>
        <input type="text" name="buscar" class="form-control"
               placeholder="Buscar supervisor, empleado, cargo o depto…"
               value="<?php echo htmlspecialchars($buscar); ?>">
        <?php if ($buscar != "") { ?>
            <a href="equipos.php" class="btn btn-secondary"><i class="bi bi-x"></i></a>
        <?php } else { ?>
            <button class="btn btn-primary" type="submit">Buscar</button>
        <?php } ?>
    </div>
    <?php if ($buscar != "") { ?>
        <small class="text-muted mt-1 d-block">
            <?php echo count($equipos); ?> resultado<?php echo count($equipos) != 1 ? 's' : ''; ?>
            para "<strong><?php echo htmlspecialchars($buscar); ?></strong>"
        </small>
    <?php } ?>
</form>

<?php if (empty($equipos)) { ?>
    <div class="card p-4 text-center text-muted">
        <?php echo $buscar ? "Sin resultados para \"$buscar\"." : "No hay supervisores activos en el sistema."; ?>
    </div>
<?php } else { ?>

<div class="row g-4">
    <?php foreach ($equipos as $eq) { ?>
    <div class="col-md-6 col-xl-4">
        <div class="equipo-card">

            <div class="equipo-header">
                <div class="sup-avatar">
                    <?php
                    $partes = explode(" ", $eq["nombre"]);
                    echo strtoupper(mb_substr($partes[0], 0, 1) . mb_substr($partes[1] ?? '', 0, 1));
                    ?>
                </div>
                <div>
                    <div class="sup-nombre"><?php echo htmlspecialchars($eq["nombre"]); ?></div>
                    <div style="font-size:0.70rem;color:rgba(255,255,255,0.55);">SUPERVISOR</div>
                </div>
                <div class="sup-badge">
                    <?php echo count($eq["empleados"]); ?>
                    empleado<?php echo count($eq["empleados"]) != 1 ? 's' : ''; ?>
                </div>
            </div>

            <div class="equipo-body">
                <?php if (empty($eq["empleados"])) { ?>
                    <div class="sin-empleados">Sin empleados a cargo actualmente</div>
                <?php } else { ?>
                    <?php foreach ($eq["empleados"] as $emp) { ?>
                    <div class="emp-row">
                        <div class="emp-avatar-sm">
                            <?php
                            $p = explode(" ", $emp["nombre"]);
                            echo strtoupper(mb_substr($p[0], 0, 1) . mb_substr($p[1] ?? '', 0, 1));
                            ?>
                        </div>
                        <div>
                            <div class="emp-nombre"><?php echo htmlspecialchars($emp["nombre"]); ?></div>
                            <div class="emp-meta">
                                <?php echo htmlspecialchars($emp["cargo"]); ?>
                                &middot; <?php echo htmlspecialchars($emp["depto"]); ?>
                            </div>
                        </div>
                        <a href="ver.php?legajo=<?php echo $emp["legajo"]; ?>"
                           class="emp-link btn btn-sm btn-info" title="Ver perfil">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                    <?php } ?>
                <?php } ?>
            </div>

        </div>
    </div>
    <?php } ?>
</div>

<?php } ?>

<?php require_once '../../includes/footer.php'; ?>
