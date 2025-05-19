<?php
session_start(); // Inicia la sesión
header('Content-Type: application/json'); 

// Verifica si el usuario esta autentificado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403); 
    echo json_encode(['error' => 'No estás autenticado']); // Mensaje de error en  JSON
    exit(); 
}

require '../includes/funciones.php'; // Archivo de funciones

$usuario_id = $_SESSION['user_id']; // ID del usuario de la sesion
$data = json_decode(file_get_contents('php://input'), true); 
$producto_id = $data['producto_id']; // ID del producto 
$valoracion = $data['valoracion']; // Valoración del producto 



// Llama a  miVoto para la valoracion y la nueva media
$media = miVoto($usuario_id, $producto_id, $valoracion);

// Ver si el usuario ya voto
if ($media === false) {
    echo json_encode(['error' => 'Ya votaste este producto']); // Devuelve un mensaje de error en JSON
} else {
    // Devuelve la nueva media y la cantidad de votos JSON
    echo json_encode([
        'media' => $media, // Nueva media de 
        'votos' => obtenerCantidadVotos($producto_id) // Nuevos votos
    ]);
}
?>
