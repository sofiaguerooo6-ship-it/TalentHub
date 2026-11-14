<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Cargo_controlador();
$errores = [];
$mensaje = "";
$datos = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod_cargo    = trim($_POST["cod_cargo"]);
    $nombre_cargo = trim($_POST["nombre_cargo"]);
    $banda_min    = $_POST["banda_min"];
    $banda_max    = $_POST["banda_max"];
    $cod_nivel    = $_POST["cod_nivel"];

    $resultado = $controlador->actualizar($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel);

    if ($resultado === "OK") {
        $mensaje = "Cargo actualizado correctamente.";
    } else {
        $errores = $resultado;
    }
} elseif (isset($_GET["cod"])) {
    $cod = $_GET["cod"];
    $resultado = $controlador->buscar($cod);
    $datos = $resultado->fetch_assoc();
}

$niveles = $controlador->obtenerNiveles();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Cargo</h2>
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
            <input type="hidden" name="cod_cargo" value="<?php echo $datos["cod_cargo"] ?? ''; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold">Código</label>
                <input type="text" class="form-control" value="<?php echo $datos["cod_cargo"] ?? ''; ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre_cargo" class="form-control"
                       value="<?php echo $datos["nombre_cargo"] ?? ''; ?>" maxlength="100" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Banda salarial mínima</label>
                    <input type="number" step="0.01" name="banda_min" class="form-control"
                           value="<?php echo $datos["banda_min"] ?? ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Banda salarial máxima</label>
                    <input type="number" step="0.01" name="banda_max" class="form-control"
                           value="<?php echo $datos["banda_max"] ?? ''; ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nivel jerárquico</label>
                <select name="cod_nivel" class="form-select" required>
                    <option value="">-- Seleccioná --</option>
                    <?php while ($niv = $niveles->fetch_assoc()) { ?>
                        <option value="<?php echo $niv["cod_nivel"]; ?>"
                            <?php echo (isset($datos["cod_nivel"]) && $datos["cod_nivel"] == $niv["cod_nivel"]) ? "selected" : ""; ?>>
                            <?php echo $niv["nombre_nivel"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>