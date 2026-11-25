<?php

require_once __DIR__ . '/includes/sesion.php';

if (!empty($_SESSION['agpr_empleado_id'])) {
    header('Location: admin/index.php');
    exit;
}

header('Location: login.php');
exit;
