<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Solicitud_licencia_controlador.php';

if (isset($_GET["id"]) && isset($_GET["nro"])) {
    $controlador = new Solicitud_licencia_controlador();
    $controlador->eliminarDocumento($_GET["id"]);
    header("Location: ver.php?nro=" . $_GET["nro"]);
    exit;
}

header("Location: listar.php");
exit;
?>