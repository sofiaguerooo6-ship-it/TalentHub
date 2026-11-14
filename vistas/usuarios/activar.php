<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Usuario_controlador.php';

if (isset($_GET["id"])) {
    $controlador = new Usuario_controlador();
    $controlador->cambiarEstado($_GET["id"], 1, NULL);
}

header("Location: listar.php");
exit;
?>