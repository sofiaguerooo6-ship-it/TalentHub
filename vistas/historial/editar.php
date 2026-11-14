<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Historial_cargo_controlador.php';
require_once '../../includes/header.php';

$controlador = new Historial_cargo_controlador();
$errores_puesto = [];
$errores_sup    = [];
$mensaje_puesto = "";
$mensaje_sup    = "";
$id = $_GET["id"] ?? null;

$datos = $controlador->buscar($id)->fetch_assoc();

$supervisores_actuales = [];
$res_sup = $controlador->obtenerSupervisores($id);
while ($sup = $res_sup->fetch_assoc()) {
    $supervisores_actuales[] = $sup["legajo_supervisor"];
}

// Listas para los selectores
$empleados_arr     = $controlador->obtenerEmpleados()->fetch_all(MYSQLI_ASSOC);
$cargos_arr        = $controlador->obtenerCargos()->fetch_all(MYSQLI_ASSOC);
$deptos_arr        = $controlador->obtenerDepartamentos()->fetch_all(MYSQLI_ASSOC);
$supervisores_disp = $controlador->obtenerSupervisoresUsuarios()->fetch_all(MYSQLI_ASSOC);

// Rol del empleado de este historial
require_once '../../config/Conexion.php';
$_cx_edit = Conexion::conectar();
$_stRol = $_cx_edit->prepare(
    "SELECT r.nombre_rol FROM usuario u JOIN rol r ON u.id_rol = r.id_rol WHERE u.legajo = ? LIMIT 1"
);
$_stRol->bind_param("i", $datos["legajo"]);
$_stRol->execute();
$_rolRow = $_stRol->get_result()->fetch_assoc();
$rol_empleado_historial = $_rolRow["nombre_rol"] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["accion"] == "puesto") {
        $legajo      = $_POST["legajo"];
        $cod_cargo   = $_POST["cod_cargo"];
        $cod_depto   = $_POST["cod_depto"];
        $fecha_inicio = $_POST["fecha_inicio"];
        $fecha_fin    = $_POST["fecha_fin"] ?? "";

        $res = $controlador->actualizar($id, $legajo, $cod_cargo, $cod_depto, $fecha_inicio, $fecha_fin);
        if ($res === "OK") {
            $mensaje_puesto = "Datos del puesto actualizados correctamente.";
            $datos = $controlador->buscar($id)->fetch_assoc();
        } else {
            $errores_puesto = is_array($res) ? $res : [$res];
        }

    } elseif ($_POST["accion"] == "supervisores") {
        $supervisores = array_filter($_POST["supervisores"] ?? [], 'strlen');
        $res = $controlador->actualizarSupervisores($id, $supervisores);
        if ($res === "OK") {
            $mensaje_sup = "Supervisores actualizados correctamente.";
            $supervisores_actuales = $supervisores;
        } else {
            $errores_sup[] = $res;
        }
    }
}
?>

<div class="page-header">
    <h2>Editar registro de historial</h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

<?php if (!empty($datos)) { ?>

<div class="row g-4">

    <!-- COLUMNA IZQUIERDA: todos los datos del puesto -->
    <div class="col-md-5">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                Datos del puesto
            </h5>

            <?php if ($mensaje_puesto != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_puesto; ?></div>
            <?php } ?>
            <?php if (!empty($errores_puesto)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_puesto as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
            <?php } ?>

            <form method="post">
                <input type="hidden" name="accion" value="puesto">

                <div class="mb-3">
                    <label class="form-label">Empleado</label>
                    <?php
                    $nombre_emp = "";
                    foreach ($empleados_arr as $emp) {
                        if ($emp["legajo"] == $datos["legajo"]) {
                            $nombre_emp = $emp["nombre_completo"];
                            break;
                        }
                    }
                    ?>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombre_emp); ?>" disabled>
                    <input type="hidden" name="legajo" value="<?php echo $datos["legajo"]; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cargo</label>
                    <select name="cod_cargo" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($cargos_arr as $c) { ?>
                            <option value="<?php echo $c["cod_cargo"]; ?>"
                                <?php echo $datos["cod_cargo"] == $c["cod_cargo"] ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($c["nombre_cargo"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Departamento</label>
                    <select name="cod_depto" class="form-select" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($deptos_arr as $d) { ?>
                            <option value="<?php echo $d["cod_depto"]; ?>"
                                <?php echo $datos["cod_depto"] == $d["cod_depto"] ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($d["nombre_depto"]); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control"
                           value="<?php echo htmlspecialchars($datos["fecha_inicio"] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha de fin <small class="text-muted">(dejar vacío = puesto actual)</small></label>
                    <input type="date" name="fecha_fin" class="form-control"
                           value="<?php echo htmlspecialchars($datos["fecha_fin"] ?? ''); ?>">
                </div>

                <button type="submit" class="btn btn-primary">Guardar datos del puesto</button>
            </form>
        </div>
    </div>

    <!-- COLUMNA DERECHA: supervisores -->
    <div class="col-md-7">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                Supervisores asignados
            </h5>

            <?php if ($mensaje_sup != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_sup; ?></div>
            <?php } ?>
            <?php if (!empty($errores_sup)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_sup as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
            <?php } ?>

            <?php if (empty($supervisores_disp)) { ?>
                <div class="alert alert-danger py-2">No hay usuarios con rol SUPERVISOR o ADMINISTRADOR activos en el sistema.</div>
            <?php } ?>

            <form method="post">
                <input type="hidden" name="accion" value="supervisores">
                <div class="mb-3">
                    <label class="form-label">Supervisores</label>
                    <div class="input-group mb-1">
                        <span class="input-group-text" style="background:white;border-color:var(--tea);">
                            <i class="bi bi-search" style="color:var(--sage);font-size:0.85rem;"></i>
                        </span>
                        <input type="text" id="buscarSupEdit" class="form-control form-control-sm"
                               placeholder="Buscar supervisor o administrador...">
                    </div>
                    <?php if ($rol_empleado_historial === "SUPERVISOR"): ?>
                    <div style="background:#f0f5e8;border-radius:8px;padding:10px 14px;font-size:0.88rem;color:var(--verde-dark);border-left:3px solid var(--sage);">
                        <i class="bi bi-shield-check me-1"></i>
                        Supervisado por el <strong>Administrador del sistema</strong>.
                    </div>
                    <?php else: ?>
                    <div id="listaSupEdit" style="max-height:200px;overflow-y:auto;border:2px solid var(--tea);border-radius:8px;padding:6px 12px;background:white;">
                        <?php foreach ($supervisores_disp as $sup) {
                            if (is_null($sup['legajo'])) continue; ?>
                        <div class="sup-item-edit py-1"
                             data-nombre="<?php echo strtolower(htmlspecialchars($sup['nombre_completo'] . ' ' . $sup['nombre_rol'])); ?>">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" name="supervisores[]"
                                       value="<?php echo $sup['legajo']; ?>"
                                       id="se_<?php echo $sup['legajo']; ?>"
                                       class="form-check-input mt-0"
                                       <?php echo in_array($sup['legajo'], $supervisores_actuales) ? 'checked' : ''; ?>>
                                <label for="se_<?php echo $sup['legajo']; ?>"
                                       class="form-check-label" style="cursor:pointer;font-size:0.88rem;">
                                    <?php echo htmlspecialchars($sup['nombre_completo']); ?>
                                    <span class="badge ms-1" style="background:var(--lavender);color:var(--verde-dark);font-size:0.65rem;">
                                        <?php echo htmlspecialchars($sup['nombre_rol']); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <small class="text-muted d-block mt-1">Para quitar todos los supervisores, deseleccioná todos y guardá.</small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Guardar supervisores</button>
            </form>

<script>
(function () {
    var inp = document.getElementById('buscarSupEdit');
    if (!inp) return;
    inp.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('.sup-item-edit').forEach(function (item) {
            item.style.display = item.dataset.nombre.includes(q) ? '' : 'none';
        });
    });
})();
</script>
        </div>
    </div>

</div>

<?php } else { ?>
    <div class="alert alert-danger">Registro no encontrado.</div>
<?php } ?>

<?php require_once '../../includes/footer.php'; ?>
