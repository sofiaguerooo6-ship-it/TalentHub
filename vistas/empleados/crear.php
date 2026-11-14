<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Empleado_controlador.php';
require_once '../../controladores/Empleado_direccion_controlador.php';
require_once '../../controladores/Historial_cargo_controlador.php';
require_once '../../controladores/Usuario_controlador.php';
require_once '../../config/Conexion.php';

$controlador      = new Empleado_controlador();
$dir_controlador  = new Empleado_direccion_controlador();
$hist_controlador = new Historial_cargo_controlador();
$usr_controlador  = new Usuario_controlador();

$errores_emp  = [];
$errores_dir  = [];
$errores_hist = [];
$errores_usr  = [];
$mensaje_dir  = "";
$mensaje_hist = "";
$mensaje_usr  = "";
$legajo_creado = null;

if (isset($_GET["legajo"])) {
    $legajo_creado = (int)$_GET["legajo"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["accion"] == "empleado") {
        $res = $controlador->crear(
            trim($_POST["dni"]),
            trim($_POST["nombre"]),
            trim($_POST["apellido"]),
            trim($_POST["email"]),
            trim($_POST["telefono"]),
            $_POST["fecha_ingreso"]
        );
        if (is_int($res)) {
            header("Location: crear.php?legajo=" . $res);
            exit;
        } else {
            $errores_emp = $res;
        }

    } elseif ($_POST["accion"] == "rol") {
        $legajo_creado = (int)$_POST["legajo"];
        $id_rol_sel    = (int)$_POST["id_rol"];

        if (empty($id_rol_sel)) {
            $errores_usr = ["Debe seleccionar un rol."];
        } else {
            $cx = Conexion::conectar();
            $stCheck = $cx->prepare("SELECT id_usuario FROM usuario WHERE legajo = ?");
            $stCheck->bind_param("i", $legajo_creado);
            $stCheck->execute();
            if ($stCheck->get_result()->fetch_assoc()) {
                $errores_usr = ["Este empleado ya tiene un rol asignado."];
            } else {
                $placeholder = "pending_" . $legajo_creado;
                $ph_pass     = password_hash(uniqid("th", true), PASSWORD_DEFAULT);
                $fecha_hoy   = date("Y-m-d");
                $stIns = $cx->prepare(
                    "INSERT INTO usuario (legajo, id_rol, nombre_usuario, contrasena, estado, fecha_alta)
                     VALUES (?, ?, ?, ?, 0, ?)"
                );
                $stIns->bind_param("iisss", $legajo_creado, $id_rol_sel, $placeholder, $ph_pass, $fecha_hoy);
                if ($stIns->execute()) {
                    $mensaje_usr = "Rol asignado. El empleado podrá crear su cuenta desde el registro.";
                } else {
                    $errores_usr = ["Error al guardar el rol."];
                }
            }
        }

    } elseif ($_POST["accion"] == "direccion") {
        $legajo_creado = (int)$_POST["legajo"];
        $res = $dir_controlador->crear(
            $legajo_creado,
            $_POST["cod_postal"],
            trim($_POST["calle"]),
            trim($_POST["altura"]),
            isset($_POST["es_principal"]) ? 1 : 0
        );
        if ($res === "OK") {
            $mensaje_dir = "Dirección agregada.";
        } else {
            $errores_dir = $res;
        }

    } elseif ($_POST["accion"] == "cargo") {
        $legajo_creado = (int)$_POST["legajo"];
        $supervisores  = array_filter($_POST["supervisores"] ?? [], 'strlen');
        $fecha_fin     = $_POST["fecha_fin"] ?: null;
        $id_rol_nuevo  = intval($_POST["id_rol_cargo"] ?? 0);

        $res = $hist_controlador->crear(
            $legajo_creado,
            $_POST["cod_cargo"],
            $_POST["cod_depto"],
            $_POST["fecha_inicio"],
            $fecha_fin,
            $supervisores
        );
        if ($res === "OK") {
            // Actualizar rol si se seleccionó uno y el usuario existe
            if ($id_rol_nuevo) {
                $cx_rol = Conexion::conectar();
                $stRol = $cx_rol->prepare("UPDATE usuario SET id_rol = ? WHERE legajo = ?");
                $stRol->bind_param("ii", $id_rol_nuevo, $legajo_creado);
                $stRol->execute();
            }
            $mensaje_hist = "Cargo y rol asignados correctamente.";
        } else {
            $errores_hist = $res;
        }
    }
}

// Datos para fase 2
$dirs_arr          = [];
$localidades_arr   = [];
$cargos_arr        = [];
$deptos_arr        = [];
$supervisores_disp = [];
$roles_arr         = [];
$datos_emp         = null;
$historial_actual  = null;
$usuario_existente = null;

if (!$legajo_creado) {
    // Fase 1: incluir header antes del HTML
    require_once '../../includes/header.php';
}

if ($legajo_creado) {
    $datos_emp = $controlador->buscar($legajo_creado)->fetch_assoc();

    // Si el empleado no existe, volver al listado (header aún no enviado)
    if (!$datos_emp) {
        header("Location: listar.php");
        exit;
    }

    require_once '../../includes/header.php';

    $dirs_arr  = $dir_controlador->listarPorLegajo($legajo_creado)->fetch_all(MYSQLI_ASSOC);

    $locs = $dir_controlador->obtenerLocalidades();
    while ($l = $locs->fetch_assoc()) $localidades_arr[] = $l;

    $cargos_arr        = $hist_controlador->obtenerCargos()->fetch_all(MYSQLI_ASSOC);
    $deptos_arr        = $hist_controlador->obtenerDepartamentos()->fetch_all(MYSQLI_ASSOC);
    $supervisores_disp = $hist_controlador->obtenerSupervisoresUsuarios()->fetch_all(MYSQLI_ASSOC);

    // Roles disponibles para cambiar junto al cargo (excluye ADMINISTRADOR)
    $cx_roles   = Conexion::conectar();
    $roles_cargo = $cx_roles->query(
        "SELECT id_rol, nombre_rol FROM rol WHERE nombre_rol IN ('EMPLEADO','SUPERVISOR') ORDER BY nombre_rol"
    )->fetch_all(MYSQLI_ASSOC);

    // Rol actual del usuario (si ya tiene)
    $id_rol_actual_cargo = null;
    if ($usuario_existente) {
        $stRolAct = $cx_roles->prepare(
            "SELECT u.id_rol FROM usuario u WHERE u.legajo = ? LIMIT 1"
        );
        $stRolAct->bind_param("i", $legajo_creado);
        $stRolAct->execute();
        $rowRolAct = $stRolAct->get_result()->fetch_assoc();
        $id_rol_actual_cargo = $rowRolAct["id_rol"] ?? null;
    }

    // Roles disponibles (solo EMPLEADO y SUPERVISOR para asignar desde aquí)
    $roles_res = $usr_controlador->obtenerRoles();
    while ($r = $roles_res->fetch_assoc()) {
        if ($r["nombre_rol"] !== "ADMINISTRADOR") $roles_arr[] = $r;
    }

    $cx = Conexion::conectar();

    // ¿Ya tiene cargo activo?
    $stH = $cx->prepare("SELECT id_hist FROM historial_cargo WHERE legajo = ? AND fecha_fin IS NULL LIMIT 1");
    $stH->bind_param("i", $legajo_creado);
    $stH->execute();
    $historial_actual = $stH->get_result()->fetch_assoc();

    // ¿Ya tiene usuario (activo o pendiente)?
    $stU = $cx->prepare(
        "SELECT u.id_usuario, u.estado, r.nombre_rol
         FROM usuario u JOIN rol r ON u.id_rol = r.id_rol
         WHERE u.legajo = ? LIMIT 1"
    );
    $stU->bind_param("i", $legajo_creado);
    $stU->execute();
    $usuario_existente = $stU->get_result()->fetch_assoc();

    // Nombre de usuario sugerido: inicial nombre + apellido sin espacios
    $usuario_sugerido = strtolower(
        mb_substr($datos_emp["nombre"] ?? "", 0, 1) .
        preg_replace('/\s+/', '', $datos_emp["apellido"] ?? "")
    );
}
?>

<div class="page-header">
    <h2>Nuevo Empleado</h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

<?php if (!$legajo_creado) { ?>
<!-- ── FASE 1: datos básicos ── -->
<div class="card p-4">
    <?php if (!empty($errores_emp)) { ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errores_emp as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
        </div>
    <?php } ?>
    <form method="post">
        <input type="hidden" name="accion" value="empleado">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">DNI</label>
                <input type="text" name="dni" class="form-control" maxlength="8"
                       placeholder="Sin puntos ni guiones"
                       value="<?php echo htmlspecialchars($_POST["dni"] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control"
                       value="<?php echo htmlspecialchars($_POST["fecha_ingreso"] ?? ''); ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" maxlength="100"
                       placeholder="Ej: Juan"
                       value="<?php echo htmlspecialchars($_POST["nombre"] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" maxlength="100"
                       placeholder="Ej: García"
                       value="<?php echo htmlspecialchars($_POST["apellido"] ?? ''); ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" maxlength="150"
                       placeholder="Ej: juan@empresa.com"
                       value="<?php echo htmlspecialchars($_POST["email"] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" maxlength="30"
                       placeholder="Ej: 1122334455"
                       value="<?php echo htmlspecialchars($_POST["telefono"] ?? ''); ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Crear Empleado →</button>
    </form>
</div>

<?php } else { ?>
<!-- ── FASE 2 ── -->

<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-check-circle-fill"></i>
    Empleado <strong><?php echo htmlspecialchars($datos_emp["nombre"] . " " . $datos_emp["apellido"]); ?></strong>
    creado — legajo <strong>#<?php echo $legajo_creado; ?></strong>.
    Completá los datos abajo o <a href="listar.php" class="ms-1">ir al listado</a>.
</div>

<!-- ── ROL ── -->
<div class="card p-4 mb-4">
    <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
        <i class="bi bi-person-badge"></i> Rol en el sistema
        <?php if ($usuario_existente) { ?>
            <span class="badge ms-2" style="background:var(--sage);color:white;font-size:0.7rem;">
                <?php echo $usuario_existente["estado"] == 1 ? "Cuenta activa" : "Pendiente de registro"; ?>
            </span>
        <?php } ?>
    </h5>

    <?php if ($mensaje_usr != "") { ?>
        <div class="alert alert-success py-2"><?php echo $mensaje_usr; ?></div>
    <?php } ?>
    <?php if (!empty($errores_usr)) { ?>
        <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_usr as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
    <?php } ?>

    <?php if ($usuario_existente && $usuario_existente["estado"] == 1) { ?>
        <p class="text-muted mb-0" style="font-size:0.88rem;">
            Este empleado ya tiene una cuenta activa con rol
            <strong><?php echo htmlspecialchars($usuario_existente["nombre_rol"]); ?></strong>.
            Podés administrarla desde <a href="../usuarios/listar.php">Usuarios</a>.
        </p>
    <?php } elseif ($usuario_existente && $usuario_existente["estado"] == 0) { ?>
        <div class="d-flex align-items-center gap-3">
            <span class="badge fs-6" style="background:var(--sage);color:white;padding:8px 14px;">
                <?php echo htmlspecialchars($usuario_existente["nombre_rol"]); ?>
            </span>
            <span class="text-muted" style="font-size:0.88rem;">
                Rol asignado. El empleado aún no creó su cuenta — puede hacerlo desde
                <a href="../../auth/registro.php">registro</a>.
            </span>
        </div>
    <?php } else { ?>
    <form method="post">
        <input type="hidden" name="accion" value="rol">
        <input type="hidden" name="legajo" value="<?php echo $legajo_creado; ?>">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Rol</label>
                <select name="id_rol" class="form-select" required>
                    <option value="">-- Seleccioná --</option>
                    <?php foreach ($roles_arr as $rol) { ?>
                        <option value="<?php echo $rol["id_rol"]; ?>">
                            <?php echo htmlspecialchars($rol["nombre_rol"]); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary">Asignar rol</button>
            </div>
        </div>
        <small class="text-muted d-block mt-2">
            El empleado elegirá su usuario y contraseña cuando se registre en el sistema.
        </small>
    </form>
    <?php } ?>
</div>

<div class="row g-4">

    <!-- ── DIRECCIÓN ── -->
    <div class="col-md-6">
        <div class="card p-4 h-100">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                <i class="bi bi-geo-alt"></i> Dirección
            </h5>

            <?php if ($mensaje_dir != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_dir; ?></div>
            <?php } ?>
            <?php if (!empty($errores_dir)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_dir as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
            <?php } ?>

            <?php if (!empty($dirs_arr)) { ?>
                <table class="table table-sm mb-3" style="font-size:0.85rem;">
                    <thead><tr><th>Calle</th><th>Ciudad</th><th>Principal</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($dirs_arr as $dir) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dir["calle"] . " " . $dir["altura"]); ?></td>
                            <td><?php echo htmlspecialchars($dir["nombre_ciudad"]); ?></td>
                            <td>
                                <?php if ($dir["es_principal"]) { ?>
                                    <span class="badge" style="background:var(--sage);color:white;">Sí</span>
                                <?php } else { ?>
                                    <span class="badge" style="background:#ddd;color:#555;">No</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="eliminar_direccion.php?id=<?php echo $dir["id_direccion"]; ?>&legajo=<?php echo $legajo_creado; ?>&redir=crear"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar?')">✕</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-muted mb-3" style="font-size:0.88rem;">Sin direcciones aún.</p>
            <?php } ?>

            <form method="post">
                <input type="hidden" name="accion" value="direccion">
                <input type="hidden" name="legajo" value="<?php echo $legajo_creado; ?>">
                <div class="mb-2">
                    <label class="form-label">Localidad</label>
                    <select name="cod_postal" class="form-select form-select-sm" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($localidades_arr as $loc) { ?>
                            <option value="<?php echo $loc["cod_postal"]; ?>"><?php echo htmlspecialchars($loc["nombre_ciudad"]); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-8">
                        <label class="form-label">Calle</label>
                        <input type="text" name="calle" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label">Altura</label>
                        <input type="text" name="altura" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="mb-2 form-check">
                    <input type="checkbox" name="es_principal" class="form-check-input" id="principal">
                    <label class="form-check-label" for="principal" style="font-size:0.85rem;">Es dirección principal</label>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Agregar dirección</button>
            </form>
        </div>
    </div>

    <!-- ── CARGO ── -->
    <div class="col-md-6">
        <div class="card p-4 h-100">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                <i class="bi bi-briefcase"></i> Cargo / Puesto
                <?php if ($historial_actual) { ?>
                    <span class="badge ms-2" style="background:var(--sage);color:white;font-size:0.7rem;">Ya asignado</span>
                <?php } ?>
            </h5>

            <?php if ($mensaje_hist != "") { ?>
                <div class="alert alert-success py-2"><?php echo $mensaje_hist; ?></div>
            <?php } ?>
            <?php if (!empty($errores_hist)) { ?>
                <div class="alert alert-danger py-2"><ul class="mb-0"><?php foreach ($errores_hist as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
            <?php } ?>

            <?php if ($historial_actual) { ?>
                <p class="text-muted" style="font-size:0.88rem;">
                    Este empleado ya tiene un cargo activo.
                    Podés editarlo desde <a href="../historial/editar.php?id=<?php echo $historial_actual["id_hist"]; ?>">Historial → Editar</a>.
                </p>
            <?php } else { ?>
            <form method="post">
                <input type="hidden" name="accion" value="cargo">
                <input type="hidden" name="legajo" value="<?php echo $legajo_creado; ?>">

                <div class="mb-2">
                    <label class="form-label">Cargo</label>
                    <select name="cod_cargo" class="form-select form-select-sm" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($cargos_arr as $c) { ?>
                            <option value="<?php echo $c["cod_cargo"]; ?>"><?php echo htmlspecialchars($c["nombre_cargo"]); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Departamento</label>
                    <select name="cod_depto" class="form-select form-select-sm" required>
                        <option value="">-- Seleccioná --</option>
                        <?php foreach ($deptos_arr as $d) { ?>
                            <option value="<?php echo $d["cod_depto"]; ?>"><?php echo htmlspecialchars($d["nombre_depto"]); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <?php
                // Determinar si el rol actualmente seleccionado (o existente) es SUPERVISOR
                $rol_nombre_actual = "";
                foreach ($roles_cargo as $r) {
                    if ($r["id_rol"] == $id_rol_actual_cargo) {
                        $rol_nombre_actual = $r["nombre_rol"];
                        break;
                    }
                }
                $es_sup_actual = ($rol_nombre_actual === "SUPERVISOR" || ($usuario_existente["nombre_rol"] ?? "") === "SUPERVISOR");
                ?>

                <?php if (!empty($roles_cargo) && $usuario_existente): ?>
                <div class="mb-2">
                    <label class="form-label">Rol</label>
                    <select name="id_rol_cargo" id="selectRolCargo" class="form-select form-select-sm">
                        <?php foreach ($roles_cargo as $r): ?>
                            <option value="<?php echo $r["id_rol"]; ?>"
                                <?php echo $r["id_rol"] == $id_rol_actual_cargo ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($r["nombre_rol"]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php else: ?>
                    <input type="hidden" name="id_rol_cargo" value="">
                <?php endif; ?>

                <div class="mb-2">
                    <label class="form-label">Supervisor/es</label>

                    <div id="bloqueAdminCargo" style="display:<?php echo $es_sup_actual ? 'block' : 'none'; ?>;">
                        <div style="background:#f0f5e8;border-radius:8px;padding:10px 14px;font-size:0.85rem;color:var(--verde-dark);border-left:3px solid var(--sage);">
                            <i class="bi bi-shield-check me-1"></i>
                            Supervisado por el <strong>Administrador del sistema</strong>.
                        </div>
                    </div>

                    <div id="bloqueListaCargo" style="display:<?php echo $es_sup_actual ? 'none' : 'block'; ?>;">
                        <?php if (!empty($supervisores_disp)): ?>
                        <div class="input-group mb-1">
                            <span class="input-group-text" style="background:white;border-color:var(--tea);">
                                <i class="bi bi-search" style="color:var(--sage);font-size:0.85rem;"></i>
                            </span>
                            <input type="text" id="buscarSupCargo" class="form-control form-control-sm" placeholder="Buscar...">
                        </div>
                        <div id="listaSupCargo" style="max-height:130px;overflow-y:auto;border:2px solid var(--tea);border-radius:8px;padding:4px 10px;background:white;">
                            <?php foreach ($supervisores_disp as $sup):
                                if (is_null($sup['legajo'])) continue; ?>
                            <div class="sup-item-cargo py-1"
                                 data-nombre="<?php echo strtolower(htmlspecialchars($sup['nombre_completo'] . ' ' . $sup['nombre_rol'])); ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="supervisores[]"
                                           value="<?php echo $sup['legajo']; ?>"
                                           id="sc_<?php echo $sup['legajo']; ?>"
                                           class="form-check-input mt-0">
                                    <label for="sc_<?php echo $sup['legajo']; ?>"
                                           class="form-check-label" style="cursor:pointer;font-size:0.84rem;">
                                        <?php echo htmlspecialchars($sup['nombre_completo']); ?>
                                        <span class="badge ms-1" style="background:var(--lavender);color:var(--verde-dark);font-size:0.65rem;">
                                            <?php echo htmlspecialchars($sup['nombre_rol']); ?>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                            <small class="text-muted d-block">No hay supervisores disponibles.</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha fin <small class="text-muted">(opcional)</small></label>
                        <input type="date" name="fecha_fin" class="form-control form-control-sm">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Asignar cargo</button>
            </form>
            <?php } ?>
        </div>
    </div>

</div>

<div class="mt-3 text-end">
    <a href="listar.php" class="btn btn-secondary">Finalizar y volver al listado</a>
</div>

<?php } ?>

<script>
(function () {
    var sel   = document.getElementById('selectRolCargo');
    var admin = document.getElementById('bloqueAdminCargo');
    var lista = document.getElementById('bloqueListaCargo');
    var buscar = document.getElementById('buscarSupCargo');

    function toggleSupCargo() {
        var txt = sel ? sel.options[sel.selectedIndex].text.toUpperCase().trim() : '';
        if (txt === 'SUPERVISOR') {
            if (admin) admin.style.display = 'block';
            if (lista) lista.style.display = 'none';
        } else {
            if (admin) admin.style.display = 'none';
            if (lista) lista.style.display = 'block';
        }
    }

    if (sel) sel.addEventListener('change', toggleSupCargo);

    if (buscar) {
        buscar.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('.sup-item-cargo').forEach(function (item) {
                item.style.display = item.dataset.nombre.includes(q) ? '' : 'none';
            });
        });
    }
})();
</script>

<?php require_once '../../includes/footer.php'; ?>
