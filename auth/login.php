<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Todos los campos son obligatorios';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            registrar_log('Inicio de sesión', 'El usuario inició sesión con éxito.');
            session_setFlash('success', 'Bienvenido ' . $user['nombre']);

            if ($user['role'] === 'admin') {
                redirect(BASE_URL . 'admin/dashboard.php');
            } else {
                redirect(BASE_URL . 'estudiante/dashboard.php');
            }
        } else {
            $error = 'Credenciales incorrectas';
        }
    }
}

$cursos_disponibles = db_fetchAll("SELECT * FROM cursos ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sabores & Recetas - Plataforma de Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
        .hero-section { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 80px 0; position: relative; overflow: hidden; }
        .hero-section::after { content: ''; position: absolute; top: 0; right: 0; width: 50%; height: 100%; background: linear-gradient(135deg, rgba(233,30,99,0.2), transparent); clip-path: polygon(20% 0%, 100% 0, 100% 100%, 0% 100%); }
        .login-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); position: relative; z-index: 10; border: 1px solid rgba(255,255,255,0.1); }
        .login-logo { text-align: center; margin-bottom: 30px; }
        .login-logo i { font-size: 48px; color: #e91e63; }
        .login-logo h1 { font-size: 28px; font-weight: 800; color: #1a1a2e; margin-top: 10px; }
        .form-control { border-radius: 12px; padding: 14px 16px; border: 2px solid #e9ecef; font-size: 15px; }
        .form-control:focus { border-color: #e91e63; box-shadow: 0 0 0 4px rgba(233,30,99,0.1); }
        .btn-login { background: linear-gradient(135deg, #e91e63, #c2185b); border: none; border-radius: 12px; padding: 14px; font-weight: 600; font-size: 16px; width: 100%; color: #fff; transition: all 0.3s ease; }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(233,30,99,0.3); }
        .register-link { text-align: center; margin-top: 24px; font-size: 15px; color: #6c757d; }
        
        .section-title { font-weight: 800; color: #1a1a2e; margin-bottom: 40px; position: relative; display: inline-block; }
        .section-title::after { content: ''; position: absolute; bottom: -10px; left: 0; width: 60px; height: 4px; background: #e91e63; border-radius: 2px; }
        
        .course-card { border-radius: 16px; overflow: hidden; transition: all 0.3s ease; border: none; background: white; }
        .course-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important; }
        .course-img-wrapper { position: relative; height: 200px; overflow: hidden; }
        .course-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .course-card:hover .course-img-wrapper img { transform: scale(1.05); }
        .course-badge { position: absolute; top: 15px; right: 15px; z-index: 2; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .price-tag { font-size: 22px; font-weight: 800; color: #1a1a2e; }
        .course-meta { font-size: 13px; color: #6c757d; display: flex; gap: 15px; margin-bottom: 15px; }
        .course-meta i { color: #e91e63; }
    </style>
</head>
<body>
    
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 text-center text-lg-start z-1">
                    <h1 class="display-4 fw-bold mb-4">Domina el Arte<br>de la Cocina</h1>
                    <p class="lead mb-4 opacity-75">Aprende de los mejores chefs y transforma tu pasión en una carrera. Accede a cursos premium, clases prácticas y mucho más.</p>
                    <a href="#catalogo" class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold">Explorar Catálogo</a>
                </div>
                <div class="col-lg-5 offset-lg-1 z-1">
                    <div class="login-card">
                        <div class="login-logo">
                            <i class="bi bi-mortarboard-fill"></i>
                            <h1>Iniciar Sesión</h1>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3"><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                        <?php endif; ?>

                        <?php $f = session_getFlash('success'); if ($f): ?>
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3"><i class="bi bi-check-circle me-2"></i><?= e($f) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" placeholder="tu@email.com" value="<?= e(old('email')) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark">Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar a la Plataforma</button>
                        </form>

                        <div class="register-link">
                            ¿No tienes cuenta? <a href="register.php" style="color:#e91e63; font-weight:700; text-decoration:none;">Regístrate aquí</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="catalogo" class="container py-5 mt-4">
        <h2 class="section-title">Cursos Destacados</h2>
        
        <div class="row g-4 mt-2">
            <?php foreach ($cursos_disponibles as $c): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card course-card shadow-sm h-100">
                        <div class="course-img-wrapper">
                            <?= nivelBadge($c['nivel']) ?>
                            <img src="<?= upload('cursos/' . $c['imagen']) ?>" alt="<?= e($c['titulo']) ?>">
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold mb-3" style="color:#1a1a2e;"><?= e($c['titulo']) ?></h5>
                            <p class="text-muted small flex-grow-1 mb-4"><?= truncate($c['descripcion'] ?? '', 110) ?></p>
                            
                            <div class="course-meta">
                                <span><i class="bi bi-clock-history"></i> <?= e($c['duracion'] ?: 'Flexible') ?></span>
                                <span><i class="bi bi-person-badge"></i> <?= e($c['instructor'] ?: 'Experto') ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <div class="price-tag"><?= format_cop($c['precio']) ?></div>
                                <a href="register.php" class="btn btn-outline-primary rounded-pill fw-bold px-4">Comprar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5 text-center">
        <div class="container">
            <p class="mb-0 opacity-75">© <?= date('Y') ?> Sabores & Recetas. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
