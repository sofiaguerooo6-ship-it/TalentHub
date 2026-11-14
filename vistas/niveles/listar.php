<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Nivel_jerarquico_controlador.php';
require_once '../../includes/header.php';

$controlador = new Nivel_jerarquico_controlador();
$resultado = $controlador->listar();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Niveles Jerárquicos</h2>
    <a href="crear.php" class="btn btn-primary">+ Nuevo nivel</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $fila["cod_nivel"]; ?></td>
                    <td><?php echo $fila["nombre_nivel"]; ?></td>
                    <td><?php echo $fila["descripcion"]; ?></td>
                    <td>
                        <a href="editar.php?cod=<?php echo $fila["cod_nivel"]; ?>" class="btn btn-sm btn-warning">Editar</a>
                        <a href="eliminar.php?cod=<?php echo $fila["cod_nivel"]; ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que querés eliminar este nivel?')">Eliminar</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>