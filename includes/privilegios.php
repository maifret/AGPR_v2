<?php

require_once __DIR__ . '/personal.php';

function empleadoEnSesion(PDO $db): ?array
{
    $id = (int) ($_SESSION['agpr_empleado_id'] ?? 0);
    if ($id < 1) {
        return null;
    }

    $empleado = obtenerEmpleado($db, $id);
    if (!$empleado || $empleado['activo'] !== 1) {
        cerrarEmpleado();

        return null;
    }

    $_SESSION['agpr_empleado_nombre'] = $empleado['nombre'];
    $_SESSION['agpr_empleado_puesto'] = $empleado['puesto'];

    return $empleado;
}

function exigirEmpleado(PDO $db): array
{
    $empleado = empleadoEnSesion($db);
    if ($empleado) {
        return $empleado;
    }

    header('Location: ' . urlLogin());
    exit;
}

function puede(string $clave): bool
{
    $claves = $GLOBALS['agpr_claves_empleado'] ?? null;
    if (!is_array($claves)) {
        return false;
    }

    return in_array($clave, $claves, true);
}

function exigirPrivilegio(string $clave): void
{
    if (puede($clave)) {
        return;
    }

    http_response_code(403);
    adminEncabezado('Sin permiso', '');
    echo '<section class="admin-panel"><h1>Sin permiso</h1>';
    echo '<p>Tu puesto no incluye esta acción. Un administrador puede concedértela desde Empleados.</p>';
    echo '<p><a class="btn-principal admin-btn" href="index.php">Volver al panel</a></p></section>';
    adminPie();
    exit;
}
