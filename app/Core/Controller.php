<?php
namespace App\Core;
abstract class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) throw new \Exception("View '{$view}' not found");
        ob_start(); require $viewPath; $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/main.php';
    }
    protected function viewAuth($view, $data = [])
    {
        extract($data);
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) throw new \Exception("View '{$view}' not found");
        ob_start(); require $viewPath; $content = ob_get_clean();
        require __DIR__ . '/../Views/layouts/auth.php';
    }
    protected function json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    protected function redirect($url)
    {
        header('Location: ' . url($url));
        exit;
    }
    protected function redirectBack()
    {
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
    protected function isPost() { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
}
