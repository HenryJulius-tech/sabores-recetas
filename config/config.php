<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = in_array($host, ['localhost', '127.0.0.1', '::1']) || strpos($host, '.test') !== false;
$projectDir = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
if ($docRoot) {
    $docRoot = rtrim($docRoot, '/');
    $basePath = str_ireplace($docRoot, '', $projectDir);
} else {
    $basePath = '/' . basename($projectDir);
}
if (empty($basePath) || $basePath[0] !== '/') {
    $basePath = '/' . ltrim($basePath, '/');
}
define('BASE_URL', ($isLocal ? 'http' : 'https') . '://' . $host . rtrim($basePath, '/') . '/');
define('IS_LOCAL', $isLocal);
define('ROOT', __DIR__ . '/..');
