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
$router->get('inscripcion/{curso_id}', 'CartController@showEnrollmentForm', ['auth','role:client']);
$router->post('inscripcion/procesar', 'CartController@processEnrollment', ['auth','role:client']);
$router->get('mis-matriculas', 'CartController@myEnrollments', ['auth','role:client']);
$router->post('mis-matriculas/subir-pago', 'CartController@uploadPayment', ['auth','role:client']);
// Admin Enrollments
$router->get('admin/enrollments', 'EnrollmentController@pending', ['auth','role:admin']);
$router->post('admin/enrollments/aprobar/{id}', 'EnrollmentController@approve', ['auth','role:admin']);
$router->post('admin/enrollments/rechazar/{id}', 'EnrollmentController@reject', ['auth','role:admin']);
$router->get('admin/enrollments/todas', 'EnrollmentController@all', ['auth','role:admin']);
$router->post('admin/enrollments/cancelar/{id}', 'EnrollmentController@cancel', ['auth','role:admin']);
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
// User Profile
$router->get('perfil', 'UserController@profile', ['auth']);
$router->get('perfil/editar', 'UserController@editProfile', ['auth']);
$router->post('perfil/actualizar', 'UserController@updateProfile', ['auth']);
$router->post('perfil/foto', 'UserController@uploadProfilePhoto', ['auth']);
$router->get('mis-cursos', 'UserController@myCourses', ['auth','role:client']);
$router->get('configuracion', 'UserController@settings', ['auth']);
$router->post('api/configuracion/notificaciones', 'UserController@saveNotifPrefs', ['auth']);
// Manual
$router->get('manual', 'PageController@manual', ['auth']);
// Notifications
$router->get('api/notificaciones', 'PageController@notifications', ['auth']);
$router->post('api/notificaciones/leer/{id}', 'PageController@markRead', ['auth']);
$router->post('api/notificaciones/leer-todas', 'PageController@markAllRead', ['auth']);
$router->post('api/notificaciones/eliminar/{id}', 'PageController@deleteNotif', ['auth']);
// Invoice PDF
$router->get('factura/{id}', 'InvoiceController@download', ['auth','role:admin,client']);
$router->get('factura/{id}/html', 'InvoiceController@preview', ['auth','role:admin,client']);
// Contact
$router->get('contacto', 'ContactController@showForm');
$router->post('contacto/enviar', 'ContactController@send');
$router->get('contactos', 'ContactController@index', ['auth','role:admin']);
$router->post('contactos/eliminar/{id}', 'ContactController@destroy', ['auth','role:admin']);
// Auditoria
$router->get('auditoria', 'AuditController@index', ['auth','role:admin']);
// Password recovery
$router->get('recuperar-contrasena', 'AuthController@showForgotForm');
$router->post('recuperar-contrasena', 'AuthController@sendReset');
$router->get('restablecer-contrasena/{token}', 'AuthController@showResetForm');
$router->post('restablecer-contrasena/{token}', 'AuthController@doReset');
$router->post('api/usuarios/reset-password/{id}', 'AuthController@adminResetUser', ['auth','role:admin']);
// Course Content - Student
$router->get('curso/{id}', 'CourseViewController@show', ['auth','role:client']);
$router->get('curso/{id}/clase/{claseId}', 'CourseViewController@classView', ['auth','role:client']);
$router->post('api/curso/{id}/clase/{claseId}/completar', 'CourseViewController@completeClass', ['auth','role:client']);
// Exams - Student
$router->get('curso/{id}/examen/{examenId}', 'ExamController@take', ['auth','role:client']);
$router->post('curso/{id}/examen/{examenId}/enviar', 'ExamController@submit', ['auth','role:client']);
$router->get('curso/{id}/examen/{examenId}/resultado/{intentoId}', 'ExamController@result', ['auth','role:client']);
// Course Content - Admin
$router->get('cursos/{id}/contenido', 'CourseController@manageContent', ['auth','role:admin']);
$router->post('api/cursos/{id}/modulos/guardar', 'CourseController@storeModule', ['auth','role:admin']);
$router->post('api/cursos/{id}/modulos/actualizar/{modId}', 'CourseController@updateModule', ['auth','role:admin']);
$router->post('api/cursos/{id}/modulos/eliminar/{modId}', 'CourseController@deleteModule', ['auth','role:admin']);
$router->post('api/cursos/{id}/clases/guardar', 'CourseController@storeClass', ['auth','role:admin']);
$router->post('api/cursos/{id}/clases/actualizar/{claseId}', 'CourseController@updateClass', ['auth','role:admin']);
$router->post('api/cursos/{id}/clases/eliminar/{claseId}', 'CourseController@deleteClass', ['auth','role:admin']);
$router->post('api/cursos/{id}/examenes/guardar', 'CourseController@storeExam', ['auth','role:admin']);
$router->post('api/cursos/{id}/examenes/actualizar/{examenId}', 'CourseController@updateExam', ['auth','role:admin']);
$router->post('api/examenes/{id}/preguntas/guardar', 'CourseController@storeQuestion', ['auth','role:admin']);
$router->post('api/preguntas/eliminar/{pqId}', 'CourseController@deleteQuestion', ['auth','role:admin']);
// Admin Progress
$router->get('admin/progreso', 'CourseController@progress', ['auth','role:admin']);
// Default home - redirect based on auth status
$router->get('', 'AuthController@redirectHome');
$router->dispatch();
