<?php

require_once __DIR__ . '/../includes/admin_boot.php';

exigirPrivilegio('puestos.editar');

$catalogo = listarPrivilegios($conexion);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf'] ?? '')) {
        flash('La solicitud no es válida. Actualiza la página.', 'error');
        header('Location: puestos.php');
        exit;
    }

    $accion = $_POST['accion'] ?? 'guardar';
    $puestoId = (int) ($_POST['id'] ?? 0);

    if ($accion === 'eliminar') {
        $puesto = obtenerPuesto($conexion, $puestoId);
        $conteo = $conexion->prepare('SELECT COUNT(*) FROM empleados WHERE puesto_id = ?');
        $conteo->execute([$puestoId]);

        if (!$puesto) {
            flash('Ese puesto ya no existe.', 'error');
        } elseif ((int) $conteo->fetchColumn() > 0) {
            flash('No puedes eliminar un puesto que todavía tiene empleados.', 'error');
        } else {
            eliminarPuesto($conexion, $puestoId);
            flash('Puesto eliminado.');
        }

        header('Location: puestos.php');
        exit;
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $claves = $_POST['privilegios'] ?? [];
    if (!is_array($claves)) {
        $claves = [];
    }

    $validas = array_column($catalogo, 'clave');
    $claves = array_values(array_intersect($claves, $validas));

    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
        $error = 'El nombre del puesto debe tener entre 2 y 80 caracteres.';
    } elseif (mb_strlen($descripcion) < 4 || mb_strlen($descripcion) > 255) {
        $error = 'Describe el puesto en menos de 255 caracteres.';
    } elseif ($puestoId > 0 && !simularPuesto($conexion, $puestoId, $claves)) {
        $error = 'Ese cambio dejaría a la tienda sin alguien que pueda administrar privilegios.';
    } else {
        try {
            guardarPuesto($conexion, [
                'id' => $puestoId,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
            ], $claves);
            flash($puestoId ? 'Puesto actualizado.' : 'Puesto creado.');
            header('Location: puestos.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = $e->getCode() === '23000'
                ? 'Ya existe un puesto con ese nombre.'
                : 'No se pudo guardar el puesto.';
        }
    }

    $id = $puestoId;
    $formulario = [
        'id' => $puestoId,
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'claves' => $claves,
    ];
}

$puestos = listarPuestos($conexion);
$editado = $id ? obtenerPuesto($conexion, $id) : null;

if (!isset($formulario)) {
    $formulario = [
        'id' => $editado['id'] ?? 0,
        'nombre' => $editado['nombre'] ?? '',
        'descripcion' => $editado['descripcion'] ?? '',
        'claves' => $editado ? clavesDePuesto($conexion, (int) $editado['id']) : [],
    ];
}

adminEncabezado('Puestos', 'puestos');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">PLANILLA</span>
        <h1>Puestos</h1>
        <p class="admin-nota">Los privilegios marcados aquí los reciben todas las personas de ese puesto, salvo que se les conceda o revoque alguno en su ficha.</p>
    </div>
</div>

<section class="admin-panel" style="margin-bottom: 18px;">
    <div class="admin-tabla-wrap">
        <table class="admin-tabla">
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Empleados</th>
                    <th>Privilegios</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($puestos as $puesto): ?>
                    <tr>
                        <td>
                            <strong><?= e($puesto['nombre']) ?></strong><br>
                            <small><?= e($puesto['descripcion']) ?></small>
                        </td>
                        <td><?= (int) $puesto['empleados'] ?></td>
                        <td><?= (int) $puesto['privilegios'] ?></td>
                        <td>
                            <div class="admin-acciones">
                                <a class="admin-btn-secundario" href="puestos.php?id=<?= (int) $puesto['id'] ?>">Editar</a>
                                <?php if ((int) $puesto['empleados'] === 0): ?>
                                    <form method="post" onsubmit="return confirm('¿Eliminar este puesto?');">
                                        <?= campoCsrf() ?>
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= (int) $puesto['id'] ?>">
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

<section class="admin-panel">
    <h2><?= !empty($formulario['id']) ? 'Editar puesto' : 'Nuevo puesto' ?></h2>
    <?php if ($error): ?>
        <p class="admin-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="admin-form" method="post">
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) $formulario['id'] ?>">
        <div class="admin-campos">
            <label>
                Nombre
                <input name="nombre" required maxlength="80" value="<?= e($formulario['nombre']) ?>">
            </label>
            <label>
                Descripción
                <input name="descripcion" required maxlength="255" value="<?= e($formulario['descripcion']) ?>">
            </label>
        </div>
        <?php adminMatrizPrivilegios($catalogo, $formulario['claves'], [], true); ?>
        <div class="admin-acciones">
            <button class="admin-btn" type="submit"><?= !empty($formulario['id']) ? 'Guardar puesto' : 'Crear puesto' ?></button>
            <?php if (!empty($formulario['id'])): ?>
                <a class="admin-btn-secundario" href="puestos.php">Crear otro</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<?php adminPie(); ?>
