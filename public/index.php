<?php
require_once __DIR__ . '/../app/autoload.php';
use App\Core\Router;
use App\Core\Session;
Session::start();
$router = new Router();
// Auth
$router->get('login', 'AuthController@showLoginForm');
$router->post('login', 'AuthController@login');
$router->get('register', 'AuthController@showRegisterForm');
$router->post('register', 'AuthController@register');
$router->get('logout', 'AuthController@logout');
// Admin Dashboard
$router->get('admin', 'DashboardController@index', ['auth','role:admin']);
$router->get('/', 'DashboardController@index', ['auth','role:admin']);
// Products
$router->get('productos', 'ProductController@index', ['auth','role:admin']);
$router->get('productos/crear', 'ProductController@create', ['auth','role:admin']);
$router->post('productos/guardar', 'ProductController@store', ['auth','role:admin']);
$router->get('productos/editar/{id}', 'ProductController@edit', ['auth','role:admin']);
$router->post('productos/actualizar/{id}', 'ProductController@update', ['auth','role:admin']);
$router->post('productos/eliminar/{id}', 'ProductController@destroy', ['auth','role:admin']);
// Shop / Cart
$router->get('tienda', 'CartController@shop', ['auth']);
$router->post('api/carrito/checkout', 'CartController@checkout', ['auth','role:client']);
$router->get('mis-compras', 'CartController@myPurchases', ['auth','role:client']);
$router->post('mis-compras/subir-pago', 'CartController@uploadPayment', ['auth','role:client']);
// Admin Purchases
$router->get('admin/compras', 'PurchaseController@index', ['auth','role:admin']);
$router->post('admin/compras/aprobar/{id}', 'PurchaseController@approve', ['auth','role:admin']);
$router->post('admin/compras/rechazar/{id}', 'PurchaseController@reject', ['auth','role:admin']);
// Finance / Movements
$router->get('movimientos', 'MovementController@index', ['auth','role:admin,worker']);
$router->post('movimientos/crear', 'MovementController@store', ['auth','role:admin,worker']);
$router->post('movimientos/aprobar/{id}', 'MovementController@approve', ['auth','role:admin']);
$router->post('movimientos/rechazar/{id}', 'MovementController@reject', ['auth','role:admin']);
$router->post('movimientos/eliminar/{id}', 'MovementController@destroy', ['auth','role:admin']);
$router->get('movimientos/exportar', 'MovementController@export', ['auth','role:admin,worker']);
$router->get('api/finance-data', 'MovementController@chartData', ['auth','role:admin']);
// Users
$router->get('usuarios', 'UserController@index', ['auth','role:admin']);
$router->get('usuarios/crear', 'UserController@create', ['auth','role:admin']);
$router->post('usuarios/guardar', 'UserController@store', ['auth','role:admin']);
$router->get('usuarios/editar/{id}', 'UserController@edit', ['auth','role:admin']);
$router->post('usuarios/actualizar/{id}', 'UserController@update', ['auth','role:admin']);
$router->post('usuarios/eliminar/{id}', 'UserController@destroy', ['auth','role:admin']);
// Manual
$router->get('manual', 'PageController@manual', ['auth']);
// Invoice PDF
$router->get('factura/{id}', 'InvoiceController@download', ['auth','role:admin,client']);
$router->dispatch();
