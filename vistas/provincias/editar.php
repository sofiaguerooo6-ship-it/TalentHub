<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Provincia_controlador.php';
require_once '../../includes/header.php';

$controlador = new Provincia_controlador();
$errores = [];
$mensaje = "";
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod    = trim($_POST["cod_provincia"]);
    $nombre = trim($_POST["nombre_provincia"]);

    $resultado = $controlador->actualizar($cod, $nombre);

    if ($resultado === "OK") {
        $mensaje = "Provincia actualizada correctamente.";
    } else {
        $errores = $resultado;
    }
} elseif (isset($_GET["cod"])) {
    $cod = $_GET["cod"];
    $resultado = $controlador->buscar($cod);
    $datos = $resultado->fetch_assoc();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Provincia</h2>
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

        <form method="post">
            <input type="hidden" name="cod_provincia" value="<?php echo $datos["cod_provincia"] ?? ''; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Código</label>
                <input type="text" class="form-control" value="<?php echo $datos["cod_provincia"] ?? ''; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre_provincia" class="form-control"
                       value="<?php echo $datos["nombre_provincia"] ?? ''; ?>" maxlength="100" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>