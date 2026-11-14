<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Departamento_controlador.php';
require_once '../../includes/header.php';

$controlador = new Departamento_controlador();
$buscar      = trim($_GET["buscar"] ?? "");

$todas = $controlador->listar()->fetch_all(MYSQLI_ASSOC);

$filas = $buscar != ""
    ? array_values(array_filter($todas, fn($r) =>
        stripos($r["cod_depto"],    $buscar) !== false ||
        stripos($r["nombre_depto"], $buscar) !== false ||
        stripos($r["localidades"] ?? "", $buscar) !== false))
    : $todas;
?>

<div class="page-header">
    <h2>Departamentos</h2>
    <a href="crear.php" class="btn btn-primary">+ Nuevo departamento</a>
</div>

<form method="get" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);"><i class="bi bi-search" style="color:var(--sage);"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por código, nombre o localidad…" value="<?php echo htmlspecialchars($buscar); ?>">
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
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Localidades</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filas)) { ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Sin resultados.</td></tr>
                <?php } ?>
                <?php foreach ($filas as $fila) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila["cod_depto"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre_depto"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["localidades"] ?? '-'); ?></td>
                    <td>
                        <a href="editar.php?cod=<?php echo $fila["cod_depto"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?cod=<?php echo $fila["cod_depto"]; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar este departamento?')">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
