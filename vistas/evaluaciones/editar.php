<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../controladores/Evaluacion_desempeno_controlador.php';
require_once '../../includes/header.php';

$controlador = new Evaluacion_desempeno_controlador();
$errores = [];
$mensaje = "";
$datos = [];

$empleados = $controlador->obtenerEmpleados();
$empleados_array = [];
while ($emp = $empleados->fetch_assoc()) {
    $empleados_array[] = $emp;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id               = $_POST["id_evaluacion"];
    $legajo_evaluado  = $_POST["legajo_evaluado"];
    $legajo_evaluador = $_POST["legajo_evaluador"] !== "" ? $_POST["legajo_evaluador"] : null;
    $periodo          = trim($_POST["periodo"]);
    $puntuacion       = $_POST["puntuacion"];
    $comentario       = trim($_POST["comentario"]);

    $resultado = $controlador->actualizar($id, $legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario);

    if ($resultado === "OK") {
        $mensaje = "Evaluación actualizada correctamente.";
    } else {
        $errores = $resultado;
    }
} elseif (isset($_GET["id"])) {
    $id = $_GET["id"];
    $resultado = $controlador->buscar($id);
    $datos = $resultado->fetch_assoc();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Editar Evaluación</h2>
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
            <input type="hidden" name="id_evaluacion" value="<?php echo $datos["id_evaluacion"] ?? ''; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Empleado evaluado</label>
                    <select name="legajo_evaluado" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($empleados_array as $emp) { ?>
                            <option value="<?php echo $emp["legajo"]; ?>"
                                <?php echo (isset($datos["legajo_evaluado"]) && $datos["legajo_evaluado"] == $emp["legajo"]) ? "selected" : ""; ?>>
                                <?php echo $emp["nombre_completo"]; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Evaluador</label>
                    <select name="legajo_evaluador" class="form-select">
                        <option value="" <?php echo (isset($datos["legajo_evaluador"]) && $datos["legajo_evaluador"] === null) ? "selected" : ""; ?>>Administrador</option>
                        <?php foreach ($empleados_array as $emp) { ?>
                            <option value="<?php echo $emp["legajo"]; ?>"
                                <?php echo (isset($datos["legajo_evaluador"]) && $datos["legajo_evaluador"] == $emp["legajo"]) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($emp["nombre_completo"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Período</label>
                    <input type="text" name="periodo" class="form-control"
                           value="<?php echo $datos["periodo"] ?? ''; ?>" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Puntuación (0-100)</label>
                    <input type="number" name="puntuacion" class="form-control"
                           value="<?php echo $datos["puntuacion"] ?? ''; ?>" min="0" max="100" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Comentario</label>
                <textarea name="comentario" class="form-control" maxlength="500" rows="3"><?php echo $datos["comentario"] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>