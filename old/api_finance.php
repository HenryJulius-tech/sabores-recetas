<?php
require_once 'config.php';
require_role('admin');

header('Content-Type: application/json');

$filtro = $_GET['filter'] ?? 'mensual';
$hoy = new DateTime();

$labels = [];
$ingresos = [];
$gastos = [];

if ($filtro == 'diario') {
    for ($i = 14; $i >= 0; $i--) {
        $dia = clone $hoy;
        $dia->modify("-$i days");
        $labels[] = $dia->format('d M');
        $date_str = $dia->format('Y-m-d');
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'ingreso' AND date = ?");
        $stmt->execute([$date_str]);
        $ingresos[] = (float)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'gasto' AND date = ?");
        $stmt->execute([$date_str]);
        $gastos[] = (float)$stmt->fetchColumn();
    }
} elseif ($filtro == 'semanal') {
    for ($i = 7; $i >= 0; $i--) {
        $inicio_sem = clone $hoy;
        $dias_restar = clone $hoy;
        $dias_restar = $dias_restar->format('w') - 1; 
        if($dias_restar < 0) $dias_restar = 6;
        $inicio_sem->modify("-" . ($dias_restar + ($i * 7)) . " days");
        
        $fin_sem = clone $inicio_sem;
        $fin_sem->modify("+6 days");
        
        $labels[] = "Sem " . $inicio_sem->format('d/m');
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'ingreso' AND date >= ? AND date <= ?");
        $stmt->execute([$inicio_sem->format('Y-m-d'), $fin_sem->format('Y-m-d')]);
        $ingresos[] = (float)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'gasto' AND date >= ? AND date <= ?");
        $stmt->execute([$inicio_sem->format('Y-m-d'), $fin_sem->format('Y-m-d')]);
        $gastos[] = (float)$stmt->fetchColumn();
    }
} else { // mensual
    for ($i = 5; $i >= 0; $i--) {
        $mes_actual = (int)$hoy->format('m') - $i;
        $year_actual = (int)$hoy->format('Y');
        while ($mes_actual <= 0) {
            $mes_actual += 12;
            $year_actual -= 1;
        }
        
        $nombres_meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"];
        $labels[] = $nombres_meses[$mes_actual - 1] . " " . $year_actual;
        
        $inicio_mes = "$year_actual-" . str_pad($mes_actual, 2, '0', STR_PAD_LEFT) . "-01";
        $fin_mes = date("Y-m-t", strtotime($inicio_mes));
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'ingreso' AND date >= ? AND date <= ?");
        $stmt->execute([$inicio_mes, $fin_mes]);
        $ingresos[] = (float)$stmt->fetchColumn();
        
        $stmt = $pdo->prepare("SELECT SUM(amount) FROM movimientos WHERE type = 'gasto' AND date >= ? AND date <= ?");
        $stmt->execute([$inicio_mes, $fin_mes]);
        $gastos[] = (float)$stmt->fetchColumn();
    }
}

echo json_encode([
    'labels' => $labels,
    'ingresos' => $ingresos,
    'gastos' => $gastos
]);
?>
