<?php

function entrarEmpleado(array $empleado): void
{
    session_regenerate_id(true);

    unset(
        $_SESSION['agpr_usuario_id'],
        $_SESSION['agpr_usuario'],
        $_SESSION['agpr_rol'],
        $_SESSION['agpr_demo_cliente']
    );

    $_SESSION['agpr_empleado_id'] = (int) $empleado['id'];
    $_SESSION['agpr_empleado_nombre'] = $empleado['nombre'];
    $_SESSION['agpr_empleado_puesto'] = $empleado['puesto'];
    $_SESSION['agpr_csrf'] = bin2hex(random_bytes(32));
}

function entrarCliente(array $usuario): void
{
    session_regenerate_id(true);

    unset(
        $_SESSION['agpr_empleado_id'],
        $_SESSION['agpr_empleado_nombre'],
        $_SESSION['agpr_empleado_puesto'],
        $_SESSION['agpr_demo_cliente']
    );

    $_SESSION['agpr_usuario_id'] = (int) $usuario['id'];
    $_SESSION['agpr_usuario'] = $usuario['nombre'];
    $_SESSION['agpr_rol'] = $usuario['rol'];
    $_SESSION['agpr_csrf'] = bin2hex(random_bytes(32));
}

function cerrarEmpleado(): void
{
    unset(
        $_SESSION['agpr_empleado_id'],
        $_SESSION['agpr_empleado_nombre'],
        $_SESSION['agpr_empleado_puesto'],
        $_SESSION['agpr_csrf']
    );

    session_regenerate_id(true);
}
