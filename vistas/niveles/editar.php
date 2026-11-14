<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Nivel_jerarquico_controlador.php';
require_once '../../includes/header.php';

$controlador = new Nivel_jerarquico_controlador();
$errores = [];
$mensaje = "";
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod         = trim($_POST["cod_nivel"]);
    $nombre      = trim($_POST["nombre_nivel"]);
    $descripcion = trim($_POST["descripcion"]);

    $resultado = $controlador->actualizar($cod, $nombre, $descripcion);

    if ($resultado === "OK") {
        $mensaje = "Nivel jerárquico actualizado correctamente.";
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
    <h2>Editar Nivel Jerárquico</h2>
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
            <input type="hidden" name="cod_nivel" value="<?php echo $datos["cod_nivel"] ?? ''; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Código</label>
                <input type="text" class="form-control" value="<?php echo $datos["cod_nivel"] ?? ''; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre_nivel" class="form-control"
                       value="<?php echo $datos["nombre_nivel"] ?? ''; ?>" maxlength="80" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" maxlength="255"><?php echo $datos["descripcion"] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>