<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index()
    {
        $action = $_GET['action'] ?? '';
        $username = $_GET['username'] ?? '';
        $role = $_GET['role'] ?? '';
        $from = $_GET['from'] ?? '';
        $to = $_GET['to'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $params = array_filter(compact('action', 'username', 'role', 'from', 'to'));
        $logs = AuditLog::search($params, $limit, $offset);
        $total = AuditLog::count($params);
        $totalPages = ceil($total / $limit);
        $actions = AuditLog::distinctActions();

        $this->view('audit.index', [
            'title' => 'Auditoría',
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'actions' => $actions,
            'filters' => $params,
        ]);
    }
}
