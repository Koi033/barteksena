<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Bartek Panel', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/18f357a62d.js" crossorigin="anonymous"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- Estilos propios desde carpeta CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/dashboard.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/datatables-custom.css">
</head>
<body class="layout-dashboard">

    <!-- ── Sidebar ────────────────────────────────────────────────── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span>Bartek</span>
            <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Cerrar menú"><i class="fas fa-bars" aria-hidden="true"></i></button>
        </div>

        <nav>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?= BASE_URL ?>/dashboard"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/dashboard') ? 'active' : '' ?>">
                       <i class="fas fa-bell" aria-hidden="true"></i> Notificaciones
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/empleados"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/empleados') ? 'active' : '' ?>">
                       <i class="fas fa-users" aria-hidden="true"></i> Empleados
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/inventario"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/inventario') ? 'active' : '' ?>">
                       <i class="fas fa-boxes" aria-hidden="true"></i> Inventario
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/ventas"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/ventas') ? 'active' : '' ?>">
                       <i class="fas fa-cash-register" aria-hidden="true"></i> Ventas
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/menu"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/menu') ? 'active' : '' ?>">
                       <i class="fas fa-cocktail" aria-hidden="true"></i> Menú Interactivo
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/horarios"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/horarios') ? 'active' : '' ?>">
                       <i class="fas fa-clock" aria-hidden="true"></i> Horarios
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/reportes"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/reportes') ? 'active' : '' ?>">
                       <i class="fas fa-chart-bar" aria-hidden="true"></i> Reportes
                    </a>
                </li>
                <li>
                    <a href="<?= BASE_URL ?>/perfil"
                       class="<?= str_contains($_SERVER['REQUEST_URI'], '/perfil') ? 'active' : '' ?>">
                       <i class="fas fa-user-circle" aria-hidden="true"></i> Mi Perfil
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <a href="<?= BASE_URL ?>/perfil" class="user-name" style="text-decoration:none; color:#fff;">
                    <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </a>
                <span class="user-role"><?= htmlspecialchars($_SESSION['usuario_rol'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <!-- Logout con confirmación y POST para evitar CSRF por GET -->
            <form method="POST" action="<?= BASE_URL ?>/logout"
                  onsubmit="return confirm('¿Cerrar sesión?')">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars(generarTokenCSRF('logout'), ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn-logout"><i class="fas fa-power-off" aria-hidden="true"></i> Cerrar Sesión</button>
            </form>
        </div>
    </aside>

    <!-- ── Contenido principal ──────────────────────────────────── -->
    <main class="main-content" id="mainContent">

        <!-- Mensajes Flash -->
        <?php if (!empty($flash)): ?>
            <div class="flash-container">
                <?php foreach ($flash as $msg): ?>
                    <div class="flash flash-<?= htmlspecialchars($msg['tipo'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($msg['mensaje'], ENT_QUOTES, 'UTF-8') ?>
                        <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $contenido ?>
    </main>

    <!-- ── Scripts ─────────────────────────────────────────────── -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
    <script src="<?= BASE_URL ?>/public/js/dashboard.js"></script>
</body>
</html>
