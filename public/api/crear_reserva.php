<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
// Si es una petición OPTIONS (preflight de CORS), responde y termina
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pdo = Database::obtenerInstancia()->obtenerConexion();

$input = json_decode(file_get_contents('php://input'), true);

$nombre   = $input['nombre'] ?? '';
$raw_fecha= $input['fecha'] ?? '';
$hora     = $input['hora'] ?? '';
$raw_mesa = $input['mesa'] ?? '';
$personas = $input['personas'] ?? '';
$telefono = $input['telefono'] ?? '';

// Sanitizar datos
$fecha = substr((string)$raw_fecha, 0, 10);
$mesa  = preg_replace('/[^0-9]/', '', (string)$raw_mesa);

if ($mesa && $fecha && $nombre) {
    $stmt = $pdo->prepare("INSERT INTO reservas (nombre_cliente, fecha, hora, numero_mesa, personas, telefono) VALUES (?, ?, ?, ?, ?, ?)");
    $exito = $stmt->execute([$nombre, $fecha, $hora, $mesa, $personas, $telefono]);

    echo json_encode(['success' => $exito]);
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}