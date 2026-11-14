<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$cx = Conexion::conectar();
$errores = [];
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST["accion"] ?? "";

    if ($accion === "crear") {
        $cod    = trim($_POST["cod_tipo"]);
        $nombre = trim($_POST["nombre"]);
        $dias   = (int)$_POST["dias_max"];
        $cert   = isset($_POST["requiere_certificado"]) ? 1 : 0;
        $rem    = isset($_POST["remunerada"]) ? 1 : 0;

        if ($cod === "" || $nombre === "" || $dias <= 0) {
            $errores[] = "Completá todos los campos obligatorios.";
        } else {
            $st = $cx->prepare("INSERT INTO tipo_licencia (cod_tipo, nombre, dias_max, requiere_certificado, remunerada) VALUES (?,?,?,?,?)");
            $st->bind_param("ssiii", $cod, $nombre, $dias, $cert, $rem);
            if ($st->execute()) {
                $mensaje = "Tipo de licencia creado correctamente.";
            } else {
                $errores[] = "Error al crear. Verificá que el código no exista ya.";
            }
        }

    } elseif ($accion === "editar") {
        $cod_orig = $_POST["cod_orig"];
        $nombre   = trim($_POST["nombre"]);
        $dias     = (int)$_POST["dias_max"];
        $cert     = isset($_POST["requiere_certificado"]) ? 1 : 0;
        $rem      = isset($_POST["remunerada"]) ? 1 : 0;

        if ($nombre === "" || $dias <= 0) {
            $errores[] = "Completá todos los campos.";
        } else {
            $st = $cx->prepare("UPDATE tipo_licencia SET nombre=?, dias_max=?, requiere_certificado=?, remunerada=? WHERE cod_tipo=?");
            $st->bind_param("siiis", $nombre, $dias, $cert, $rem, $cod_orig);
            $st->execute() ? $mensaje = "Actualizado correctamente." : $errores[] = "Error al actualizar.";
        }

    } elseif ($accion === "eliminar") {
        $cod = $_POST["cod_tipo"];
        // Verificar si tiene solicitudes asociadas
        $stChk = $cx->prepare("SELECT COUNT(*) AS total FROM solicitud_licencia WHERE cod_tipo = ?");
        $stChk->bind_param("s", $cod);
        $stChk->execute();
        $uso = $stChk->get_result()->fetch_assoc()["total"];
        if ($uso > 0) {
            $errores[] = "No se puede eliminar: tiene $uso solicitud(es) asociada(s).";
        } else {
            $st = $cx->prepare("DELETE FROM tipo_licencia WHERE cod_tipo = ?");
            $st->bind_param("s", $cod);
            $st->execute() ? $mensaje = "Eliminado correctamente." : $errores[] = "Error al eliminar.";
        }
    }
}

$tipos = $cx->query("SELECT * FROM tipo_licencia ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

// Para el modal de edición
$editar = null;
if (isset($_GET["editar"])) {
    $st = $cx->prepare("SELECT * FROM tipo_licencia WHERE cod_tipo = ?");
    $st->bind_param("s", $_GET["editar"]);
    $st->execute();
    $editar = $st->get_result()->fetch_assoc();
}
?>

<div class="page-header">
    <h2>Tipos de licencia</h2>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-success"><?php echo $mensaje; ?></div>
<?php endif; ?>
<?php if (!empty($errores)): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e) echo "<li>$e</li>"; ?></ul></div>
<?php endif; ?>

<div class="row g-4">

    <!-- LISTA -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Días máx.</th>
                            <th>Certificado</th>
                            <th>Remunerada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tipos)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay tipos de licencia.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($tipos as $t): ?>
                        <tr>
                            <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);"><?php echo htmlspecialchars($t["cod_tipo"]); ?></span></td>
                            <td><?php echo htmlspecialchars($t["nombre"]); ?></td>
                            <td><?php echo $t["dias_max"]; ?></td>
                            <td>
                                <?php if ($t["requiere_certificado"]): ?>
                                    <span class="badge" style="background:#A8D4AE;color:#2A5C35;">Sí</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#ddd;color:#555;">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t["remunerada"]): ?>
                                    <span class="badge" style="background:#A8D4AE;color:#2A5C35;">Sí</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#E4A0A0;color:#6B1A1A;">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="?editar=<?php echo urlencode($t["cod_tipo"]); ?>"
                                       class="btn btn-sm"
                                       style="background:var(--lavender);border:1px solid var(--tea);color:var(--verde-dark);"
                                       title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar <?php echo htmlspecialchars($t["nombre"]); ?>?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="cod_tipo" value="<?php echo htmlspecialchars($t["cod_tipo"]); ?>">
                                        <button type="submit" class="btn btn-sm"
                                                style="background:#fde8e8;border:1px solid #f5b0b0;color:#b03030;"
                                                title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FORMULARIO CREAR / EDITAR -->
    <div class="col-md-5">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                <?php echo $editar ? "Editar tipo" : "Nuevo tipo de licencia"; ?>
            </h5>
            <form method="post">
                <input type="hidden" name="accion" value="<?php echo $editar ? 'editar' : 'crear'; ?>">
                <?php if ($editar): ?>
                    <input type="hidden" name="cod_orig" value="<?php echo htmlspecialchars($editar["cod_tipo"]); ?>">
                <?php endif; ?>

                <?php if (!$editar): ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="cod_tipo" class="form-control" maxlength="10"
                           placeholder="Ej: MAT" required>
                    <small class="text-muted">Corto y en mayúsculas. No se puede cambiar después.</small>
                </div>
                <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($editar["cod_tipo"]); ?>" disabled>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" maxlength="100"
                           placeholder="Ej: Maternidad"
                           value="<?php echo $editar ? htmlspecialchars($editar["nombre"]) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Días máximos por año</label>
                    <input type="number" name="dias_max" class="form-control" min="1" max="365"
                           placeholder="Ej: 30"
                           value="<?php echo $editar ? $editar["dias_max"] : ''; ?>" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="requiere_certificado" class="form-check-input" id="chk_cert"
                           <?php echo ($editar && $editar["requiere_certificado"]) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="chk_cert">Requiere certificado médico</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remunerada" class="form-check-input" id="chk_rem"
                           <?php echo (!$editar || $editar["remunerada"]) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="chk_rem">Remunerada</label>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editar ? "Guardar cambios" : "Crear tipo"; ?>
                    </button>
                    <?php if ($editar): ?>
                        <a href="listar.php" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once '../../includes/footer.php'; ?>
