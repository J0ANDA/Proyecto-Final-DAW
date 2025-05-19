<?php
session_start(); // Inicia la sesión
header('Content-Type: application/json');

require '../includes/conexion.php'; // archivo de conexión a la base de datos

// Verificar 'producto_id' si esta en el get
if (!isset($_GET['producto_id'])) {
    echo json_encode(['error' => 'Producto no especificado']); // mensaje de error JSON
    exit(); // fin del script
}

$producto_id = $_GET['producto_id']; // ID del producto del GET

// Media de las valoraciones y cantidad de votos de un producto especificado
$stmt = $pdo->prepare("SELECT ROUND(AVG(valoracion), 1) as media, COUNT(*) as votos FROM votos WHERE producto_id = ?");
$stmt->execute([$producto_id]);
$result = $stmt->fetch();

// Si no hay votos, devolver media = 0 y votos = 0
$media = $result['media'] ?? 0; 
$votos = $result['votos'] ?? 0; 

// Devolver los datos en JSON
echo json_encode([
    'media' => $media,
    'votos' => $votos
]);
?>
