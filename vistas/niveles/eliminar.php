<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Nivel_jerarquico_controlador.php';

if (isset($_GET["cod"])) {
    $controlador = new Nivel_jerarquico_controlador();
    $controlador->eliminar($_GET["cod"]);
}

header("Location: listar.php");
exit;
?>