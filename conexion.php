<?php

$servidor = getenv('AGPR_DB_HOST') ?: '127.0.0.1';
$base_datos = getenv('AGPR_DB_NAME') ?: 'agpr_v2_db';
$usuario = getenv('AGPR_DB_USER') ?: 'root';
$contrasena = getenv('AGPR_DB_PASSWORD') !== false
    ? getenv('AGPR_DB_PASSWORD')
    : '';

if (defined('AGPR_SIN_CONEXION')) {
    return;
}

try {
    $conexion = new PDO(
        "mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(503);

    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (strpos($script, '/api/') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        exit('[]');
    }

    exit('Error al conectar con la base de datos. Si todavía no existe, abre instalar.php.');
}
