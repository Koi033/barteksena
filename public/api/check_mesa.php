<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';

$pdo = Database::obtenerInstancia()->obtenerConexion();

$input = json_decode(file_get_contents('php://input'), true);

$raw_mesa  = $input['mesa'] ?? null;
$raw_fecha = $input['fecha'] ?? null;

// 1. Limpiar mesa: extraer solo los dígitos (por si enviaron "la 10" o "10")
$mesa = preg_replace('/[^0-9]/', '', (string)$raw_mesa);

// 2. Limpiar fecha: recortar solo YYYY-MM-DD (primeros 10 caracteres de ISO)
$fecha = null;
if ($raw_fecha) {
    $fecha = substr((string)$raw_fecha, 0, 10);
}

if ($mesa && $fecha) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas WHERE numero_mesa = ? AND fecha = ?");
    $stmt->execute([$mesa, $fecha]);
    $count = $stmt->fetchColumn();

    // Devuelve true si la mesa ya existe para esa fecha
    echo json_encode(['ocupada' => ($count > 0)]);
} else {
    echo json_encode(['ocupada' => false, 'error' => 'Parametros invalidos']);
}
