<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Models\AuditLog;
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
        unset($data['_token']);
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
    
    public function profile()
    {
        $usuario = User::find(Session::userId());
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado');
            $this->redirect('login');
        }
        $this->view('users.profile', ['title' => 'Mi Perfil', 'usuario' => $usuario]);
    }

    public function editProfile()
    {
        $usuario = User::find(Session::userId());
        if (!$usuario) {
            Session::setFlash('error', 'Usuario no encontrado');
            $this->redirect('login');
        }
        $this->view('users.edit_profile', ['title' => 'Editar Perfil', 'usuario' => $usuario]);
    }
    
    public function updateProfile()
    {
        header('Content-Type: application/json');
        
        $userId = Session::userId();
        $response = ['success' => false, 'message' => 'Error al actualizar perfil'];
        
        // Validación básica
        $fullName = $_POST['full_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $bio = $_POST['bio'] ?? '';
        
        if (empty($fullName) || empty($email)) {
            $response['message'] = 'Nombre y email son obligatorios';
            echo json_encode($response);
            exit;
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'El email no es válido';
            echo json_encode($response);
            exit;
        }
        
        // Verificar email duplicado
        $existing = \App\Core\Database::fetchOne(
            "SELECT id FROM usuarios WHERE email=? AND id!=?", 
            [$email, $userId]
        );
        if ($existing) {
            $response['message'] = 'Este email ya está registrado';
            echo json_encode($response);
            exit;
        }
        
        // Manejo de foto de perfil
        $photoFilename = null;
        if (!empty($_FILES['profile_photo']['name'])) {
            $upload = \App\Helpers\Security::validateUpload($_FILES['profile_photo'], ['image/jpeg', 'image/png', 'image/gif']);
            if (!$upload['valid']) {
                $response['message'] = $upload['error'];
                echo json_encode($response);
                exit;
            }
            
            $user = User::find($userId);
            if (!empty($user['profile_photo']) && file_exists(__DIR__ . '/../../public/uploads/profiles/' . $user['profile_photo'])) {
                unlink(__DIR__ . '/../../public/uploads/profiles/' . $user['profile_photo']);
            }
            
            $photoFilename = 'profile_' . $userId . '_' . time() . '.' . pathinfo($upload['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['profile_photo']['tmp_name'], __DIR__ . '/../../public/uploads/profiles/' . $photoFilename);
        }
        
        // Preparar datos para actualizar
        $updateData = [
            'fullname' => $fullName,
            'email' => $email,
            'bio' => $bio,
        ];
        
        if ($photoFilename) {
            $updateData['profile_photo'] = $photoFilename;
        }
        
        // Manejo de cambio de contraseña
        if (!empty($_POST['new_password'])) {
            if (empty($_POST['current_password'])) {
                $response['message'] = 'Debes ingresar tu contraseña actual';
                echo json_encode($response);
                exit;
            }
            
            // Verificar contraseña actual
            $user = User::find($userId);
            if (!password_verify($_POST['current_password'], $user['password'])) {
                $response['message'] = 'La contraseña actual es incorrecta';
                echo json_encode($response);
                exit;
            }
            
            // Validar nueva contraseña
            $newPassword = $_POST['new_password'];
            if (strlen($newPassword) < 8) {
                $response['message'] = 'La contraseña debe tener al menos 8 caracteres';
                echo json_encode($response);
                exit;
            }
            
            if ($_POST['new_password'] !== $_POST['confirm_password']) {
                $response['message'] = 'Las contraseñas no coinciden';
                echo json_encode($response);
                exit;
            }
            
            $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }
        
        // Actualizar usuario
        try {
            User::updateUser($userId, $updateData);
            
            // Actualizar sesión con nuevos datos
            Session::setUser($userId);
            
            AuditLog::log('Perfil actualizado', 'Usuario: ' . Session::username());
            $response['success'] = true;
            $response['message'] = 'Perfil actualizado correctamente';
        } catch (\Exception $e) {
            $response['message'] = 'Error al actualizar perfil: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit;
    }
    
    public function myCourses()
    {
        $userId = Session::userId();
        
        $enrolledCourses = \App\Core\Database::fetchAll(
            "SELECT c.*, m.status 
             FROM cursos c 
             INNER JOIN matriculas m ON c.id = m.curso_id 
             WHERE m.user_id = ? AND m.status = 'approved'
             ORDER BY m.created_at DESC",
            [$userId]
        );
        
        foreach ($enrolledCourses as &$course) {
            $totalClasses = (int)\App\Core\Database::fetchOne(
                "SELECT COUNT(*) as cnt FROM clases cls
                 JOIN modulos md ON cls.modulo_id = md.id
                 WHERE md.curso_id = ?", [$course['id']]
            )['cnt'];
            
            $completedClasses = \App\Models\CompletedClass::countByUserAndCourse($userId, $course['id']);
            
            $course['progress'] = $totalClasses > 0 ? round(($completedClasses / $totalClasses) * 100) : 0;
            $course['total_classes'] = $totalClasses;
            $course['completed_classes'] = $completedClasses;
        }
        unset($course);
        
        $approvedCount = \App\Core\Database::fetchOne(
            "SELECT COUNT(*) as count FROM matriculas WHERE user_id = ? AND status = 'approved'",
            [$userId]
        );
        
        $this->view('users.my_courses', [
            'title' => 'Mis Cursos',
            'enrolledCourses' => $enrolledCourses,
            'approvedCount' => $approvedCount['count'] ?? 0
        ]);
    }
    
    public function settings()
    {
        $user = User::find(Session::userId());
        $this->view('users.settings', [
            'title' => 'Configuración',
            'user' => $user,
        ]);
    }

    public function saveNotifPrefs()
    {
        header('Content-Type: application/json');
        $field = $_POST['field'] ?? '';
        $value = !empty($_POST['value']) ? 1 : 0;
        if (!in_array($field, ['email_notifications', 'newsletter'])) {
            echo json_encode(['success' => false, 'error' => 'Campo inválido']);
            exit;
        }
        User::updateUser(Session::userId(), [$field => $value]);
        echo json_encode(['success' => true]);
        exit;
    }
    
    public function uploadProfilePhoto()
    {
        header('Content-Type: application/json');
        $userId = Session::userId();
        
        if (empty($_FILES['profile_photo']['name'])) {
            echo json_encode(['success' => false, 'error' => 'Por favor selecciona una foto']);
            exit;
        }
        
        $upload = \App\Helpers\Security::validateUpload($_FILES['profile_photo'], ['image/jpeg', 'image/png', 'image/gif']);
        if (!$upload['valid']) {
            echo json_encode(['success' => false, 'error' => $upload['error']]);
            exit;
        }
        
        $user = User::find($userId);
        if (!empty($user['profile_photo']) && file_exists(__DIR__ . '/../../public/uploads/profiles/' . $user['profile_photo'])) {
            unlink(__DIR__ . '/../../public/uploads/profiles/' . $user['profile_photo']);
        }
        
        $fileName = 'profile_' . $userId . '_' . time() . '.' . pathinfo($upload['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], __DIR__ . '/../../public/uploads/profiles/' . $fileName);
        
        User::updateUser($userId, ['profile_photo' => $fileName]);
        Session::setUser($userId);
        AuditLog::log('Foto de perfil cambiada', 'Usuario: ' . Session::username());
        echo json_encode(['success' => true, 'message' => 'Foto actualizada']);
        exit;
    }
}
