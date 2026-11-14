<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: /talenthub2/auth/login.php");
exit;
?>