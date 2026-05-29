<?php
require_once 'config.php';
session_destroy();
session_start();
set_flash_message('success', 'Sesión cerrada correctamente.');
header('Location: login.php');
exit;
?>
