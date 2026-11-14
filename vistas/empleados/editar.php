<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Empleado_controlador.php';
require_once '../../controladores/Empleado_direccion_controlador.php';
require_once '../../includes/header.php';

$controlador     = new Empleado_controlador();
$dir_controlador = new Empleado_direccion_controlador();

$errores_emp = [];
$errores_dir = [];
$mensaje_emp = "";
$mensaje_dir = "";
$datos = [];

// Cargar legajo desde GET o POST
$legajo = $_GET["legajo"] ?? $_POST["legajo"] ?? null;
if (!$legajo) {
    header("Location: listar.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["accion"] == "empleado") {
        $dni           = trim($_POST["dni"]);
        $nombre        = trim($_POST["nombre"]);
        $apellido      = trim($_POST["apellido"]);
        $email         = trim($_POST["email"]);
        $telefono      = trim($_POST["telefono"]);
        $fecha_ingreso = $_POST["fecha_ingreso"];

        $res = $controlador->actualizar($legajo, $dni, $nombre, $apellido, $email, $telefono, $fecha_ingreso);
        if ($res === "OK") {
            $mensaje_emp = "Empleado actualizado correctamente.";
        } else {
            $errores_emp = $res;
        }

    } elseif ($_POST["accion"] == "direccion") {
        $cod_postal   = $_POST["cod_postal"];
        $calle        = trim($_POST["calle"]);
        $altura       = trim($_POST["altura"]);
        $es_principal = isset($_POST["es_principal"]) ? 1 : 0;

        $res = $dir_controlador->crear($legajo, $cod_postal, $calle, $altura, $es_principal);
        if ($res === "OK") {
            $mensaje_dir = "Dirección agregada correctamente.";
        } else {
            $errores_dir = $res;
        }
    }
}

$resultado = $controlador->buscar($legajo);
$datos     = $resultado->fetch_assoc();
$direcciones    = $dir_controlador->listarPorLegajo($legajo);
$localidades    = $dir_controlador->obtenerLocalidades();
$localidades_arr = [];
while ($loc = $localidades->fetch_assoc()) {
    $localidades_arr[] = $loc;
}
$dirs_arr = [];
while ($dir = $direcciones->fetch_assoc()) {
    $dirs_arr[] = $dir;
}
?>

<div class="page-header">
    <h2>Editar Empleado</h2>
    <a href="ver.php?legajo=<?php echo $legajo; ?>" class="btn btn-secondary">← Ver detalle</a>
</div>

<?php if (!empty($datos)) { ?>

<div class="row g-4">

    <!-- DATOS DEL EMPLEADO -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                Datos personales
            </h5>

            <?php if ($mensaje_emp != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_emp; ?></div>
            <?php } ?>
            <?php if (!empty($errores_emp)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_emp as $e) echo "<li>$e</li>"; ?></ul></div>
            <?php } ?>

            <form method="post">
                <input type="hidden" name="legajo" value="<?php echo $datos["legajo"]; ?>">
                <input type="hidden" name="accion" value="empleado">

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Legajo</label>
                        <input type="text" class="form-control" value="<?php echo $datos["legajo"]; ?>" disabled>
                    </div>
                    <div class="col-6">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control" value="<?php echo htmlspecialchars($datos["dni"]); ?>" maxlength="8" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($datos["nombre"]); ?>" maxlength="100" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($datos["apellido"]); ?>" maxlength="100" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($datos["email"]); ?>" maxlength="150" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($datos["telefono"] ?? ''); ?>" maxlength="30">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control" value="<?php echo $datos["fecha_ingreso"]; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
        </div>
    </div>

    <!-- DIRECCIONES -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                Direcciones
            </h5>

            <?php if ($mensaje_dir != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_dir; ?></div>
            <?php } ?>
            <?php if (!empty($errores_dir)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_dir as $e) echo "<li>$e</li>"; ?></ul></div>
            <?php } ?>

            <?php if (!empty($dirs_arr)) { ?>
                <table class="table table-sm mb-3" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>Calle</th>
                            <th>Ciudad</th>
                            <th>Principal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dirs_arr as $dir) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dir["calle"] . " " . $dir["altura"]); ?></td>
                            <td><?php echo htmlspecialchars($dir["nombre_ciudad"] . ", " . $dir["nombre_provincia"]); ?></td>
                            <td>
                                <?php if ($dir["es_principal"]) { ?>
                                    <span class="badge" style="background:var(--sage);color:white;">Sí</span>
                                <?php } else { ?>
                                    <span class="badge" style="background:#ddd;color:#555;">No</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="eliminar_direccion.php?id=<?php echo $dir["id_direccion"]; ?>&legajo=<?php echo $legajo; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar esta dirección?')">Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-muted" style="font-size:0.88rem;">No hay direcciones registradas.</p>
            <?php } ?>

            <hr>
            <p class="fw-bold mb-2" style="font-size:0.85rem;color:var(--verde-dark);">Agregar dirección</p>
            <form method="post">
                <input type="hidden" name="legajo" value="<?php echo $legajo; ?>">
                <input type="hidden" name="accion" value="direccion">
                <div class="mb-2">
                    <label class="form-label">Localidad</label>
                    <select name="cod_postal" class="form-select form-select-sm" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($localidades_arr as $loc) { ?>
                            <option value="<?php echo $loc["cod_postal"]; ?>"><?php echo htmlspecialchars($loc["nombre_ciudad"]); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-8">
                        <label class="form-label">Calle</label>
                        <input type="text" name="calle" class="form-control form-control-sm" placeholder="Nombre de la calle" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Altura</label>
                        <input type="text" name="altura" class="form-control form-control-sm" placeholder="Nro" required>
                    </div>
                </div>
                <div class="mb-2 form-check">
                    <input type="checkbox" name="es_principal" class="form-check-input" id="principal">
                    <label class="form-check-label" for="principal" style="font-size:0.85rem;">Es dirección principal</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agregar dirección</button>
            </form>
        </div>
    </div>

</div>

<?php } else { ?>
    <div class="alert alert-danger">Empleado no encontrado.</div>
<?php } ?>

<?php require_once '../../includes/footer.php'; ?>
