<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Historial_cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Historial_cargo_controlador();
$errores = [];
$mensaje = "";
$id = $_GET["id"] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_hist   = $_POST["id_hist"];
    $fecha_fin = $_POST["fecha_fin"];

    $resultado = $controlador->cerrarPuesto($id_hist, $fecha_fin);

    if ($resultado === "OK") {
        $mensaje = "Puesto cerrado correctamente.";
    } else {
        $errores = $resultado;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Cerrar Puesto Actual</h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

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

        <p class="text-muted">Ingresá la fecha en que el empleado dejó este puesto:</p>

        <form method="post">
            <input type="hidden" name="id_hist" value="<?php echo $id; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning">Cerrar puesto</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>