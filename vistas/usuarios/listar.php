<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Usuario_controlador.php';
require_once '../../includes/header.php';

$controlador = new Usuario_controlador();
$buscar      = trim($_GET["buscar"] ?? "");

$todas = $controlador->listar()->fetch_all(MYSQLI_ASSOC);

$filas = $buscar != ""
    ? array_values(array_filter($todas, fn($r) =>
        stripos($r["nombre_usuario"] ?? "", $buscar) !== false ||
        stripos($r["nombre_rol"]     ?? "", $buscar) !== false ||
        stripos($r["empleado"]       ?? "", $buscar) !== false))
    : $todas;
?>

<div class="page-header">
    <h2>Usuarios del Sistema</h2>
</div>

<form method="get" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);"><i class="bi bi-search" style="color:var(--sage);"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por usuario, rol o empleado…" value="<?php echo htmlspecialchars($buscar); ?>">
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
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Empleado</th>
                    <th>Estado</th>
                    <th>Fecha alta</th>
                    <th>Fecha baja</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filas)) { ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Sin resultados.</td></tr>
                <?php } ?>
                <?php foreach ($filas as $fila) { ?>
                <tr>
                    <td><?php echo $fila["id_usuario"]; ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre_usuario"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre_rol"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["empleado"] ?? '-'); ?></td>
                    <td>
                        <?php if ($fila["estado"] == 1) { ?>
                            <span class="badge" style="background:var(--sage);color:white;">Activo</span>
                        <?php } else { ?>
                            <span class="badge" style="background:#E4A0A0;color:#6B1A1A;">Inactivo</span>
                        <?php } ?>
                    </td>
                    <td><?php echo $fila["fecha_alta"]; ?></td>
                    <td><?php echo $fila["fecha_baja"] ?? '-'; ?></td>
                    <td>
                        <?php if ($fila["estado"] == 1) { ?>
                            <a href="desactivar.php?id=<?php echo $fila["id_usuario"]; ?>"
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('¿Seguro que querés desactivar este usuario?')">Desactivar</a>
                        <?php } else { ?>
                            <a href="activar.php?id=<?php echo $fila["id_usuario"]; ?>" class="btn btn-sm btn-success">Activar</a>
                        <?php } ?>
                        <a href="eliminar.php?id=<?php echo $fila["id_usuario"]; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar este usuario?')">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
