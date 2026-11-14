<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../controladores/Solicitud_licencia_controlador.php';
require_once '../../includes/header.php';

$controlador = new Solicitud_licencia_controlador();
$errores = [];
$mensaje = "";
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nro_solicitud      = $_POST["nro_solicitud"];
    $cod_estado_nuevo   = $_POST["cod_estado"];
    $observacion        = trim($_POST["observacion"]);
    $legajo_responsable = $_SESSION["id_usuario"];

    $resultado = $controlador->cambiarEstado($nro_solicitud, $cod_estado_nuevo, $legajo_responsable, $observacion);

    if ($resultado === "OK") {
        $mensaje = "Estado actualizado correctamente.";
    } else {
        $errores = $resultado;
    }
}

if (isset($_GET["nro"])) {
    $nro = $_GET["nro"];
    $resultado = $controlador->buscar($nro);
    $datos = $resultado->fetch_assoc();
}

$estados = $controlador->obtenerEstados();
$estados_array = [];
while ($est = $estados->fetch_assoc()) {
    $estados_array[] = $est;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cambiar Estado de Solicitud</h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

<?php if (!empty($datos)) { ?>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <p><strong>Solicitud Nro:</strong> <?php echo $datos["nro_solicitud"]; ?></p>
        <p><strong>Estado actual:</strong> <?php echo $datos["cod_estado"]; ?></p>
        <p><strong>Fechas:</strong> <?php echo $datos["fecha_inicio"]; ?> al <?php echo $datos["fecha_fin"]; ?></p>
    </div>
</div>
<?php } ?>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if ($mensaje != "") { ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php } ?>

        <?php if (!empty($errores)) { ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errores as $error) { ?>
                        <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <form method="post">
            <input type="hidden" name="nro_solicitud" value="<?php echo $datos["nro_solicitud"] ?? ''; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Nuevo estado</label>
                <select name="cod_estado" class="form-select" required>
                    <option value="">-- Seleccioná --</option>
                    <?php foreach ($estados_array as $est) { ?>
                        <option value="<?php echo $est["cod_estado"]; ?>"
                            <?php echo (isset($datos["cod_estado"]) && $est["cod_estado"] == $datos["cod_estado"]) ? "selected" : ""; ?>>
                            <?php echo $est["nombre_estado"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Observación</label>
                <textarea name="observacion" class="form-control" maxlength="500" rows="3"><?php echo $datos["observacion"] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambio</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>