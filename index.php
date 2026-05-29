<?php
$uri = $_SERVER['PATH_INFO'] ?? $_SERVER['ORIG_PATH_INFO'] ?? '';
if ($uri && $uri !== '/') {
    $_SERVER['REQUEST_URI'] = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $uri;
    $_GET['_url'] = ltrim($uri, '/');
} else {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = dirname($_SERVER['SCRIPT_NAME']);
    if (strpos($requestUri, $base . '/index.php') === 0) {
        $path = substr($requestUri, strlen($base . '/index.php'));
        if ($path === false) $path = '';
        $_SERVER['REQUEST_URI'] = $base . $path;
    }
}
require __DIR__ . '/public/index.php';
