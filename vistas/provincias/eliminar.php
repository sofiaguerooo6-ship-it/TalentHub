<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Provincia_controlador.php';

if (isset($_GET["cod"])) {
    $controlador = new Provincia_controlador();
    $controlador->eliminar($_GET["cod"]);
}

header("Location: listar.php");
exit;
?>