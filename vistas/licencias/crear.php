<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Solicitud_licencia_controlador.php';
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$controlador = new Solicitud_licencia_controlador();
$errores = [];
$mensaje = "";
$nro_creado = null;

$empleados = $controlador->obtenerEmpleados();
$empleados_array = [];
while ($emp = $empleados->fetch_assoc()) {
    $empleados_array[] = $emp;
}

$tipos = $controlador->obtenerTiposLicencia();
$tipos_array = [];
while ($tipo = $tipos->fetch_assoc()) {
    $tipos_array[] = $tipo;
}

$tipos_doc = $controlador->obtenerTiposDocumento();
$tipos_doc_array = [];
while ($td = $tipos_doc->fetch_assoc()) {
    $tipos_doc_array[] = $td;
}

// Si es EMPLEADO o SUPERVISOR en modo personal, se autocompleta su propio legajo
$legajo_fijo = null;
$es_modo_personal = ($_SESSION["rol"] == "EMPLEADO") ||
                    ($_SESSION["rol"] == "SUPERVISOR" && isset($_GET["personal"]) && $_GET["personal"] == "1");
if ($es_modo_personal) {
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION["id_usuario"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $legajo_fijo = $row["legajo"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $legajo          = $legajo_fijo ?? $_POST["legajo"];
    $cod_tipo        = $_POST["cod_tipo"];
    $fecha_solicitud = $_POST["fecha_solicitud"];
    $fecha_inicio    = $_POST["fecha_inicio"];
    $fecha_fin       = $_POST["fecha_fin"];
    $observacion     = trim($_POST["observacion"]);

    $resultado = $controlador->crear($legajo, $cod_tipo, $fecha_solicitud, $fecha_inicio, $fecha_fin, $observacion);

    if (is_int($resultado)) {
        $nro_creado = $resultado;

        // Si el tipo requiere certificado y se adjuntó uno, guardarlo
        $cod_tipo_doc   = trim($_POST["cod_tipo_doc"] ?? "");
        $nombre_archivo = trim($_POST["nombre_certificado"] ?? "");
        $tipo_requiere_cert = false;
        foreach ($tipos_array as $t) {
            if ($t["cod_tipo"] === $cod_tipo && $t["requiere_certificado"]) {
                $tipo_requiere_cert = true;
                break;
            }
        }
        if ($tipo_requiere_cert && $cod_tipo_doc !== "" && $nombre_archivo !== "") {
            $controlador->agregarDocumento($nro_creado, $cod_tipo_doc, $nombre_archivo);
            $mensaje = "Solicitud creada correctamente con certificado adjunto. Estado: PENDIENTE.";
        } else {
            $mensaje = "Solicitud creada correctamente. Estado: PENDIENTE.";
        }
    } else {
        $errores = $resultado;
    }
}

// Resumen de disponibilidad
$resumen = null;
if ($legajo_fijo) {
    $resumen = $controlador->obtenerResumenDisponibilidad($legajo_fijo);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Nueva Solicitud de Licencia</h2>
    <a href="listar.php<?php echo $es_modo_personal && $_SESSION['rol'] == 'SUPERVISOR' ? '?personal=1' : ''; ?>"
       class="btn btn-secondary">← Volver</a>
</div>

<?php if ($resumen) { ?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h6 class="mb-3" style="color: var(--verde-dark);">Tu disponibilidad de licencias</h6>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center p-3"
                     style="background: var(--lavender); border-radius: 10px;">
                    <span class="fw-bold">Total anual disponible (excluye Enfermedad con certificado)</span>
                    <span class="badge bg-primary fs-6"><?php echo $resumen["disponibles_total"]; ?> / <?php echo $resumen["total_anual"]; ?> días</span>
                </div>
            </div>
        </div>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Máximo anual</th>
                    <th>Usados</th>
                    <th>Disponibles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resumen["tipos"] as $t) { ?>
                <tr>
                    <td><?php echo $t["nombre"]; ?>
                        <?php if ($t["requiere_certificado"]) { ?>
                            <span class="badge bg-info" style="font-size:0.68rem;">requiere certificado</span>
                        <?php } ?>
                    </td>
                    <td><?php echo $t["dias_max"]; ?></td>
                    <td><?php echo $t["dias_usados"]; ?></td>
                    <td>
                        <span class="badge <?php echo $t["dias_disponibles_tipo"] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo $t["dias_disponibles_tipo"]; ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if ($mensaje != "") { ?>
            <div class="alert alert-success">
                <?php echo $mensaje; ?>
                <?php if ($nro_creado) { ?>
                    <a href="ver.php?nro=<?php echo $nro_creado; ?>" class="alert-link ms-2">Ver solicitud #<?php echo $nro_creado; ?></a>
                <?php } ?>
            </div>
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

        <?php if ($nro_creado === null) { ?>
        <?php
        $anio = date("Y");
        $fecha_min = "$anio-01-01";
        $fecha_max = "$anio-12-31";
        // Preservar selección del tipo si hubo error
        $cod_tipo_prev = $_POST["cod_tipo"] ?? "";
        ?>
        <form method="post">
            <div class="row">
                <?php if (!$legajo_fijo) { ?>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Empleado</label>
                    <select name="legajo" id="selectEmpleado" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($empleados_array as $emp) { ?>
                            <option value="<?php echo $emp["legajo"]; ?>"
                                <?php echo (($_POST["legajo"] ?? "") == $emp["legajo"]) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($emp["nombre_completo"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
                <div class="<?php echo $legajo_fijo ? 'col-md-12' : 'col-md-6'; ?> mb-3">
                    <label class="form-label fw-bold">Tipo de licencia</label>
                    <select name="cod_tipo" id="selectTipo" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($tipos_array as $tipo) { ?>
                            <option value="<?php echo $tipo["cod_tipo"]; ?>"
                                <?php echo ($cod_tipo_prev == $tipo["cod_tipo"]) ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($tipo["nombre"]); ?> (máx. <?php echo $tipo["dias_max"]; ?> días/año)
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha de solicitud</label>
                    <input type="date" name="fecha_solicitud" id="fechaSolicitud" class="form-control" required
                           min="<?php echo $fecha_min; ?>" max="<?php echo $fecha_max; ?>"
                           value="<?php echo htmlspecialchars($_POST["fecha_solicitud"] ?? date('Y-m-d')); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" id="fechaInicio" class="form-control" required
                           min="<?php echo $fecha_min; ?>" max="<?php echo $fecha_max; ?>"
                           value="<?php echo htmlspecialchars($_POST["fecha_inicio"] ?? ""); ?>">
                    <div class="form-text">Mínimo 3 días después de la solicitud.</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha de fin</label>
                    <input type="date" name="fecha_fin" class="form-control" required
                           min="<?php echo $fecha_min; ?>" max="<?php echo $fecha_max; ?>"
                           value="<?php echo htmlspecialchars($_POST["fecha_fin"] ?? ""); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Observación</label>
                <textarea name="observacion" class="form-control" maxlength="500" rows="3" placeholder="Motivo o aclaración adicional (opcional)..."><?php echo htmlspecialchars($_POST["observacion"] ?? ""); ?></textarea>
            </div>

            <!-- Sección certificado — visible cuando el tipo seleccionado requiere certificado -->
            <div id="seccion-certificado" style="display:none;">
                <hr>
                <h6 style="color: var(--verde-dark);">
                    <i class="bi bi-file-earmark-medical"></i> Certificado
                </h6>
                <div class="alert mb-3" style="background: var(--lavender); border-color: var(--tea); font-size:0.88rem;">
                    Si adjuntás el certificado ahora, los días de esta licencia <strong>no se descontarán</strong> de tu total anual.
                    Solo podés adjuntarlo en este momento.
                </div>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold">Tipo de documento</label>
                        <select name="cod_tipo_doc" class="form-select">
                            <option value="">-- Seleccioná --</option>
                            <?php foreach ($tipos_doc_array as $td) { ?>
                                <option value="<?php echo $td["cod_tipo_doc"]; ?>">
                                    <?php echo htmlspecialchars($td["nombre"]); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-7 mb-3">
                        <label class="form-label fw-bold">Nombre del archivo</label>
                        <input type="text" name="nombre_certificado" class="form-control"
                               placeholder="Ej: certificado_medico.pdf">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Crear Solicitud</button>
        </form>
        <?php } ?>
    </div>
</div>

<script>
(function () {
    var selectTipo = document.getElementById('selectTipo');
    var seccion    = document.getElementById('seccion-certificado');
    if (!selectTipo || !seccion) return;

    var tiposCertificado = <?php echo json_encode(
        array_values(array_map(
            fn($t) => $t['cod_tipo'],
            array_filter($tipos_array, fn($t) => $t['requiere_certificado'] == 1)
        ))
    ); ?>;

    function toggleCert() {
        seccion.style.display = tiposCertificado.includes(selectTipo.value) ? 'block' : 'none';
    }

    selectTipo.addEventListener('change', toggleCert);
    toggleCert();
})();

(function () {
    var fechaSol   = document.getElementById('fechaSolicitud');
    var fechaIni   = document.getElementById('fechaInicio');
    if (!fechaSol || !fechaIni) return;

    function actualizarMinInicio() {
        if (!fechaSol.value) return;
        var base = new Date(fechaSol.value + 'T00:00:00');
        base.setDate(base.getDate() + 3);
        var min = base.toISOString().split('T')[0];
        fechaIni.min = min;
        if (fechaIni.value && fechaIni.value < min) {
            fechaIni.value = '';
        }
    }

    fechaSol.addEventListener('change', actualizarMinInicio);
    actualizarMinInicio();
})();

<?php if (!$legajo_fijo) { ?>
document.getElementById('selectEmpleado').addEventListener('change', function() {
    const legajo = this.value;
    if (!legajo) return;
    window.location.href = 'crear.php?legajo_consulta=' + legajo;
});
<?php } ?>
</script>

<?php require_once '../../includes/footer.php'; ?>
