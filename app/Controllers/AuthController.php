<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Helpers\Security;
class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $this->viewAuth('auth.login', ['title' => 'Iniciar Sesión']);
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
            Session::setFlash('error', 'Credenciales inválidas');
            $this->redirect('login');
        }
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('role', $user['role']);
        Session::setFlash('success', 'Bienvenido ' . $user['username']);
        $this->redirectBasedOnRole();
    }
    public function showRegisterForm()
    {
        if (Session::isLoggedIn()) $this->redirectBasedOnRole();
        $this->viewAuth('auth.register', ['title' => 'Registro']);
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
        Session::setFlash('success', 'Registro exitoso. Ya puedes iniciar sesión.');
        $this->redirect('login');
    }
    public function logout()
    {
        Session::destroy();
        session_start();
        Session::setFlash('success', 'Sesión cerrada');
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
