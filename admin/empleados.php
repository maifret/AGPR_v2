<?php

require_once __DIR__ . '/../includes/admin_boot.php';

exigirPrivilegio('empleados.ver');

$empleados = listarEmpleados($conexion);
$puedeCrear = puede('empleados.crear');
$puedeEditar = puede('empleados.editar');
$puedeEliminar = puede('empleados.eliminar');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    exigirPrivilegio('empleados.eliminar');

    $id = (int) ($_POST['id'] ?? 0);

    if (!csrfValido($_POST['csrf'] ?? '')) {
        flash('La solicitud no es válida. Actualiza la página.', 'error');
    } elseif ($id === (int) $empleado['id']) {
        flash('No puedes eliminar tu propia cuenta.', 'error');
    } else {
        $mapa = mapaPersonal($conexion);
        if (!quedaAdministrador($mapa, $id)) {
            flash('Debe quedar al menos un empleado activo que pueda ver la plantilla y cambiar privilegios.', 'error');
        } else {
            $objetivo = obtenerEmpleado($conexion, $id);
            if (!$objetivo) {
                flash('Ese empleado ya no existe.', 'error');
            } else {
                eliminarEmpleado($conexion, $id);
                flash('Se eliminó a ' . $objetivo['nombre'] . '.');
            }
        }
    }

    header('Location: empleados.php');
    exit;
}

adminEncabezado('Empleados', 'empleados');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">PLANILLA</span>
        <h1>Empleados</h1>
        <p class="admin-nota">El puesto define los privilegios base. En cada persona puedes conceder permisos extra o quitar los del puesto.</p>
    </div>
    <?php if ($puedeCrear): ?>
        <a class="admin-btn" href="empleado.php">Nuevo empleado</a>
    <?php endif; ?>
</div>

<section class="admin-panel">
    <div class="admin-tabla-wrap">
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Puesto</th>
                    <th>Privilegios efectivos</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($empleados as $persona): ?>
                    <tr>
                        <td>
                            <strong><?= e($persona['nombre']) ?></strong><br>
                            <small><?= e($persona['correo']) ?></small>
                        </td>
                        <td><?= e($persona['puesto']) ?></td>
                        <td><?= count($persona['privilegios']) ?></td>
                        <td>
                            <span class="admin-estado <?= $persona['activo'] ? 'admin-estado-si' : 'admin-estado-no' ?>">
                                <?= $persona['activo'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td>
                            <div class="admin-acciones">
                                <?php if ($puedeEditar): ?>
                                    <a class="admin-btn-secundario" href="empleado.php?id=<?= (int) $persona['id'] ?>">Editar</a>
                                <?php endif; ?>
                                <?php if ($puedeEliminar && (int) $persona['id'] !== (int) $empleado['id']): ?>
                                    <form method="post" onsubmit="return confirm('¿Eliminar a este empleado?');">
                                        <?= campoCsrf() ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $persona['id'] ?>">
                                        <button class="admin-btn-peligro" type="submit">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php adminPie(); ?>
