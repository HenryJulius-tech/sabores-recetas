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
function levelBadge($l) {
    $map = ['principiante'=>'badge-status','intermedio'=>'badge-status pending','avanzado'=>'badge-status rejected'];
    $c = $map[$l] ?? 'badge-status';
    return '<span class="' . $c . '">' . ucfirst($l) . '</span>';
}
function statusBadge($s) {
    $map = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected'];
    $l = ['pending'=>'Pendiente','approved'=>'Aprobado','rejected'=>'Rechazado'];
    $c = $map[$s] ?? 'pending';
    return '<span class="badge-status ' . $c . '">' . ($l[$s] ?? $s) . '</span>';
}
