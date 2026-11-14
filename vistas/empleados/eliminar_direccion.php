<?php
require_once '../../auth/verificar_sesion.php';
require_once '../../auth/verificar_rol.php';
verificarRol(['ADMINISTRADOR']);
require_once '../../controladores/Empleado_direccion_controlador.php';

if (isset($_GET["id"]) && isset($_GET["legajo"])) {
    $controlador = new Empleado_direccion_controlador();
    $controlador->eliminar($_GET["id"]);
    $destino = ($_GET["redir"] ?? "") == "crear"
        ? "crear.php?legajo=" . $_GET["legajo"]
        : "editar.php?legajo=" . $_GET["legajo"];
    header("Location: " . $destino);
    exit;
}

header("Location: listar.php");
exit;
?>