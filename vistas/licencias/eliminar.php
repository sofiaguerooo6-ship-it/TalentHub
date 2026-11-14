<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Solicitud_licencia_controlador.php';

if (isset($_GET["nro"])) {
    $controlador = new Solicitud_licencia_controlador();
    $controlador->eliminar($_GET["nro"]);
}

header("Location: listar.php");
exit;
?>