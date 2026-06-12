<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Models\PasswordReset;
use App\Helpers\Security;
class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $cursos = Course::available();
        $categorias = Category::withCourseCount();
        $this->viewAuth('auth.login', ['title' => 'Iniciar Sesión', 'cursos' => $cursos, 'categorias' => $categorias]);
    }
    public function login()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!Security::rateLimit('login_' . $_SERVER['REMOTE_ADDR'], 5, 900)) {
            Session::setFlash('error', 'Demasiados intentos. Espere 15 minutos.');
            $this->redirect('login');
        }
        $user = User::findByUsername($username);
        if (!$user || !Security::verifyPassword($password, $user['password_hash'])) {
            AuditLog::log('Inicio de sesión fallido', 'Intento fallido para usuario: ' . $username);
            Session::setFlash('error', 'Credenciales inválidas');
            $this->redirect('login');
        }
        Session::regenerate();
        Session::setUser($user['id']);
        AuditLog::log('Inicio de sesión', 'Usuario: ' . $user['username'] . ' (' . $user['role'] . ')');
        Session::setFlash('success', 'Bienvenido ' . $user['username']);
        $this->redirectBasedOnRole();
    }
    public function showRegisterForm()
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $cursos = Course::available();
        $categorias = Category::withCourseCount();
        $this->viewAuth('auth.register', ['title' => 'Registro', 'cursos' => $cursos, 'categorias' => $categorias]);
    }
    public function register()
    {
        $data = $_POST;
        unset($data['_token']);
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            Session::setFlash('error', 'Todos los campos son requeridos');
            $this->redirect('register');
        }
        if (strlen($data['password']) < 6) {
            Session::setFlash('error', 'La contraseña debe tener al menos 6 caracteres');
            $this->redirect('register');
        }
        if (User::findByUsername($data['username'])) {
            Session::setFlash('error', 'El usuario ya existe');
            $this->redirect('register');
        }
        if (User::findByEmail($data['email'])) {
            Session::setFlash('error', 'El email ya está registrado');
            $this->redirect('register');
        }
        $data['role'] = 'client';
        User::create($data);
        AuditLog::log('Registro de usuario', 'Nuevo usuario: ' . $data['username'] . ' (' . $data['email'] . ')');
        Notification::notifyAdmins('new_user', 'Nuevo usuario registrado', 'Se ha registrado: ' . $data['username'], 'usuarios');
        Session::setFlash('success', 'Registro exitoso. Ya puedes iniciar sesión.');
        $this->redirect('login');
    }
    public function logout()
    {
        AuditLog::log('Cierre de sesión', 'Usuario: ' . Session::username());
        Session::destroy();
        session_start();
        Session::setFlash('success', 'Sesión cerrada');
        $this->redirect('login');
    }

    public function showForgotForm()
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $cursos = Course::available();
        $categorias = Category::withCourseCount();
        $this->viewAuth('auth.forgot', ['title' => 'Recuperar Contraseña', 'cursos' => $cursos, 'categorias' => $categorias]);
    }

    public function sendReset()
    {
        $email = trim($_POST['email'] ?? '');
        $user = User::findByEmail($email);
        if (!$user) {
            Session::setFlash('error', 'No existe una cuenta con ese correo');
            $this->redirect('recuperar-contrasena');
        }
        $token = PasswordReset::createToken($user['id']);
        $resetUrl = url('restablecer-contrasena/' . $token);
        AuditLog::log('Solicitud de recuperación', 'Usuario: ' . $user['username'] . ' - Token generado');
        Session::setFlash('success', 'Tu enlace de recuperación: <a href="' . $resetUrl . '" class="alert-link">' . $resetUrl . '</a>');
        $this->redirect('recuperar-contrasena');
    }

    public function showResetForm($token)
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $reset = PasswordReset::findByToken($token);
        $cursos = Course::available();
        $categorias = Category::withCourseCount();
        $this->viewAuth('auth.reset', [
            'title' => 'Restablecer Contraseña',
            'token' => $token,
            'error' => $reset ? '' : 'El enlace es inválido o ha expirado.',
            'cursos' => $cursos,
            'categorias' => $categorias,
        ]);
    }

    public function doReset($token)
    {
        $reset = PasswordReset::findByToken($token);
        if (!$reset) {
            Session::setFlash('error', 'El enlace es inválido o ha expirado.');
            $this->redirect('login');
        }
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';
        if (strlen($password) < 8) {
            Session::setFlash('error', 'La contraseña debe tener al menos 8 caracteres');
            $this->redirect('restablecer-contrasena/' . $token);
        }
        if ($password !== $confirm) {
            Session::setFlash('error', 'Las contraseñas no coinciden');
            $this->redirect('restablecer-contrasena/' . $token);
        }
        User::updateUser($reset['user_id'], ['password_hash' => Security::hashPassword($password)]);
        PasswordReset::markUsed($token);
        AuditLog::log('Contraseña restablecida', 'Usuario ID: ' . $reset['user_id']);
        Session::setFlash('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
        $this->redirect('login');
    }

    public function adminResetUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            $this->json(['success' => false, 'error' => 'Usuario no encontrado']);
        }
        $token = PasswordReset::createToken($id);
        $resetUrl = url('restablecer-contrasena/' . $token);
        AuditLog::log('Reset de contraseña por admin', 'Admin reseteó contraseña de: ' . $user['username']);
        $this->json(['success' => true, 'link' => $resetUrl]);
    }
    public function redirectHome()
    {
        if (Session::isLoggedIn()) {
            $this->redirectBasedOnRole();
        }
        $this->redirect('login');
    }
    private function redirectBasedOnRole()
    {
        $role = Session::userRole();
        if ($role === 'admin') $this->redirect('admin');
        elseif ($role === 'client') $this->redirect('catalogo');
        else $this->redirect('movimientos');
    }
}
