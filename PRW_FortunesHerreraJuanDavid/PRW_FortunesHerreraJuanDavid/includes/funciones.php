<?php
// includes/funciones.php

require 'conexion.php'; // Incluye la conexión a la base de datos

function miVoto($usuario_id, $producto_id, $valoracion) {
    global $pdo;

    // Verificar si el usuario ya ha votado este producto
    $stmt = $pdo->prepare("SELECT * FROM votos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    if ($stmt->fetch()) {
        return false; // El usuario ya votó este producto
    }

    // Insertar el voto
    $stmt = $pdo->prepare("INSERT INTO votos (usuario_id, producto_id, valoracion) VALUES (?, ?, ?)");
    $stmt->execute([$usuario_id, $producto_id, $valoracion]);

    // Calcular la nueva media de valoraciones redondeada a un decimal
    $stmt = $pdo->prepare("SELECT ROUND(AVG(valoracion), 1) as media FROM votos WHERE producto_id = ?");
    $stmt->execute([$producto_id]);
    $result = $stmt->fetch();

    // Devolver la media de las valoraciones
    return $result['media'];
}

function pintarEstrellas($producto_id) {
    global $pdo;

    // Obtener la media de valoraciones (redondeada a un decimal) y el número de votos
    $stmt = $pdo->prepare("SELECT ROUND(AVG(valoracion), 1) as media, COUNT(*) as votos FROM votos WHERE producto_id = ?");
    $stmt->execute([$producto_id]);
    $result = $stmt->fetch();

    // Si no hay votos, devolver valores por defecto
    if (!$result['media']) {
        return [
            'media' => 0,
            'votos' => 0
        ];
    }

    // Devolver la media y el número de votos
    return [
        'media' => $result['media'],
        'votos' => $result['votos']
    ];
}
?>
