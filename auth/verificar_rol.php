<?php
function verificarRol($roles_permitidos) {
    if (!isset($_SESSION["rol"])) {
        header("Location: /talenthub2/auth/login.php");
        exit;
    }
    if (!in_array($_SESSION["rol"], $roles_permitidos)) {
        header("Location: /talenthub2/acceso_denegado.php");
        exit;
    }
}
?>