<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Localidad_controlador.php';
require_once '../../includes/header.php';

$controlador = new Localidad_controlador();
$errores = [];
$mensaje = "";
$provincias = $controlador->obtenerProvincias();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod_postal    = trim($_POST["cod_postal"]);
    $nombre_ciudad = trim($_POST["nombre_ciudad"]);
    $cod_provincia = $_POST["cod_provincia"];

    $resultado = $controlador->crear($cod_postal, $nombre_ciudad, $cod_provincia);

    if ($resultado === "OK") {
        $mensaje = "Localidad creada correctamente.";
    } else {
        $errores = $resultado;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nueva Localidad</h2>
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
            <div class="mb-3">
                <label class="form-label fw-bold">Código Postal</label>
                <input type="text" name="cod_postal" class="form-control" maxlength="10" placeholder="Ej: 1000" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Ciudad</label>
                <input type="text" name="nombre_ciudad" class="form-control" maxlength="100" placeholder="Ej: Buenos Aires" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Provincia</label>
                <select name="cod_provincia" class="form-select" required>
                    <option value="">-- Seleccioná --</option>
                    <?php while ($prov = $provincias->fetch_assoc()) { ?>
                        <option value="<?php echo $prov["cod_provincia"]; ?>">
                            <?php echo $prov["nombre_provincia"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Localidad</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>