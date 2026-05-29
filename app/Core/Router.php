<?php
namespace App\Core;
class Router
{
    private $routes = [];
    public function get($route, $action, $middleware = []) { $this->addRoute('GET', $route, $action, $middleware); }
    public function post($route, $action, $middleware = []) { $this->addRoute('POST', $route, $action, $middleware); }
    private function addRoute($method, $route, $action, $middleware)
    {
        $route = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $route);
        $this->routes[] = ['method' => $method, 'route' => $route, 'pattern' => '#^' . $route . '$#', 'action' => $action, 'middleware' => $middleware];
    }
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = trim(str_replace(dirname($_SERVER['SCRIPT_NAME']), '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');
        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            $testUri = $r['route'] === '/' ? ($uri === '' ? '/' : null) : $uri;
            if ($testUri === null) continue;
            if (!preg_match($r['pattern'], $testUri, $matches)) continue;
            foreach ($r['middleware'] as $mw) {
                if (strpos($mw, 'role:') === 0) { Middleware::role(substr($mw, 5)); }
                elseif ($mw === 'auth') { Middleware::auth(); }
                elseif ($mw === 'csrf') { Middleware::csrf(); }
            }
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            list($ctrl, $meth) = explode('@', $r['action']);
            $class = 'App\\Controllers\\' . $ctrl;
            call_user_func_array([new $class(), $meth], $params);
            return;
        }
        http_response_code(404);
        echo '<h1>404</h1>';
    }
}
