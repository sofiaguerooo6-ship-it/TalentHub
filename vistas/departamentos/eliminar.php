<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Departamento_controlador.php';

if (isset($_GET["cod"])) {
    $controlador = new Departamento_controlador();
    $controlador->eliminar($_GET["cod"]);
}

header("Location: listar.php");
exit;
?>