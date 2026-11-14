<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Departamento_controlador.php';
require_once '../../includes/header.php';

$controlador = new Departamento_controlador();
$errores = [];
$mensaje = "";
$localidades = $controlador->obtenerLocalidades();
$localidades_array = [];
while ($loc = $localidades->fetch_assoc()) {
    $localidades_array[] = $loc;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod_depto       = trim($_POST["cod_depto"]);
    $nombre_depto    = trim($_POST["nombre_depto"]);
    $localidades_sel = $_POST["localidades"] ?? [];
    $sede_principal  = $_POST["sede_principal"] ?? "";

    $resultado = $controlador->crear($cod_depto, $nombre_depto, $localidades_sel, $sede_principal);

    if ($resultado === "OK") {
        $mensaje = "Departamento creado correctamente.";
    } else {
        $errores = $resultado;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nuevo Departamento</h2>
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
                <input type="text" name="cod_depto" class="form-control" maxlength="10" placeholder="Ej: RRHH" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nombre</label>
                <input type="text" name="nombre_depto" class="form-control" maxlength="100" placeholder="Ej: Recursos Humanos" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Localidades <small class="text-muted">(Ctrl+clic para seleccionar varias)</small></label>
                <select name="localidades[]" class="form-select" multiple size="6" required>
                    <?php foreach ($localidades_array as $loc) { ?>
                        <option value="<?php echo $loc["cod_postal"]; ?>">
                            <?php echo $loc["nombre_ciudad"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Sede principal</label>
                <select name="sede_principal" class="form-select">
                    <option value="">-- Seleccioná --</option>
                    <?php foreach ($localidades_array as $loc) { ?>
                        <option value="<?php echo $loc["cod_postal"]; ?>">
                            <?php echo $loc["nombre_ciudad"]; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Crear Departamento</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>