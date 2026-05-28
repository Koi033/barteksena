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
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/recuperar.css">
</head>
<body class="layout-auth">

    <!-- Mensajes Flash -->
    <?php if (!empty($flash)): ?>
        <div class="flash-container flash-absolute">
            <?php foreach ($flash as $msg): ?>
                <div class="flash flash-<?= htmlspecialchars($msg['tipo'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($msg['mensaje'], ENT_QUOTES, 'UTF-8') ?>
                    <button class="flash-close" onclick="this.parentElement.remove()">×</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?= $contenido ?>

    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
