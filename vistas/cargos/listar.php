<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Cargo_controlador();
$buscar      = trim($_GET["buscar"] ?? "");

$todas = $controlador->listar()->fetch_all(MYSQLI_ASSOC);

$filas = $buscar != ""
    ? array_values(array_filter($todas, fn($r) =>
        stripos($r["cod_cargo"],    $buscar) !== false ||
        stripos($r["nombre_cargo"], $buscar) !== false ||
        stripos($r["nombre_nivel"], $buscar) !== false))
    : $todas;
?>

<div class="page-header">
    <h2>Cargos</h2>
    <a href="crear.php" class="btn btn-primary">+ Nuevo cargo</a>
</div>

<form method="get" class="mb-3">
    <div class="input-group" style="max-width:380px;">
        <span class="input-group-text" style="background:white;border-color:var(--tea);"><i class="bi bi-search" style="color:var(--sage);"></i></span>
        <input type="text" name="buscar" class="form-control" placeholder="Buscar por código, nombre o nivel…" value="<?php echo htmlspecialchars($buscar); ?>">
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
                    <th>Nivel</th>
                    <th>Banda mínima</th>
                    <th>Banda máxima</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filas)) { ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Sin resultados.</td></tr>
                <?php } ?>
                <?php foreach ($filas as $fila) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($fila["cod_cargo"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre_cargo"]); ?></td>
                    <td><?php echo htmlspecialchars($fila["nombre_nivel"]); ?></td>
                    <td>$<?php echo number_format($fila["banda_min"], 2, ',', '.'); ?></td>
                    <td>$<?php echo number_format($fila["banda_max"], 2, ',', '.'); ?></td>
                    <td>
                        <a href="editar.php?cod=<?php echo $fila["cod_cargo"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?cod=<?php echo $fila["cod_cargo"]; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar este cargo?')">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
