<?php
use App\Core\Session;
use App\Helpers\Security;
function e($v) { return Security::sanitize($v); }
function csrf_field() { return Security::csrfField(); }
function csrf_token() { return Security::generateCsrf(); }
function flash($k) { return Session::getFlash($k); }
function format_cop($a) { return '$' . number_format((float)$a, 0, ',', '.'); }
function format_date($d) { return date('d/m/Y', strtotime($d)); }
function format_datetime($d) { return date('d/m/Y H:i', strtotime($d)); }
function asset($p) { return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/' . ltrim($p, '/'); }
function url($p = '') {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    if ($p !== '') $base .= '/index.php';
    return $base . '/' . ltrim($p, '/');
}
function old($k, $d = '') { return $_POST[$k] ?? $d; }
function selected($v, $c) { return $v == $c ? 'selected' : ''; }
function truncate($t, $l = 100) { if (mb_strlen($t) <= $l) return $t; return mb_substr($t, 0, $l) . '...'; }
function stockBadge($s) {
    if ($s > 10) return '<span class="badge bg-success">' . $s . ' disp.</span>';
    if ($s > 0) return '<span class="badge bg-warning text-dark">' . $s . ' disp.</span>';
    return '<span class="badge bg-danger">Agotado</span>';
}
function statusBadge($s) {
    $map = ['pending'=>'bg-warning text-dark','approved'=>'bg-success','rejected'=>'bg-danger'];
    $l = ['pending'=>'Pendiente','approved'=>'Aprobado','rejected'=>'Rechazado'];
    $c = $map[$s] ?? 'bg-secondary';
    return '<span class="badge ' . $c . '">' . ($l[$s] ?? $s) . '</span>';
}
