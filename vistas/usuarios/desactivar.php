<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Usuario_controlador.php';

if (isset($_GET["id"])) {
    $controlador = new Usuario_controlador();
    $fecha_baja = date("Y-m-d");
    $controlador->cambiarEstado($_GET["id"], 0, $fecha_baja);
}

header("Location: listar.php");
exit;
?>