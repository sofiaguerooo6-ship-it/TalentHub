<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../controladores/Evaluacion_desempeno_controlador.php';

if (isset($_GET["id"])) {
    $controlador = new Evaluacion_desempeno_controlador();
    $controlador->eliminar($_GET["id"]);
}

header("Location: listar.php");
exit;
?>