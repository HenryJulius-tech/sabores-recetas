<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\AuditLog;
class ContactController extends Controller
{
    public function showForm()
    {
        $this->view('contact', ['title' => 'Contactar Soporte']);
    }

    public function send()
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if (empty($name) || empty($email) || empty($message)) {
            $this->json(['success' => false, 'error' => 'Todos los campos son requeridos'], 400);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['success' => false, 'error' => 'Email inválido'], 400);
        }
        Contact::create([
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ]);
        AuditLog::log('Mensaje de contacto', 'De: ' . $name . ' (' . $email . ')');
        Notification::notifyAdmins('contact', 'Nuevo mensaje de contacto', $name . ' (' . $email . ') envió un mensaje', 'contactos');
        $this->json(['success' => true]);
    }

    public function index()
    {
        $mensajes = \App\Core\Database::fetchAll("SELECT * FROM contactos ORDER BY created_at DESC");
        $this->view('contacts.index', ['title' => 'Mensajes de Contacto', 'mensajes' => $mensajes]);
    }

    public function destroy($id)
    {
        Contact::delete((int)$id);
        Session::setFlash('success', 'Mensaje eliminado');
        $this->redirect('contactos');
    }
}
