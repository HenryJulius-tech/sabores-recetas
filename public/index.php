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
// Courses
$router->get('cursos', 'CourseController@index', ['auth','role:admin']);
$router->get('cursos/crear', 'CourseController@create', ['auth','role:admin']);
$router->post('cursos/guardar', 'CourseController@store', ['auth','role:admin']);
$router->get('cursos/editar/{id}', 'CourseController@edit', ['auth','role:admin']);
$router->post('cursos/actualizar/{id}', 'CourseController@update', ['auth','role:admin']);
$router->post('cursos/eliminar/{id}', 'CourseController@destroy', ['auth','role:admin']);
// Categories
$router->get('categorias', 'CategoryController@index', ['auth','role:admin']);
$router->post('categorias/guardar', 'CategoryController@store', ['auth','role:admin']);
$router->post('categorias/eliminar/{id}', 'CategoryController@destroy', ['auth','role:admin']);
// Catalog / Cart
$router->get('catalogo', 'CartController@shop', ['auth']);
$router->post('api/carrito/checkout', 'CartController@checkout', ['auth','role:client']);
$router->get('mis-matriculas', 'CartController@myEnrollments', ['auth','role:client']);
$router->post('mis-matriculas/subir-pago', 'CartController@uploadPayment', ['auth','role:client']);
// Admin Enrollments
$router->get('admin/enrollments', 'EnrollmentController@pending', ['auth','role:admin']);
$router->post('admin/enrollments/aprobar/{id}', 'EnrollmentController@approve', ['auth','role:admin']);
$router->post('admin/enrollments/rechazar/{id}', 'EnrollmentController@reject', ['auth','role:admin']);
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
