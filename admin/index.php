<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../config/config.php';
header('Location: ' . BASE_URL . 'admin/dashboard.php');
exit;
