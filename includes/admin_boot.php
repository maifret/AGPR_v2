<?php

require_once __DIR__ . '/sesion.php';
require_once dirname(__DIR__) . '/conexion.php';
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/productos.php';
require_once __DIR__ . '/privilegios.php';
require_once __DIR__ . '/admin_vista.php';

try {
    $empleado = exigirEmpleado($conexion);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(503);
    exit('Falta preparar la base de datos. Abre ../instalar.php');
}

$GLOBALS['agpr_claves_empleado'] = $empleado['privilegios'];
