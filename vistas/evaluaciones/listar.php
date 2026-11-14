<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
require_once '../../controladores/Evaluacion_desempeno_controlador.php';
require_once '../../includes/header.php';

$controlador  = new Evaluacion_desempeno_controlador();
$modo_personal = false;

if ($_SESSION["rol"] == "EMPLEADO") {
    require_once '../../config/Conexion.php';
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row      = $stmt->get_result()->fetch_assoc();
    $legajo   = $row["legajo"];
    $resultado = $controlador->listarPorLegajo($legajo);

} elseif ($_SESSION["rol"] == "SUPERVISOR") {
    require_once '../../config/Conexion.php';
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row               = $stmt->get_result()->fetch_assoc();
    $legajo_supervisor = $row["legajo"];

    $modo_personal = isset($_GET["personal"]) && $_GET["personal"] == "1";

    if ($modo_personal) {
        $resultado = $controlador->listarPorLegajo($legajo_supervisor);
    } else {
        $resultado = $controlador->listarPorSupervisor($legajo_supervisor);
    }

} else {
    verificarRol(['ADMINISTRADOR']);
    $resultado = $controlador->listar();
}

$buscar = trim($_GET["buscar"] ?? "");

$todas = $resultado->fetch_all(MYSQLI_ASSOC);

$evaluaciones = $buscar != ""
    ? array_values(array_filter($todas, fn($r) =>
        stripos($r["evaluado"]  ?? "", $buscar) !== false ||
        stripos($r["evaluador"] ?? "", $buscar) !== false ||
        stripos($r["periodo"]   ?? "", $buscar) !== false))
    : $todas;

// ¿Puede crear/editar/eliminar?
$puede_gestionar = $_SESSION["rol"] == "ADMINISTRADOR" ||
                   ($_SESSION["rol"] == "SUPERVISOR" && !$modo_personal);
?>

<div class="page-header">
    <h2>
        <?php
        if ($modo_personal) echo "Mis Evaluaciones";
        elseif ($_SESSION["rol"] == "SUPERVISOR") echo "Evaluaciones del equipo";
        else echo "Evaluaciones de Desempeño";
        ?>
    </h2>
    <?php if ($puede_gestionar) { ?>
        <a href="crear.php" class="btn btn-primary">+ Nueva evaluación</a>
    <?php } ?>
</div>

<form method="get" class="mb-3">
    <?php if ($modo_personal) { ?><input type="hidden" name="personal" value="1"><?php } ?>
    <div class="input-group" style="max-width:380px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);"><i class="bi bi-search" style="color:var(--sage);"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por evaluado, evaluador o período…" value="<?php echo htmlspecialchars($buscar); ?>">
        <?php if ($buscar != "") { ?>
            <a href="listar.php<?php echo $modo_personal ? '?personal=1' : ''; ?>" class="btn btn-secondary"><i class="bi bi-x"></i></a>
        <?php } else { ?>
            <button class="btn btn-primary" type="submit">Buscar</button>
        <?php } ?>
    </div>
    <?php if ($buscar != "") { ?>
        <small class="text-muted mt-1 d-block"><?php echo count($evaluaciones); ?> resultado<?php echo count($evaluaciones) != 1 ? 's' : ''; ?> para "<strong><?php echo htmlspecialchars($buscar); ?></strong>"</small>
    <?php } ?>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Evaluado</th>
                    <th>Evaluador</th>
                    <th>Período</th>
                    <th>Puntuación</th>
                    <th>Comentario</th>
                    <?php if ($puede_gestionar) { ?>
                        <th>Acciones</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($evaluaciones)) { ?>
                <tr>
                    <td colspan="<?php echo $puede_gestionar ? 7 : 6; ?>" class="text-center text-muted py-4">
                        No hay evaluaciones registradas.
                    </td>
                </tr>
                <?php } ?>
                <?php foreach ($evaluaciones as $fila) { ?>
                <tr>
                    <td>
                        <span class="badge" style="background:var(--lavender);color:var(--verde-dark);">
                            #<?php echo $fila["id_evaluacion"]; ?>
                        </span>
                    </td>
                    <td><strong><?php echo htmlspecialchars($fila["evaluado"]); ?></strong></td>
                    <td><?php echo htmlspecialchars($fila["evaluador"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["periodo"]); ?></td>
                    <td>
                        <?php
                        $punt  = $fila["puntuacion"];
                        if ($punt >= 75)     { $bg = '#A8D4AE'; $fg = '#2A5C35'; }
                        elseif ($punt >= 50) { $bg = '#F2D48A'; $fg = '#7A5000'; }
                        else                 { $bg = '#E4A0A0'; $fg = '#6B1A1A'; }
                        ?>
                        <span style="background:<?php echo $bg; ?>;color:<?php echo $fg; ?>;
                                     font-size:0.78rem;padding:4px 10px;border-radius:20px;font-weight:600;">
                            <?php echo $punt; ?>/100
                        </span>
                    </td>
                    <td style="max-width:220px; white-space:normal; font-size:0.86rem;">
                        <?php echo htmlspecialchars($fila["comentario"] ?? '-'); ?>
                    </td>
                    <?php if ($puede_gestionar) { ?>
                    <td>
                        <a href="editar.php?id=<?php echo $fila["id_evaluacion"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?id=<?php echo $fila["id_evaluacion"]; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar esta evaluación?')">Eliminar</a>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
