<?php

require_once dirname(__DIR__) . '/conexion.php';
require_once dirname(__DIR__) . '/includes/productos.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!agprTablasListas($conexion)) {
    http_response_code(503);
    echo '[]';
    exit;
}

$productos = listarProductos($conexion, ['solo_activos' => true]);
$publicos = [];

foreach ($productos as $producto) {
    $publicos[] = [
        'id' => $producto['id'],
        'nombre' => $producto['nombre'],
        'categoria' => $producto['categoria'],
        'precio' => $producto['precio'],
        'imagen' => $producto['imagen'],
        'stock' => $producto['stock'],
    ];
}

echo json_encode($publicos, JSON_UNESCAPED_UNICODE);
