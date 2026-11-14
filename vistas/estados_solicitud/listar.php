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
        $cod    = strtoupper(trim($_POST["cod_estado"]));
        $nombre = trim($_POST["nombre_estado"]);
        $desc   = trim($_POST["descripcion"]);
        if ($cod === "" || $nombre === "") {
            $errores[] = "Código y nombre son obligatorios.";
        } else {
            $st = $cx->prepare("INSERT INTO estado_solicitud (cod_estado, nombre_estado, descripcion) VALUES (?,?,?)");
            $st->bind_param("sss", $cod, $nombre, $desc);
            $st->execute() ? $mensaje = "Estado creado correctamente." : $errores[] = "Error. El código ya puede existir.";
        }

    } elseif ($accion === "editar") {
        $cod_orig = $_POST["cod_orig"];
        $nombre   = trim($_POST["nombre_estado"]);
        $desc     = trim($_POST["descripcion"]);
        if ($nombre === "") {
            $errores[] = "El nombre es obligatorio.";
        } else {
            $st = $cx->prepare("UPDATE estado_solicitud SET nombre_estado=?, descripcion=? WHERE cod_estado=?");
            $st->bind_param("sss", $nombre, $desc, $cod_orig);
            $st->execute() ? $mensaje = "Actualizado correctamente." : $errores[] = "Error al actualizar.";
        }

    } elseif ($accion === "eliminar") {
        $cod = $_POST["cod_estado"];
        $stChk = $cx->prepare("SELECT COUNT(*) AS total FROM solicitud_licencia WHERE cod_estado = ?");
        $stChk->bind_param("s", $cod);
        $stChk->execute();
        $uso = $stChk->get_result()->fetch_assoc()["total"];
        if ($uso > 0) {
            $errores[] = "No se puede eliminar: hay $uso solicitud(es) con este estado.";
        } else {
            $st = $cx->prepare("DELETE FROM estado_solicitud WHERE cod_estado = ?");
            $st->bind_param("s", $cod);
            $st->execute() ? $mensaje = "Eliminado correctamente." : $errores[] = "Error al eliminar.";
        }
    }
}

$estados = $cx->query("SELECT * FROM estado_solicitud ORDER BY nombre_estado")->fetch_all(MYSQLI_ASSOC);

$editar = null;
if (isset($_GET["editar"])) {
    $st = $cx->prepare("SELECT * FROM estado_solicitud WHERE cod_estado = ?");
    $st->bind_param("s", $_GET["editar"]);
    $st->execute();
    $editar = $st->get_result()->fetch_assoc();
}
?>

<div class="page-header">
    <h2>Estados de solicitud</h2>
</div>

<?php if ($mensaje): ?>
    <div class="alert alert-success"><?php echo $mensaje; ?></div>
<?php endif; ?>
<?php if (!empty($errores)): ?>
    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errores as $e) echo "<li>$e</li>"; ?></ul></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>Código</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($estados)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay estados.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($estados as $e): ?>
                        <tr>
                            <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);"><?php echo htmlspecialchars($e["cod_estado"]); ?></span></td>
                            <td><?php echo htmlspecialchars($e["nombre_estado"]); ?></td>
                            <td style="font-size:0.83rem;color:#888;"><?php echo htmlspecialchars($e["descripcion"] ?? ""); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="?editar=<?php echo urlencode($e["cod_estado"]); ?>"
                                       class="btn btn-sm"
                                       style="background:var(--lavender);border:1px solid var(--tea);color:var(--verde-dark);"
                                       title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este estado?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="cod_estado" value="<?php echo htmlspecialchars($e["cod_estado"]); ?>">
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

    <div class="col-md-5">
        <div class="card p-4">
            <h5 class="mb-3" style="color:var(--verde-dark);border-bottom:2px solid var(--tea);padding-bottom:10px;">
                <?php echo $editar ? "Editar estado" : "Nuevo estado"; ?>
            </h5>
            <form method="post">
                <input type="hidden" name="accion" value="<?php echo $editar ? 'editar' : 'crear'; ?>">
                <?php if ($editar): ?>
                    <input type="hidden" name="cod_orig" value="<?php echo htmlspecialchars($editar["cod_estado"]); ?>">
                <?php endif; ?>

                <?php if (!$editar): ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="cod_estado" class="form-control" maxlength="10" placeholder="Ej: CANC" required>
                </div>
                <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($editar["cod_estado"]); ?>" disabled>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre_estado" class="form-control" maxlength="50"
                           placeholder="Ej: CANCELADA"
                           value="<?php echo $editar ? htmlspecialchars($editar["nombre_estado"]) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción <small class="text-muted">(opcional)</small></label>
                    <input type="text" name="descripcion" class="form-control" maxlength="255"
                           placeholder="Descripción del estado..."
                           value="<?php echo $editar ? htmlspecialchars($editar["descripcion"] ?? '') : ''; ?>">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editar ? "Guardar cambios" : "Crear estado"; ?>
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
