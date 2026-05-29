<?php
require_once 'config.php';
require_role(['admin', 'worker']);

// Filtros
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$type = trim($_GET['type'] ?? '');
$day = trim($_GET['day'] ?? '');
$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');

$sql = "SELECT m.*, u.username as registrado_por_name FROM movimientos m LEFT JOIN usuarios u ON m.created_by_id = u.id WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND m.description LIKE ?";
    $params[] = "%$search%";
}
if ($category) {
    $sql .= " AND m.category = ?";
    $params[] = $category;
}
if ($type) {
    $sql .= " AND m.type = ?";
    $params[] = $type;
}
if ($year) {
    $sql .= " AND YEAR(m.date) = ?";
    $params[] = $year;
}
if ($month) {
    $sql .= " AND MONTH(m.date) = ?";
    $params[] = $month;
}
if ($day) {
    $sql .= " AND DAY(m.date) = ?";
    $params[] = $day;
}

$sql .= " ORDER BY m.date DESC, m.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movements = $stmt->fetchAll();

// CSV Export
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=movimientos_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
// Add BOM for Excel UTF-8 support
fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

fputcsv($output, ['ID', 'Fecha', 'Tipo', 'Categoría', 'Descripción', 'Monto', 'Registrado Por']);

foreach ($movements as $m) {
    fputcsv($output, [
        $m['id'],
        $m['date'],
        $m['type'],
        $m['category'],
        $m['description'],
        $m['amount'],
        $m['registrado_por_name']
    ]);
}
fclose($output);
exit;
?>
