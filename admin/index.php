<?php

require_once __DIR__ . '/../includes/admin_boot.php';

$totalProductos = (int) $conexion->query('SELECT COUNT(*) FROM productos')->fetchColumn();
$totalEmpleados = (int) $conexion->query('SELECT COUNT(*) FROM empleados WHERE activo = 1')->fetchColumn();
$totalPuestos = (int) $conexion->query('SELECT COUNT(*) FROM puestos')->fetchColumn();

adminEncabezado('Panel', 'inicio');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">AGPR / PANEL</span>
        <h1>Hola, <?= e($empleado['nombre']) ?></h1>
        <p class="admin-nota">Entras como <?= e($empleado['puesto']) ?>. Estas son las áreas que tu puesto te permite usar.</p>
    </div>
</div>

<section class="admin-grid">
    <article class="admin-tarjeta">
        <span class="admin-kicker">CATÁLOGO</span>
        <strong><?= $totalProductos ?></strong>
        <p>Productos registrados.</p>
        <?php if (puede('productos.ver')): ?>
            <a href="productos.php">Administrar productos</a>
        <?php endif; ?>
    </article>
    <article class="admin-tarjeta">
        <span class="admin-kicker">PLANILLA</span>
        <strong><?= $totalEmpleados ?></strong>
        <p>Empleados con acceso activo.</p>
        <?php if (puede('empleados.ver')): ?>
            <a href="empleados.php">Administrar empleados</a>
        <?php endif; ?>
    </article>
    <article class="admin-tarjeta">
        <span class="admin-kicker">PUESTOS</span>
        <strong><?= $totalPuestos ?></strong>
        <p>Puestos y sus privilegios base.</p>
        <?php if (puede('puestos.editar')): ?>
            <a href="puestos.php">Administrar puestos</a>
        <?php endif; ?>
    </article>
</section>

<?php adminPie(); ?>
