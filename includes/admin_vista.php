<?php

function adminEncabezado(string $titulo, string $seccion): void
{
    $base = agprEnCarpeta('admin') ? '../' : '';
    $nombre = e($_SESSION['agpr_empleado_nombre'] ?? '');
    $puesto = e($_SESSION['agpr_empleado_puesto'] ?? '');
    $flash = tomarFlash();

    $enlaces = [
        'inicio' => ['Inicio', 'index.php', true],
        'productos' => ['Productos', 'productos.php', puede('productos.ver')],
        'empleados' => ['Empleados', 'empleados.php', puede('empleados.ver')],
        'puestos' => ['Puestos', 'puestos.php', puede('puestos.editar')],
    ];

    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . e($titulo) . ' | AGPR</title>';
    echo '<link rel="icon" href="' . e($base) . 'img/isotipo.png">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">';
    echo '<link rel="stylesheet" href="' . e($base) . 'css/estilos.css">';
    echo '<link rel="stylesheet" href="' . e($base) . 'css/admin.css">';
    echo '</head><body class="admin-body">';
    echo '<header class="admin-barra"><div class="admin-barra-interior">';
    echo '<a class="admin-marca" href="index.php"><img src="' . e($base) . 'img/logo.png" alt="AGPR"></a>';
    echo '<nav class="admin-nav" aria-label="Panel">';

    foreach ($enlaces as $clave => $enlace) {
        if (!$enlace[2]) {
            continue;
        }
        $activo = $seccion === $clave ? ' activo' : '';
        echo '<a class="admin-nav-link' . $activo . '" href="' . e($enlace[1]) . '">' . e($enlace[0]) . '</a>';
    }

    echo '</nav><div class="admin-usuario">';
    echo '<span><strong>' . $nombre . '</strong><small>' . $puesto . '</small></span>';
    echo '<a href="' . e($base) . 'index.php">Ver tienda</a>';
    echo '<form method="post" action="salir.php">' . campoCsrf();
    echo '<button type="submit">Salir</button></form>';
    echo '</div></div></header><main class="admin-main">';

    if ($flash) {
        $tipo = $flash['tipo'] === 'error' ? 'error' : 'ok';
        echo '<p class="admin-flash admin-flash-' . $tipo . '" role="status">' . e($flash['mensaje']) . '</p>';
    }
}

function adminPie(): void
{
    echo '</main></body></html>';
}

function adminMatrizPrivilegios(
    array $catalogo,
    array $marcados,
    array $delPuesto,
    bool $editable
): void {
    $marcado = array_fill_keys($marcados, true);
    $base = array_fill_keys($delPuesto, true);
    $grupo = '';

    echo '<div class="admin-privilegios">';

    foreach ($catalogo as $privilegio) {
        if ($privilegio['grupo'] !== $grupo) {
            $grupo = $privilegio['grupo'];
            echo '<h3>' . e($grupo) . '</h3>';
        }

        $clave = $privilegio['clave'];
        $id = 'priv-' . preg_replace('/[^a-z0-9]+/i', '-', $clave);
        echo '<label class="admin-privilegio" for="' . e($id) . '">';
        echo '<input type="checkbox" name="privilegios[]" id="' . e($id) . '" value="' . e($clave) . '"';
        if (isset($marcado[$clave])) {
            echo ' checked';
        }
        if (!$editable) {
            echo ' disabled';
        }
        echo '>';
        echo '<span><strong>' . e($privilegio['nombre']) . '</strong>';
        echo '<small>' . e($privilegio['descripcion']) . '</small></span>';
        if (isset($base[$clave])) {
            echo '<em>Del puesto</em>';
        } elseif (isset($marcado[$clave])) {
            echo '<em class="extra">Extra</em>';
        }
        echo '</label>';
    }

    echo '</div>';
}
