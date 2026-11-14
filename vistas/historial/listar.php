<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../controladores/Historial_cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Historial_cargo_controlador();
$buscar      = trim($_GET["buscar"] ?? "");

if ($_SESSION["rol"] == "SUPERVISOR") {
    require_once '../../config/Conexion.php';
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row               = $stmt->get_result()->fetch_assoc();
    $legajo_supervisor = $row["legajo"];
    $todas = $controlador->listarPorSupervisor($legajo_supervisor)->fetch_all(MYSQLI_ASSOC);
} else {
    $todas = $controlador->listar()->fetch_all(MYSQLI_ASSOC);
}

$filas = $buscar != ""
    ? array_values(array_filter($todas, fn($r) =>
        stripos($r["empleado"]      ?? "", $buscar) !== false ||
        stripos($r["cargo"]         ?? "", $buscar) !== false ||
        stripos($r["departamento"]  ?? "", $buscar) !== false ||
        stripos($r["supervisores"]  ?? "", $buscar) !== false))
    : $todas;
?>

<div class="page-header">
    <h2>Historial de Cargos</h2>
</div>

<form method="get" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);"><i class="bi bi-search" style="color:var(--sage);"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por empleado, cargo o depto…" value="<?php echo htmlspecialchars($buscar); ?>">
        <?php if ($buscar != "") { ?>
            <a href="listar.php" class="btn btn-secondary"><i class="bi bi-x"></i></a>
        <?php } else { ?>
            <button class="btn btn-primary" type="submit">Buscar</button>
        <?php } ?>
    </div>
    <?php if ($buscar != "") { ?>
        <small class="text-muted mt-1 d-block"><?php echo count($filas); ?> resultado<?php echo count($filas) != 1 ? 's' : ''; ?> para "<strong><?php echo htmlspecialchars($buscar); ?></strong>"</small>
    <?php } ?>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Supervisores</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                        <th>Acciones</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filas)) { ?>
                <tr><td colspan="<?php echo $_SESSION['rol'] == 'ADMINISTRADOR' ? 8 : 7; ?>" class="text-center text-muted py-4">Sin resultados.</td></tr>
                <?php } ?>
                <?php foreach ($filas as $fila) { ?>
                <tr>
                    <td><?php echo $fila["id_hist"]; ?></td>
                    <td><?php echo htmlspecialchars($fila["empleado"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["cargo"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["departamento"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["supervisores"] ?? '-'); ?></td>
                    <td><?php echo $fila["fecha_inicio"]; ?></td>
                    <td>
                        <?php if (empty($fila["fecha_fin"])) { ?>
                            <span class="badge" style="background:var(--sage);color:white;">Actual</span>
                        <?php } else { ?>
                            <?php echo $fila["fecha_fin"]; ?>
                        <?php } ?>
                    </td>
                    <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                    <td>
                        <a href="editar.php?id=<?php echo $fila["id_hist"]; ?>" class="btn btn-sm btn-primary">Editar</a>
                        <?php if (empty($fila["fecha_fin"])) { ?>
                            <a href="cerrar.php?id=<?php echo $fila["id_hist"]; ?>" class="btn btn-sm btn-warning">Cerrar</a>
                        <?php } ?>
                        <a href="eliminar.php?id=<?php echo $fila["id_hist"]; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar este registro?')">Eliminar</a>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
