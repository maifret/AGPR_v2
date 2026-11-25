<?php

require_once __DIR__ . '/../includes/admin_boot.php';

exigirPrivilegio('productos.ver');

$productos = listarProductos($conexion);
$puedeCrear = puede('productos.crear');
$puedeEditar = puede('productos.editar');
$puedeEliminar = puede('productos.eliminar');

adminEncabezado('Productos', 'productos');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">CATÁLOGO</span>
        <h1>Productos</h1>
    </div>
    <?php if ($puedeCrear): ?>
        <a class="admin-btn" href="producto.php">Nuevo producto</a>
    <?php endif; ?>
</div>

<section class="admin-panel">
    <div class="admin-tabla-wrap">
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td>
                            <img src="../<?= e($producto['imagen']) ?>" alt="">
                        </td>
                        <td>
                            <strong><?= e($producto['nombre']) ?></strong><br>
                            <small><?= e($producto['categoria_nombre']) ?></small>
                        </td>
                        <td>$<?= number_format($producto['precio'], 0) ?> MXN</td>
                        <td><?= (int) $producto['stock'] ?></td>
                        <td>
                            <span class="admin-estado <?= $producto['activo'] ? 'admin-estado-si' : 'admin-estado-no' ?>">
                                <?= $producto['activo'] ? 'Activo' : 'Oculto' ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-acciones">
                                <?php if ($puedeEditar): ?>
                                    <a class="admin-btn-secundario" href="producto.php?id=<?= (int) $producto['id'] ?>">Editar</a>
                                <?php endif; ?>
                                <?php if ($puedeEliminar): ?>
                                    <form method="post" action="producto.php" onsubmit="return confirm('¿Eliminar este producto?');">
                                        <?= campoCsrf() ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $producto['id'] ?>">
                                        <button class="admin-btn-peligro" type="submit">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$productos): ?>
                    <tr><td colspan="6">Todavía no hay productos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php adminPie(); ?>
