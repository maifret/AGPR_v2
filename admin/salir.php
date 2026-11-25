<?php

require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/http.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValido($_POST['csrf'] ?? '')) {
    http_response_code(403);
    exit('Solicitud no válida.');
}

cerrarEmpleado();
header('Location: ../login.php');
exit;
