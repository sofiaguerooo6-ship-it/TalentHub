<?php
require_once 'auth/verificar_sesion.php';
require_once 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6 text-center">
        <div class="card shadow-sm border-danger">
            <div class="card-body p-5">
                <h1 class="text-danger mb-3">⛔ Acceso Denegado</h1>
                <p class="text-muted">No tenés permisos para acceder a esta sección.</p>
                <p>Tu rol es: <span class="badge bg-secondary fs-6"><?php echo $_SESSION["rol"]; ?></span></p>
                <a href="/talenthub2/index.php" class="btn btn-primary mt-3">← Volver al inicio</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>