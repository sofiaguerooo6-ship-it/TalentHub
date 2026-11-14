<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../controladores/Empleado_controlador.php';
require_once '../../includes/header.php';

$controlador = new Empleado_controlador();
$busqueda = trim($_GET["buscar"] ?? "");
$campo    = $_GET["campo"] ?? "apellido";
$orden    = $_GET["orden"] ?? "apellido_asc";
if (!in_array($campo, ["nombre", "apellido"])) $campo = "apellido";
$ordenes_validos = ["apellido_asc", "apellido_desc", "nombre_asc", "nombre_desc"];
if (!in_array($orden, $ordenes_validos)) $orden = "apellido_asc";

if ($_SESSION["rol"] == "SUPERVISOR") {
    require_once '../../config/Conexion.php';
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $legajo_supervisor = $row["legajo"];

    if ($busqueda != "") {
        $resultado = $controlador->buscarPorSupervisorYNombre($legajo_supervisor, $busqueda, $campo, $orden);
    } else {
        $resultado = $controlador->listarPorSupervisor($legajo_supervisor, $orden);
    }
} else {
    if ($busqueda != "") {
        $resultado = $controlador->buscarPorNombre($busqueda, $campo, $orden);
    } else {
        $resultado = $controlador->listar($orden);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Empleados</h2>
    <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
        <a href="crear.php" class="btn btn-primary">+ Nuevo empleado</a>
    <?php } ?>
</div>

<form method="get" class="mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label fw-bold" style="font-size:0.82rem;">Buscar por</label>
            <select name="campo" class="form-select">
                <option value="apellido" <?php echo ($campo == "apellido") ? "selected" : ""; ?>>Apellido</option>
                <option value="nombre"   <?php echo ($campo == "nombre")   ? "selected" : ""; ?>>Nombre</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label fw-bold" style="font-size:0.82rem;">&nbsp;</label>
            <input type="text" name="buscar" class="form-control"
                   placeholder="Escribí para filtrar..."
                   value="<?php echo htmlspecialchars($busqueda); ?>">
        </div>
        <div class="col-auto">
            <label class="form-label fw-bold" style="font-size:0.82rem;">Ordenar por</label>
            <select name="orden" class="form-select">
                <option value="apellido_asc"  <?php echo ($orden == "apellido_asc")  ? "selected" : ""; ?>>Apellido A→Z</option>
                <option value="apellido_desc" <?php echo ($orden == "apellido_desc") ? "selected" : ""; ?>>Apellido Z→A</option>
                <option value="nombre_asc"    <?php echo ($orden == "nombre_asc")    ? "selected" : ""; ?>>Nombre A→Z</option>
                <option value="nombre_desc"   <?php echo ($orden == "nombre_desc")   ? "selected" : ""; ?>>Nombre Z→A</option>
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label" style="font-size:0.82rem;">&nbsp;</label>
            <button type="submit" class="btn btn-primary d-block">Buscar</button>
        </div>
        <?php if ($busqueda != "" || $orden != "apellido_asc") { ?>
        <div class="col-auto">
            <label class="form-label" style="font-size:0.82rem;">&nbsp;</label>
            <a href="listar.php" class="btn btn-secondary d-block">Limpiar</a>
        </div>
        <?php } ?>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Legajo</th>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $hay_resultados = false;
                while ($fila = $resultado->fetch_assoc()) {
                    $hay_resultados = true;
                ?>
                <tr>
                    <td><?php echo $fila["legajo"]; ?></td>
                    <td><?php echo $fila["dni"]; ?></td>
                    <td><?php echo $fila["nombre"]; ?></td>
                    <td><?php echo $fila["apellido"]; ?></td>
                    <td><?php echo $fila["email"]; ?></td>
                    <td><?php echo $fila["telefono"]; ?></td>
                    <td><?php echo $fila["fecha_ingreso"]; ?></td>
                    <td>
                        <a href="ver.php?legajo=<?php echo $fila["legajo"]; ?>" class="btn btn-sm btn-info">Ver</a>
                        <?php if ($_SESSION["rol"] == "ADMINISTRADOR") { ?>
                            <a href="editar.php?legajo=<?php echo $fila["legajo"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <a href="crear.php?legajo=<?php echo $fila["legajo"]; ?>" class="btn btn-sm" style="background:var(--sage);color:white;" title="Asignar rol, cargo y dirección">Completar</a>
                            <a href="eliminar.php?legajo=<?php echo $fila["legajo"]; ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Seguro que querés eliminar este empleado?')">Eliminar</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!$hay_resultados) { ?>
    <p class="text-muted text-center mt-3">No se encontraron empleados con ese nombre.</p>
<?php } ?>

<?php require_once '../../includes/footer.php'; ?>