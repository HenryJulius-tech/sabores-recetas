<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\Notification;
class PageController extends Controller
{
    public function manual()
    {
        $role = Session::userRole();
        $this->view('manual', ['title' => 'Manual de Usuario', 'role' => $role]);
    }

    public function notifications()
    {
        $userId = Session::userId();
        $count = Notification::unreadCount($userId);
        $list = Notification::recent($userId);
        $this->json(['count' => $count, 'list' => $list]);
    }

    public function markRead($id)
    {
        Notification::markRead($id, Session::userId());
        $this->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::markAllRead(Session::userId());
        $this->json(['success' => true]);
    }

    public function deleteNotif($id)
    {
        Notification::deleteById($id, Session::userId());
        $this->json(['success' => true]);
    }
}
