<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Solicitud_licencia_controlador.php';
require_once '../../includes/header.php';

$controlador = new Solicitud_licencia_controlador();
$datos = [];

if (!isset($_GET["nro"])) {
    header("Location: listar.php");
    exit;
}

$nro = $_GET["nro"];

$resultado = $controlador->buscar($nro);
$datos = $resultado->fetch_assoc();
$documentos = $controlador->listarDocumentos($nro);
?>

<div class="page-header">
    <h2>Detalle de Solicitud #<?php echo $nro; ?></h2>
    <a href="listar.php" class="btn btn-secondary">← Volver</a>
</div>

<?php if (!empty($datos)) { ?>

<div class="row g-4">

    <!-- Detalle solicitud -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3" style="color: var(--verde-dark); border-bottom: 2px solid var(--tea); padding-bottom:10px;">
                Información de la solicitud
            </h5>
            <table class="table table-borderless mb-0" style="font-size:0.9rem;">
                <tr>
                    <td class="fw-bold" style="color:#666; width:45%;">Nro solicitud</td>
                    <td>#<?php echo $datos["nro_solicitud"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Legajo empleado</td>
                    <td><?php echo $datos["legajo"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Tipo de licencia</td>
                    <td><?php echo $datos["cod_tipo"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Estado</td>
                    <td>
                        <?php
                        $estado = $datos["cod_estado"];
                        $badge = "secondary";
                        if ($estado == "PEN") $badge = "warning";
                        elseif ($estado == "APR") $badge = "success";
                        elseif ($estado == "REC") $badge = "danger";
                        elseif ($estado == "CAN") $badge = "dark";
                        ?>
                        <span class="badge bg-<?php echo $badge; ?>"><?php echo $estado; ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Fecha solicitud</td>
                    <td><?php echo $datos["fecha_solicitud"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Fecha inicio</td>
                    <td><?php echo $datos["fecha_inicio"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Fecha fin</td>
                    <td><?php echo $datos["fecha_fin"]; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Días solicitados</td>
                    <td><?php echo (strtotime($datos["fecha_fin"]) - strtotime($datos["fecha_inicio"])) / 86400; ?></td>
                </tr>
                <tr>
                    <td class="fw-bold" style="color:#666;">Observación</td>
                    <td><?php echo $datos["observacion"] ?? '-'; ?></td>
                </tr>
            </table>
            <?php if ($_SESSION["rol"] != "EMPLEADO") { ?>
                <div class="mt-3">
                    <a href="cambiar_estado.php?nro=<?php echo $datos["nro_solicitud"]; ?>" class="btn btn-warning btn-sm">
                        Cambiar estado
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Documentos adjuntos -->
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3" style="color: var(--verde-dark); border-bottom: 2px solid var(--tea); padding-bottom:10px;">
                Documentos adjuntos
            </h5>

            <!-- Listado de documentos -->
            <?php
            $docs_array = [];
            while ($doc = $documentos->fetch_assoc()) {
                $docs_array[] = $doc;
            }
            ?>

            <?php if (!empty($docs_array)) { ?>
                <table class="table table-sm mb-3" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($docs_array as $doc) { ?>
                        <tr>
                            <td><?php echo $doc["tipo"]; ?></td>
                            <td><?php echo $doc["nombre_archivo"]; ?></td>
                            <td><?php echo $doc["fecha_carga"]; ?></td>
                            <td>
                                <a href="eliminar_documento.php?id=<?php echo $doc["id_doc"]; ?>&nro=<?php echo $nro; ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('¿Eliminar este documento?')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="text-muted" style="font-size:0.88rem;">No hay documentos adjuntos.</p>
            <?php } ?>

        </div>
    </div>

</div>

<?php } else { ?>
    <div class="alert alert-danger">Solicitud no encontrada.</div>
<?php } ?>

<?php require_once '../../includes/footer.php'; ?>