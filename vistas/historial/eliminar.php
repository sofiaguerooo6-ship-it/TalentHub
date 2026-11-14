<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Historial_cargo_controlador.php';

if (isset($_GET["id"])) {
    $controlador = new Historial_cargo_controlador();
    $controlador->eliminar($_GET["id"]);
}

header("Location: listar.php");
exit;
?>