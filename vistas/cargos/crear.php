<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Cargo_controlador();
$errores = [];
$mensaje = "";
$niveles = $controlador->obtenerNiveles();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod_cargo    = trim($_POST["cod_cargo"]);
    $nombre_cargo = trim($_POST["nombre_cargo"]);
    $banda_min    = $_POST["banda_min"];
    $banda_max    = $_POST["banda_max"];
    $cod_nivel    = $_POST["cod_nivel"];

    $resultado = $controlador->crear($cod_cargo, $nombre_cargo, $banda_min, $banda_max, $cod_nivel);

    if ($resultado === "OK") {
        $mensaje = "Cargo creado correctamente.";
    } else {
        $errores = $resultado;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nuevo Cargo</h2>
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
                <label class="form-label fw-bold">Código</label>
                <input type="text" name="cod_cargo" class="form-control" maxlength="10" placeholder="Ej: GER" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre_cargo" class="form-control" maxlength="100" placeholder="Ej: Gerente de RRHH" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Banda salarial mínima</label>
                    <input type="number" step="0.01" name="banda_min" class="form-control" placeholder="Ej: 500000" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Banda salarial máxima</label>
                    <input type="number" step="0.01" name="banda_max" class="form-control" placeholder="Ej: 800000" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nivel jerárquico</label>
                <select name="cod_nivel" class="form-select" required>
                    <option value="">-- Seleccioná --</option>
                    <?php while ($niv = $niveles->fetch_assoc()) { ?>
                        <option value="<?php echo $niv["cod_nivel"]; ?>">
                            <?php echo $niv["nombre_nivel"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Cargo</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>