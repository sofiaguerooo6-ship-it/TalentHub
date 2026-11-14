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
        $cod    = trim($_POST["cod_tipo_doc"]);
        $nombre = trim($_POST["nombre"]);
        if ($cod === "" || $nombre === "") {
            $errores[] = "Completá todos los campos.";
        } else {
            $st = $cx->prepare("INSERT INTO tipo_documento (cod_tipo_doc, nombre) VALUES (?,?)");
            $st->bind_param("ss", $cod, $nombre);
            $st->execute() ? $mensaje = "Creado correctamente." : $errores[] = "Error. El código ya puede existir.";
        }

    } elseif ($accion === "editar") {
        $cod_orig = $_POST["cod_orig"];
        $nombre   = trim($_POST["nombre"]);
        if ($nombre === "") {
            $errores[] = "El nombre es obligatorio.";
        } else {
            $st = $cx->prepare("UPDATE tipo_documento SET nombre=? WHERE cod_tipo_doc=?");
            $st->bind_param("ss", $nombre, $cod_orig);
            $st->execute() ? $mensaje = "Actualizado correctamente." : $errores[] = "Error al actualizar.";
        }

    } elseif ($accion === "eliminar") {
        $cod = $_POST["cod_tipo_doc"];
        $stChk = $cx->prepare("SELECT COUNT(*) AS total FROM documento_adjunto WHERE cod_tipo_doc = ?");
        $stChk->bind_param("s", $cod);
        $stChk->execute();
        $uso = $stChk->get_result()->fetch_assoc()["total"];
        if ($uso > 0) {
            $errores[] = "No se puede eliminar: hay $uso documento(s) de este tipo.";
        } else {
            $st = $cx->prepare("DELETE FROM tipo_documento WHERE cod_tipo_doc = ?");
            $st->bind_param("s", $cod);
            $st->execute() ? $mensaje = "Eliminado correctamente." : $errores[] = "Error al eliminar.";
        }
    }
}

$tipos = $cx->query("SELECT * FROM tipo_documento ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

$editar = null;
if (isset($_GET["editar"])) {
    $st = $cx->prepare("SELECT * FROM tipo_documento WHERE cod_tipo_doc = ?");
    $st->bind_param("s", $_GET["editar"]);
    $st->execute();
    $editar = $st->get_result()->fetch_assoc();
}
?>

<div class="page-header">
    <h2>Tipos de documento</h2>
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
                        <tr><th>Código</th><th>Nombre</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tipos)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-3">No hay tipos de documento.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($tipos as $t): ?>
                        <tr>
                            <td><span class="badge" style="background:var(--lavender);color:var(--verde-dark);"><?php echo htmlspecialchars($t["cod_tipo_doc"]); ?></span></td>
                            <td><?php echo htmlspecialchars($t["nombre"]); ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="?editar=<?php echo urlencode($t["cod_tipo_doc"]); ?>"
                                       class="btn btn-sm"
                                       style="background:var(--lavender);border:1px solid var(--tea);color:var(--verde-dark);"
                                       title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="post" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar <?php echo htmlspecialchars($t["nombre"]); ?>?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="cod_tipo_doc" value="<?php echo htmlspecialchars($t["cod_tipo_doc"]); ?>">
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
                <?php echo $editar ? "Editar tipo" : "Nuevo tipo de documento"; ?>
            </h5>
            <form method="post">
                <input type="hidden" name="accion" value="<?php echo $editar ? 'editar' : 'crear'; ?>">
                <?php if ($editar): ?>
                    <input type="hidden" name="cod_orig" value="<?php echo htmlspecialchars($editar["cod_tipo_doc"]); ?>">
                <?php endif; ?>

                <?php if (!$editar): ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" name="cod_tipo_doc" class="form-control" maxlength="10" placeholder="Ej: CERT" required>
                </div>
                <?php else: ?>
                <div class="mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($editar["cod_tipo_doc"]); ?>" disabled>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" maxlength="100"
                           placeholder="Ej: Certificado médico"
                           value="<?php echo $editar ? htmlspecialchars($editar["nombre"]) : ''; ?>" required>
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
