<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR', 'SUPERVISOR']);
require_once '../../controladores/Evaluacion_desempeno_controlador.php';
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$controlador = new Evaluacion_desempeno_controlador();
$errores = [];
$mensaje = "";

// El evaluador se toma de la sesión — el usuario no puede elegirlo (evita fraudes).
// ADMINISTRADOR: legajo_evaluador = null → guarda NULL en BD → muestra "Administrador"
// SUPERVISOR: legajo_evaluador = su propio legajo → guarda su legajo → muestra su nombre
$legajo_evaluador = null;
$nombre_evaluador = 'Administrador';
if ($_SESSION["rol"] == "SUPERVISOR") {
    $cx = Conexion::conectar();
    $st = $cx->prepare(
        "SELECT u.legajo, CONCAT(e.nombre, ' ', e.apellido) AS nombre
         FROM usuario u JOIN empleado e ON u.legajo = e.legajo
         WHERE u.id_usuario = ?"
    );
    $st->bind_param("i", $_SESSION["id_usuario"]);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    $legajo_evaluador = $row["legajo"];
    $nombre_evaluador = $row["nombre"];
}

$empleados = $controlador->obtenerEmpleados();
$empleados_array = [];
while ($emp = $empleados->fetch_assoc()) {
    $empleados_array[] = $emp;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $legajo_evaluado = $_POST["legajo_evaluado"];
    $periodo         = trim($_POST["periodo"]);
    $puntuacion      = $_POST["puntuacion"];
    $comentario      = trim($_POST["comentario"]);

    $resultado = $controlador->crear($legajo_evaluado, $legajo_evaluador, $periodo, $puntuacion, $comentario);

    if ($resultado === "OK") {
        $mensaje = "Evaluación creada correctamente.";
    } else {
        $errores = $resultado;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nueva Evaluación de Desempeño</h2>
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
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Empleado evaluado</label>
                    <select name="legajo_evaluado" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($empleados_array as $emp) { ?>
                            <option value="<?php echo $emp["legajo"]; ?>"
                                <?php echo (($_POST["legajo_evaluado"] ?? "") == $emp["legajo"]) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($emp["nombre_completo"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Evaluador</label>
                    <div class="form-control bg-light text-muted">
                        <?php echo htmlspecialchars($nombre_evaluador); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Período</label>
                    <input type="text" name="periodo" class="form-control"
                           value="<?php echo htmlspecialchars($_POST["periodo"] ?? ""); ?>"
                           placeholder="Ej: 2026-1S" maxlength="20" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Puntuación (0-100)</label>
                    <input type="number" name="puntuacion" class="form-control"
                           value="<?php echo htmlspecialchars($_POST["puntuacion"] ?? ""); ?>"
                           min="0" max="100" placeholder="0 - 100" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Comentario</label>
                <textarea name="comentario" class="form-control" maxlength="500" rows="3"
                          placeholder="Comentarios sobre el desempeño del empleado..."><?php echo htmlspecialchars($_POST["comentario"] ?? ""); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear Evaluación</button>
        </form>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
