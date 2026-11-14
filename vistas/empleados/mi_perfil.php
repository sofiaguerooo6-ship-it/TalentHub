<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../config/Conexion.php';
require_once '../../includes/header.php';

$conexion = Conexion::conectar();

// Obtener el legajo del usuario logueado
$stmt = $conexion->prepare("SELECT legajo FROM usuario WHERE id_usuario = ?");
$stmt->bind_param("i", $_SESSION["id_usuario"]);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$legajo = $row["legajo"];

if (!$legajo) {
    echo "<div class='alert alert-warning'>Tu cuenta no está vinculada a ningún empleado.</div>";
    require_once '../../includes/footer.php';
    exit;
}

// Datos personales
$stmt = $conexion->prepare("SELECT * FROM empleado WHERE legajo = ?");
$stmt->bind_param("i", $legajo);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();

// Cargo y departamento actual
$stmt = $conexion->prepare(
    "SELECT c.nombre_cargo, n.nombre_nivel, d.nombre_depto,
            h.fecha_inicio, h.id_hist
     FROM historial_cargo h
     JOIN cargo c ON h.cod_cargo = c.cod_cargo
     JOIN nivel_jerarquico n ON c.cod_nivel = n.cod_nivel
     JOIN departamento d ON h.cod_depto = d.cod_depto
     WHERE h.legajo = ? AND h.fecha_fin IS NULL
     LIMIT 1"
);
$stmt->bind_param("i", $legajo);
$stmt->execute();
$puesto_actual = $stmt->get_result()->fetch_assoc();

// Supervisores actuales
$supervisores = [];
if ($puesto_actual) {
    $stmt = $conexion->prepare(
        "SELECT CONCAT(e.nombre, ' ', e.apellido) AS supervisor
         FROM supervisor_historial sh
         JOIN empleado e ON sh.legajo_supervisor = e.legajo
         WHERE sh.id_hist = ?"
    );
    $stmt->bind_param("i", $puesto_actual["id_hist"]);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $supervisores[] = $fila["supervisor"];
    }
}

// Historial completo
$stmt = $conexion->prepare(
    "SELECT c.nombre_cargo, d.nombre_depto, h.fecha_inicio, h.fecha_fin
     FROM historial_cargo h
     JOIN cargo c ON h.cod_cargo = c.cod_cargo
     JOIN departamento d ON h.cod_depto = d.cod_depto
     WHERE h.legajo = ?
     ORDER BY h.fecha_inicio DESC"
);
$stmt->bind_param("i", $legajo);
$stmt->execute();
$historial = $stmt->get_result();
?>

<div class="page-header">
    <h2>Mi Perfil</h2>
</div>

<div class="row g-4">

    <!-- Datos personales -->
    <div class="col-md-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3" style="color: var(--verde-dark); border-bottom: 2px solid var(--tea); padding-bottom: 10px;">
                Datos personales
            </h5>
            <table class="table table-borderless mb-0" style="font-size: 0.9rem;">
                <tr>
                    <td class="fw-bold" style="color:#666; width:40%;">Legajo</td>
                    <td><?php echo $empleado["legajo"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">DNI</td>
                    <td><?php echo $empleado["dni"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Nombre</td>
                    <td><?php echo $empleado["nombre"] . " " . $empleado["apellido"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Email</td>
                    <td><?php echo $empleado["email"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Teléfono</td>
                    <td><?php echo $empleado["telefono"] ?? '-'; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Fecha ingreso</td>
                    <td><?php echo $empleado["fecha_ingreso"]; ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Puesto actual -->
    <div class="col-md-7">
        <div class="card p-4 h-100">
            <h5 class="mb-3" style="color: var(--verde-dark); border-bottom: 2px solid var(--tea); padding-bottom: 10px;">
                Puesto actual
            </h5>
            <?php if ($puesto_actual) { ?>
                <table class="table table-borderless mb-0" style="font-size: 0.9rem;">
                    <tr>
                        <td class="fw-bold" style="color:#666; width:40%;">Cargo</td>
                        <td><?php echo $puesto_actual["nombre_cargo"]; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color:#666;">Nivel</td>
                        <td><?php echo $puesto_actual["nombre_nivel"]; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color:#666;">Departamento</td>
                        <td><?php echo $puesto_actual["nombre_depto"]; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color:#666;">Desde</td>
                        <td><?php echo $puesto_actual["fecha_inicio"]; ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color:#666;">Supervisores</td>
                        <td>
                            <?php if (!empty($supervisores)) { ?>
                                <?php foreach ($supervisores as $sup) { ?>
                                    <span class="badge" style="background-color: var(--sage); color: white; margin-right:4px;">
                                        <?php echo htmlspecialchars($sup); ?>
                                    </span>
                                <?php } ?>
                            <?php } else { ?>
                                <span class="text-muted">Sin supervisor asignado</span>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <p class="text-muted">No tenés un puesto asignado actualmente.</p>
            <?php } ?>
        </div>
    </div>

    <!-- Historial de puestos -->
    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div style="padding: 16px 20px; border-bottom: 1px solid var(--tea);">
                    <h5 class="mb-0" style="color: var(--verde-dark);">Historial de puestos</h5>
                </div>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Cargo</th>
                            <th>Departamento</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $historial->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $fila["nombre_cargo"]; ?></td>
                            <td><?php echo $fila["nombre_depto"]; ?></td>
                            <td><?php echo $fila["fecha_inicio"]; ?></td>
                            <td>
                                <?php if (empty($fila["fecha_fin"])) { ?>
                                    <span class="badge bg-success">Actual</span>
                                <?php } else { ?>
                                    <?php echo $fila["fecha_fin"]; ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php require_once '../../includes/footer.php'; ?>