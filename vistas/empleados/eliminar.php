<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Empleado_controlador.php';

if (isset($_GET["legajo"])) {
    $controlador = new Empleado_controlador();
    $controlador->eliminar($_GET["legajo"]);
}

header("Location: listar.php");
exit;
?>