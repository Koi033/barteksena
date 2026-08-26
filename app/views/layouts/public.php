<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Bartek', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/18f357a62d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/public.css">
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/images/favicon.jfif">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/errors.css">
</head>
<body class="layout-public">

    <header class="header-public">
        <div class="logo">
            <a href="<?= BASE_URL ?>/">
                <img src="<?= BASE_URL ?>/public/images/logo.1.png" alt="Bartek Logo">
            </a>
        </div>
        <nav>
            <ul class="menu-horizontal">
                <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
                <li><a href="<?= BASE_URL ?>/nosotros">Nosotros</a></li>
                <li><a href="<?= BASE_URL ?>/servicios">Servicios</a></li>
                <li><a href="<?= BASE_URL ?>/contacto">Contacto</a></li>
                <?php if (estaAutenticado()): ?>
                    <li><a href="<?= BASE_URL ?>/dashboard" class="btn-nav-accent">Panel</a></li>
                    <li><a href="<?= BASE_URL ?>/logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/login">Login</a></li>
                    <li><a href="<?= BASE_URL ?>/registro">Registro</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

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

    <!-- Contenido de la vista -->
    <?= $contenido ?>

    <footer class="footer-public">
        <p>© 2026 Bartek</p>
        <div class="redes">
            <a href="https://www.facebook.com/share/1B9g2TnVrB/" target="_blank" rel="noopener">Facebook</a>
            <a href="https://www.instagram.com/bartek.bar_?igsh=MTc0amM2OTQ0ZDVqaA==" target="_blank" rel="noopener">Instagram</a>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
