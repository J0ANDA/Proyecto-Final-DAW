<?php
session_start(); // Inicia la sesión
header('Content-Type: application/json'); 

// Verificacion para ver si el usuario esta autentificado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); 
    echo json_encode(['error' => 'No estás autenticado']); 
    exit(); 
}

require '../includes/conexion.php'; // archivo de conexión a la base de datos

$usuario_id = $_SESSION['user_id']; // ID del usuario de la sesión
$data = json_decode(file_get_contents('php://input'), true); 
$producto_id = $data['producto_id']; // ID del producto 

// Eliminar el voto del usuario
$stmt = $pdo->prepare("DELETE FROM votos WHERE usuario_id = ? AND producto_id = ?");
$stmt->execute([$usuario_id, $producto_id]);

// Calcular la nueva media de valoraciones
$stmt = $pdo->prepare("SELECT ROUND(AVG(valoracion), 1) as media, COUNT(*) as votos FROM votos WHERE producto_id = ?");
$stmt->execute([$producto_id]);
$result = $stmt->fetch();

//media y numero de votos 
$media = $result['media'] ?? 0; 
$votos = $result['votos'] ?? 0; 

// Devolver la nueva media y numero de votos
echo json_encode([
    'media' => $media,
    'votos' => $votos
]);
?>
