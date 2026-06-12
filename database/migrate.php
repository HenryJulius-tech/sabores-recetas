<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

echo "<h2>Migración de Base de Datos - Sabores & Recetas</h2>";

// 1. Verificar si ya estamos en el nuevo esquema
$existeNuevo = false;
try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('usuarios', $tables)) {
        $cols = $pdo->query("DESCRIBE usuarios")->fetchAll(PDO::FETCH_ASSOC);
        $colNames = array_column($cols, 'Field');
        if (in_array('nombre', $colNames)) {
            $existeNuevo = true;
        }
    }
} catch (Exception $e) {}

if ($existeNuevo) {
    echo "<p style='color:green;'>✓ El esquema nuevo ya existe</p>";
} else {
    // 2. Leer datos viejos ANTES de modificar tablas
    echo "<h3>1. Leyendo datos de la estructura vieja...</h3>";
    
    $oldUsers = [];
    $oldCursos = [];
    $oldModulos = [];
    $oldClases = [];
    $oldMatriculas = [];
    $oldPagos = [];
    
    try { $oldUsers = db_fetchAll("SELECT * FROM usuarios"); echo "✓ Usuarios: " . count($oldUsers) . "<br>"; } catch (Exception $e) { echo "⚠ usuarios: " . $e->getMessage() . "<br>"; }
    try { $oldCursos = db_fetchAll("SELECT * FROM cursos"); echo "✓ Cursos: " . count($oldCursos) . "<br>"; } catch (Exception $e) { echo "⚠ cursos: " . $e->getMessage() . "<br>"; }
    try { $oldModulos = db_fetchAll("SELECT * FROM modulos"); echo "✓ Módulos: " . count($oldModulos) . "<br>"; } catch (Exception $e) { echo "⚠ modulos: " . $e->getMessage() . "<br>"; }
    try { $oldClases = db_fetchAll("SELECT * FROM clases"); echo "✓ Clases: " . count($oldClases) . "<br>"; } catch (Exception $e) { echo "⚠ clases: " . $e->getMessage() . "<br>"; }
    try { $oldMatriculas = db_fetchAll("SELECT * FROM matriculas"); echo "✓ Matrículas: " . count($oldMatriculas) . "<br>"; } catch (Exception $e) { echo "⚠ matriculas: " . $e->getMessage() . "<br>"; }
    try { $oldPagos = db_fetchAll("SELECT * FROM pagos"); echo "✓ Pagos: " . count($oldPagos) . "<br>"; } catch (Exception $e) { echo "⚠ pagos: " . $e->getMessage() . "<br>"; }
    
    // 3. Eliminar tablas viejas y crear nuevas
    echo "<h3>2. Recreando tablas con nuevo esquema...</h3>";
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    foreach (['pagos', 'inscripciones', 'clases', 'modulos', 'cursos', 'usuarios'] as $t) {
        $pdo->exec("DROP TABLE IF EXISTS $t");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $statements = explode(';', $sql);
    foreach ($statements as $stmt) {
        $stmt = trim($stmt);
        if ($stmt) {
            try { $pdo->exec($stmt); } catch (PDOException $e) { echo "<p style='color:orange;'>⚠ " . $e->getMessage() . "</p>"; }
        }
    }
    echo "✓ Tablas nuevas creadas<br>";
    
    // 4. Migrar datos
    echo "<h3>3. Migrando datos...</h3>";
    
    // Usuarios
    foreach ($oldUsers as $u) {
        db_insert("INSERT INTO usuarios (nombre, email, password, role, foto, created_at) VALUES (?,?,?,?,?,?)",
            [$u['fullname'] ?: $u['username'], $u['email'], $u['password_hash'], $u['role'], $u['profile_photo'], $u['created_at']]);
    }
    echo "✓ Usuarios migrados: " . count($oldUsers) . "<br>";
    
    // Cursos
    foreach ($oldCursos as $c) {
        db_insert("INSERT INTO cursos (id, titulo, descripcion, precio, imagen, nivel, duracion, instructor, created_at) VALUES (?,?,?,?,?,?,?,?,?)",
            [$c['id'], $c['title'], $c['description'], $c['price'], $c['image_url'], $c['level'] ?? 'principiante', $c['duration'] ?? '', $c['instructor'] ?? '', $c['created_at']]);
    }
    echo "✓ Cursos migrados: " . count($oldCursos) . "<br>";
    
    // Módulos
    foreach ($oldModulos as $m) {
        db_insert("INSERT INTO modulos (id, curso_id, titulo, descripcion, orden, created_at) VALUES (?,?,?,?,?,?)",
            [$m['id'], $m['curso_id'], $m['title'], $m['description'], $m['orden'], $m['created_at']]);
    }
    echo "✓ Módulos migrados: " . count($oldModulos) . "<br>";
    
    // Clases
    foreach ($oldClases as $cl) {
        db_insert("INSERT INTO clases (id, modulo_id, titulo, descripcion, video_url, duracion, orden, created_at) VALUES (?,?,?,?,?,?,?,?)",
            [$cl['id'], $cl['modulo_id'], $cl['title'], $cl['description'], $cl['video_url'], $cl['duration'], $cl['orden'], $cl['created_at']]);
    }
    echo "✓ Clases migradas: " . count($oldClases) . "<br>";
    
    // Matrículas → Inscripciones
    foreach ($oldMatriculas as $m) {
        $estado = $m['status'] === 'approved' ? 'aprobado' : ($m['status'] === 'rejected' ? 'rechazado' : 'pendiente');
        db_insert("INSERT INTO inscripciones (id, user_id, curso_id, estado, created_at) VALUES (?,?,?,?,?)",
            [$m['id'], $m['user_id'], $m['curso_id'], $estado, $m['created_at']]);
    }
    echo "✓ Inscripciones migradas: " . count($oldMatriculas) . "<br>";
    
    // Pagos
    foreach ($oldPagos as $p) {
        $estado = $p['status'] === 'approved' ? 'aprobado' : ($p['status'] === 'rejected' ? 'rechazado' : 'pendiente');
        db_insert("INSERT INTO pagos (id, inscripcion_id, monto, metodo, comprobante, estado, created_at) VALUES (?,?,?,?,?,?,?)",
            [$p['id'], $p['matricula_id'], $p['amount'] ?? 0, $p['payment_method'], $p['proof_image_url'] ?? '', $estado, $p['created_at']]);
    }
    echo "✓ Pagos migrados: " . count($oldPagos) . "<br>";
}

echo "<hr><p style='color:green;font-size:18px;font-weight:bold;'>✓ Migración completada</p>";
echo "<p><a href='" . BASE_URL . "auth/login.php' class='btn btn-primary'>Ir al login</a></p>";
