<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Session;
class PageController extends Controller
{
    public function manual()
    {
        $role = Session::userRole();
        $this->view('manual', ['title' => 'Manual de Usuario', 'role' => $role]);
    }
}
