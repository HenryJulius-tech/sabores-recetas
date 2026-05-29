<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
        $this->view('users.index', ['title' => 'Usuarios', 'usuarios' => $usuarios]);
    }
    public function create()
    {
        $this->view('users.form', ['title' => 'Nuevo Usuario', 'usuario' => null]);
    }
    public function store()
    {
        $data = $_POST;
        if (empty($data['username']) || empty($data['password'])) {
            Session::setFlash('error', 'Usuario y contraseña requeridos');
            $this->redirectBack();
        }
        if (User::findByUsername($data['username'])) {
            Session::setFlash('error', 'El usuario ya existe');
            $this->redirectBack();
        }
        User::create($data);
        Session::setFlash('success', 'Usuario creado');
        $this->redirect('usuarios');
    }
    public function edit($id)
    {
        $usuario = User::find($id);
        if (!$usuario) { Session::setFlash('error', 'Usuario no encontrado'); $this->redirect('usuarios'); }
        $this->view('users.form', ['title' => 'Editar Usuario', 'usuario' => $usuario]);
    }
    public function update($id)
    {
        $data = $_POST;
        unset($data['id']);
        if (empty($data['password'])) unset($data['password']);
        User::updateUser($id, $data);
        Session::setFlash('success', 'Usuario actualizado');
        $this->redirect('usuarios');
    }
    public function destroy($id)
    {
        if ($id == Session::userId()) { Session::setFlash('error', 'No puedes eliminarte a ti mismo'); $this->redirect('usuarios'); }
        $target = User::find($id);
        if ($target && $target['role'] === 'admin' && $target['id'] != Session::userId()) {
            $admins = User::where('role', 'admin');
            if (count($admins) <= 1) { Session::setFlash('error', 'Debe haber al menos un admin'); $this->redirect('usuarios'); }
        }
        try { User::delete($id); Session::setFlash('success', 'Usuario eliminado'); }
        catch (\Exception $e) { Session::setFlash('error', 'No se puede eliminar: tiene registros asociados'); }
        $this->redirect('usuarios');
    }
}
