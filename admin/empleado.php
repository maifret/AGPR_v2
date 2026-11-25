<?php

require_once __DIR__ . '/../includes/admin_boot.php';

$catalogo = listarPrivilegios($conexion);
$puestos = listarPuestos($conexion);
$puedePrivilegios = puede('empleados.privilegios');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
}

$editando = $id > 0;
exigirPrivilegio($editando ? 'empleados.editar' : 'empleados.crear');

$actual = $editando ? obtenerEmpleado($conexion, $id) : null;
if ($editando && !$actual && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('No encontramos a ese empleado.', 'error');
    header('Location: empleados.php');
    exit;
}

$datos = [
    'id' => $actual['id'] ?? 0,
    'nombre' => $actual['nombre'] ?? '',
    'correo' => $actual['correo'] ?? '',
    'puesto_id' => $actual['puesto_id'] ?? (int) ($puestos[0]['id'] ?? 0),
    'activo' => $actual['activo'] ?? 1,
];
$marcados = $actual['privilegios'] ?? clavesDePuesto($conexion, (int) $datos['puesto_id']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValido($_POST['csrf'] ?? '')) {
        $error = 'La solicitud no es válida. Actualiza la página.';
    } else {
        $datos['nombre'] = trim($_POST['nombre'] ?? '');
        $datos['correo'] = strtolower(trim($_POST['correo'] ?? ''));
        $datos['puesto_id'] = (int) ($_POST['puesto_id'] ?? 0);
        $datos['activo'] = isset($_POST['activo']) ? 1 : 0;
        $clave = $_POST['clave'] ?? '';
        $puesto = obtenerPuesto($conexion, $datos['puesto_id']);
        $delPuesto = $puesto ? clavesDePuesto($conexion, $datos['puesto_id']) : [];

        if ($puedePrivilegios) {
            $enviados = $_POST['privilegios'] ?? [];
            if (!is_array($enviados)) {
                $enviados = [];
            }
            $overrides = calcularOverrides($delPuesto, $enviados, $catalogo);
            $efectivas = clavesEfectivas($delPuesto, $overrides);
        } else {
            $overrides = null;
            $baseOverrides = $actual['overrides'] ?? [];
            $efectivas = clavesEfectivas($delPuesto, $baseOverrides);
        }

        $marcados = $efectivas;

        if (mb_strlen($datos['nombre']) < 2 || mb_strlen($datos['nombre']) > 80) {
            $error = 'El nombre debe tener entre 2 y 80 caracteres.';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL) || strlen($datos['correo']) > 150) {
            $error = 'Escribe un correo válido.';
        } elseif (!$puesto) {
            $error = 'Elige un puesto válido.';
        } elseif ($editando && $datos['id'] === (int) $empleado['id'] && $datos['activo'] !== 1) {
            $error = 'No puedes desactivar tu propia cuenta.';
        } elseif (!$editando && (strlen($clave) < 8 || strlen($clave) > 72)) {
            $error = 'La contraseña debe tener entre 8 y 72 caracteres.';
        } elseif ($editando && $clave !== '' && (strlen($clave) < 8 || strlen($clave) > 72)) {
            $error = 'La contraseña nueva debe tener entre 8 y 72 caracteres.';
        } elseif (correoEmpleadoOcupado($conexion, $datos['correo'], (int) $datos['id'])) {
            $error = 'Ese correo ya pertenece a otro empleado.';
        } elseif (correoClienteOcupado($conexion, $datos['correo'])) {
            $error = 'Ese correo ya está registrado como cliente.';
        } else {
            $mapa = mapaPersonal($conexion);
            $reemplazo = [
                'id' => (int) ($datos['id'] ?: 0),
                'activo' => $datos['activo'] === 1,
                'claves' => $efectivas,
            ];

            $conserva = $editando
                ? quedaAdministrador($mapa, null, $reemplazo)
                : quedaAdministrador($mapa);

            if (!$conserva) {
                $error = 'Debe quedar al menos un empleado activo con permiso para ver empleados y cambiar privilegios.';
            } else {
                try {
                    $conexion->beginTransaction();

                    if ($editando) {
                        actualizarEmpleado($conexion, [
                            'id' => (int) $datos['id'],
                            'nombre' => $datos['nombre'],
                            'correo' => $datos['correo'],
                            'puesto_id' => $datos['puesto_id'],
                            'activo' => $datos['activo'],
                            'contrasena' => $clave !== '' ? password_hash($clave, PASSWORD_DEFAULT) : null,
                        ], $puedePrivilegios ? $overrides : null);
                    } else {
                        crearEmpleado($conexion, [
                            'nombre' => $datos['nombre'],
                            'correo' => $datos['correo'],
                            'contrasena' => password_hash($clave, PASSWORD_DEFAULT),
                            'puesto_id' => $datos['puesto_id'],
                            'activo' => $datos['activo'],
                        ], $overrides ?? []);
                    }

                    $conexion->commit();
                    flash($editando ? 'Empleado actualizado.' : 'Empleado creado.');
                    header('Location: empleados.php');
                    exit;
                } catch (PDOException $e) {
                    if ($conexion->inTransaction()) {
                        $conexion->rollBack();
                    }
                    error_log($e->getMessage());
                    $error = $e->getCode() === '23000'
                        ? 'Ese correo ya está registrado.'
                        : 'No se pudo guardar al empleado.';
                }
            }
        }
    }
}

$delPuesto = clavesDePuesto($conexion, (int) $datos['puesto_id']);
$privilegiosPorPuesto = [];
foreach ($puestos as $puesto) {
    $privilegiosPorPuesto[(int) $puesto['id']] = clavesDePuesto($conexion, (int) $puesto['id']);
}

adminEncabezado($editando ? 'Editar empleado' : 'Nuevo empleado', 'empleados');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">PLANILLA</span>
        <h1><?= $editando ? 'Editar empleado' : 'Nuevo empleado' ?></h1>
    </div>
    <a class="admin-btn-secundario" href="empleados.php">Volver</a>
</div>

<section class="admin-panel">
    <?php if ($error): ?>
        <p class="admin-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="admin-form" method="post" id="form-empleado">
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) $datos['id'] ?>">

        <div class="admin-campos">
            <label>
                Nombre
                <input name="nombre" required maxlength="80" value="<?= e($datos['nombre']) ?>">
            </label>
            <label>
                Correo
                <input type="email" name="correo" required maxlength="150" value="<?= e($datos['correo']) ?>">
            </label>
            <label>
                <?= $editando ? 'Nueva contraseña' : 'Contraseña' ?>
                <input type="password" name="clave" minlength="<?= $editando ? '0' : '8' ?>" maxlength="72" autocomplete="new-password" <?= $editando ? '' : 'required' ?>>
            </label>
            <label>
                Puesto
                <select name="puesto_id" id="puesto-empleado" required>
                    <?php foreach ($puestos as $puesto): ?>
                        <option value="<?= (int) $puesto['id'] ?>" <?= (int) $datos['puesto_id'] === (int) $puesto['id'] ? 'selected' : '' ?>>
                            <?= e($puesto['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="admin-check">
                <input type="checkbox" name="activo" value="1" <?= !empty($datos['activo']) ? 'checked' : '' ?>>
                Cuenta activa
            </label>
        </div>

        <div>
            <h2>Privilegios</h2>
            <p class="admin-nota">
                Al cambiar el puesto, la lista vuelve a los permisos de ese puesto.
                Marca uno que no traiga el puesto para concederlo, o desmárcalo para quitárselo solo a esta persona.
            </p>
            <?php adminMatrizPrivilegios($catalogo, $marcados, $delPuesto, $puedePrivilegios); ?>
            <?php if (!$puedePrivilegios): ?>
                <p class="admin-nota">Puedes cambiar el puesto. Para ajustar privilegios uno por uno necesitas ese permiso.</p>
            <?php endif; ?>
        </div>

        <button class="admin-btn" type="submit"><?= $editando ? 'Guardar cambios' : 'Crear empleado' ?></button>
    </form>
</section>

<script>
    const privilegiosPorPuesto = <?= json_encode($privilegiosPorPuesto, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT) ?>;
    const selector = document.getElementById('puesto-empleado');
    const casillas = document.querySelectorAll('input[name="privilegios[]"]');
    const puedeEditarPrivilegios = [...casillas].some((casilla) => !casilla.disabled);

    if (puedeEditarPrivilegios) {
        selector?.addEventListener('change', () => {
            const claves = new Set(privilegiosPorPuesto[selector.value] || []);

            casillas.forEach((casilla) => {
                casilla.checked = claves.has(casilla.value);
                const etiqueta = casilla.closest('.admin-privilegio');
                if (!etiqueta) return;
                etiqueta.querySelector('em')?.remove();
                if (!casilla.checked) return;
                const nota = document.createElement('em');
                nota.textContent = 'Del puesto';
                etiqueta.append(nota);
            });
        });
    }
</script>

<?php adminPie(); ?>
