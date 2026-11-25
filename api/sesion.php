<?php

require_once dirname(__DIR__) . '/includes/sesion.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$destino = 'login.php';
$tipo = null;
$nombre = '';

if (!empty($_SESSION['agpr_empleado_id'])) {
    $destino = 'admin/index.php';
    $tipo = 'empleado';
    $nombre = (string) ($_SESSION['agpr_empleado_nombre'] ?? '');
} elseif (!empty($_SESSION['agpr_usuario_id'])) {
    $destino = 'cuenta.php';
    $tipo = 'cliente';
    $nombre = (string) ($_SESSION['agpr_usuario'] ?? '');
}

echo json_encode([
    'destino' => $destino,
    'tipo' => $tipo,
    'nombre' => $nombre,
], JSON_UNESCAPED_UNICODE);
