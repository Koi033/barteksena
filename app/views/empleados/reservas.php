<?php
// Carga la configuración de la base de datos desde la raíz del proyecto
require_once dirname(__DIR__, 3) . '/config/database.php';

try {
    $pdo = Database::obtenerInstancia()->obtenerConexion();
    
    // Traemos todas las reservas de MySQL
    $stmt = $pdo->query("SELECT * FROM reservas ORDER BY fecha DESC, hora ASC");
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reservas = [];
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bartek - Reservas</title>
    <style>
        body { font-family: sans-serif; background-color: #121212; color: #fff; padding: 20px; }
        .tabla-reservas { width: 100%; border-collapse: collapse; background: #1e1e1e; margin-top: 20px; border-radius: 8px; overflow: hidden; }
        .tabla-reservas th, .tabla-reservas td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #333; }
        .tabla-reservas th { background: #2a2a2a; color: #ff8c00; }
        .badge-mesa { background: #007bff; padding: 4px 8px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <h2> Gestión de Reservas</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;">Error: <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <table class="tabla-reservas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Mesa</th>
                <th>Personas</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reservas)): ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No hay reservas registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservas as $row): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['nombre_cliente']) ?></strong></td>
                        <td><?= htmlspecialchars($row['telefono']) ?></td>
                        <td><span class="badge-mesa">Mesa <?= htmlspecialchars($row['numero_mesa']) ?></span></td>
                        <td><?= htmlspecialchars($row['personas']) ?></td>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['hora']) ?></td>
                        <td style="color: #aaa; font-size: 0.85em;"><?= htmlspecialchars($row['creado_en']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>