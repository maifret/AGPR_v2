<?php

require_once __DIR__ . '/../includes/admin_boot.php';

$etiquetasValidas = ['', 'oferta', 'nuevo', 'mas-vendido', 'destacado'];
$nombresEtiqueta = [
    '' => 'Sin etiqueta',
    'oferta' => 'Oferta',
    'nuevo' => 'Nuevo',
    'mas-vendido' => 'Más vendido',
    'destacado' => 'Destacado',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    exigirPrivilegio('productos.eliminar');

    if (!csrfValido($_POST['csrf'] ?? '')) {
        flash('La solicitud no es válida. Actualiza la página.', 'error');
        header('Location: productos.php');
        exit;
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nombre = $id ? eliminarProducto($conexion, $id) : null;
    flash($nombre ? 'Se eliminó ' . $nombre . '.' : 'Ese producto ya no existe.', $nombre ? 'ok' : 'error');
    header('Location: productos.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

$editando = (bool) $id;
exigirPrivilegio($editando ? 'productos.editar' : 'productos.crear');

$categorias = listarCategorias($conexion);
$imagenes = imagenesDeCatalogo();
$producto = $editando ? obtenerProducto($conexion, (int) $id) : null;

if ($editando && !$producto && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('No encontramos ese producto.', 'error');
    header('Location: productos.php');
    exit;
}

$datos = $producto ?: [
    'id' => 0,
    'categoria_id' => (int) ($categorias[0]['id'] ?? 0),
    'nombre' => '',
    'precio' => '',
    'imagen' => $imagenes[0] ?? '',
    'descripcion' => '',
    'detalle' => '',
    'caracteristicas_texto' => '',
    'etiqueta' => '',
    'stock' => 0,
    'activo' => 1,
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') !== 'eliminar') {
    if (!csrfValido($_POST['csrf'] ?? '')) {
        $error = 'La solicitud no es válida. Actualiza la página.';
    } else {
        $datos['id'] = (int) $id;
        $datos['categoria_id'] = (int) ($_POST['categoria_id'] ?? 0);
        $datos['nombre'] = trim($_POST['nombre'] ?? '');
        $datos['precio'] = str_replace(',', '.', trim($_POST['precio'] ?? ''));
        $datos['descripcion'] = trim($_POST['descripcion'] ?? '');
        $datos['detalle'] = trim($_POST['detalle'] ?? '');
        $datos['caracteristicas_texto'] = trim($_POST['caracteristicas'] ?? '');
        $datos['etiqueta'] = $_POST['etiqueta'] ?? '';
        $datos['stock'] = $_POST['stock'] ?? '';
        $datos['activo'] = isset($_POST['activo']) ? 1 : 0;
        $imagenElegida = $_POST['imagen'] ?? '';

        $categoriaValida = false;
        foreach ($categorias as $categoria) {
            if ((int) $categoria['id'] === $datos['categoria_id']) {
                $categoriaValida = true;
                break;
            }
        }

        if (!$categoriaValida) {
            $error = 'Elige una categoría válida.';
        } elseif (mb_strlen($datos['nombre']) < 2 || mb_strlen($datos['nombre']) > 120) {
            $error = 'El nombre debe tener entre 2 y 120 caracteres.';
        } elseif (!is_numeric($datos['precio']) || (float) $datos['precio'] <= 0 || (float) $datos['precio'] > 999999.99) {
            $error = 'Escribe un precio mayor que cero.';
        } elseif (filter_var($datos['stock'], FILTER_VALIDATE_INT) === false || (int) $datos['stock'] < 0) {
            $error = 'El stock debe ser un número entero de cero en adelante.';
        } elseif (mb_strlen($datos['descripcion']) < 4 || mb_strlen($datos['descripcion']) > 180) {
            $error = 'La descripción corta debe tener entre 4 y 180 caracteres.';
        } elseif (mb_strlen($datos['detalle']) > 2000) {
            $error = 'El detalle es demasiado largo.';
        } elseif (mb_strlen($datos['caracteristicas_texto']) > 2000) {
            $error = 'Las características son demasiado largas.';
        } elseif (!in_array($datos['etiqueta'], $etiquetasValidas, true)) {
            $error = 'La etiqueta no es válida.';
        } else {
            $subida = guardarImagenProducto($_FILES['imagen_archivo'] ?? []);

            if (!$subida['ok'] && empty($subida['vacio'])) {
                $error = $subida['error'];
            } elseif ($subida['ok']) {
                $imagenNueva = $subida['ruta'];
            } elseif (in_array($imagenElegida, $imagenes, true)) {
                $imagenNueva = $imagenElegida;
            } else {
                $imagenNueva = '';
            }

            if ($error === '' && $imagenNueva === '') {
                $error = 'Elige o sube una imagen.';
            }

            if ($error === '') {
                $anterior = $producto['imagen'] ?? '';
                $datos['precio'] = round((float) $datos['precio'], 2);
                $datos['stock'] = (int) $datos['stock'];
                $datos['imagen'] = $imagenNueva;
                $datos['caracteristicas'] = $datos['caracteristicas_texto'];
                if ($datos['detalle'] === '') {
                    $datos['detalle'] = $datos['descripcion'];
                }

                guardarProducto($conexion, $datos);

                if ($anterior !== '' && $anterior !== $imagenNueva) {
                    borrarImagenSubida($anterior);
                }

                flash($editando ? 'Producto actualizado.' : 'Producto creado.');
                header('Location: productos.php');
                exit;
            }
        }
    }
}

adminEncabezado($editando ? 'Editar producto' : 'Nuevo producto', 'productos');
?>

<div class="admin-encabezado">
    <div>
        <span class="admin-kicker">CATÁLOGO</span>
        <h1><?= $editando ? 'Editar producto' : 'Nuevo producto' ?></h1>
    </div>
    <a class="admin-btn-secundario" href="productos.php">Volver</a>
</div>

<section class="admin-panel">
    <?php if ($error): ?>
        <p class="admin-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form class="admin-form" method="post" enctype="multipart/form-data">
        <?= campoCsrf() ?>
        <input type="hidden" name="id" value="<?= (int) ($datos['id'] ?? 0) ?>">

        <div class="admin-campos">
            <label>
                Nombre
                <input name="nombre" required maxlength="120" value="<?= e($datos['nombre']) ?>">
            </label>
            <label>
                Categoría
                <select name="categoria_id" required>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int) $categoria['id'] ?>" <?= (int) $datos['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>>
                            <?= e($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Precio MXN
                <input name="precio" type="number" min="0.01" step="0.01" required value="<?= e($datos['precio']) ?>">
            </label>
            <label>
                Stock
                <input name="stock" type="number" min="0" step="1" required value="<?= e($datos['stock']) ?>">
            </label>
            <label>
                Etiqueta
                <select name="etiqueta">
                    <?php foreach ($nombresEtiqueta as $clave => $nombre): ?>
                        <option value="<?= e($clave) ?>" <?= (string) $datos['etiqueta'] === (string) $clave ? 'selected' : '' ?>>
                            <?= e($nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="admin-check">
                <input type="checkbox" name="activo" value="1" <?= !empty($datos['activo']) ? 'checked' : '' ?>>
                Visible en la tienda
            </label>
            <label class="admin-ancho">
                Descripción corta
                <input name="descripcion" required maxlength="180" value="<?= e($datos['descripcion']) ?>">
            </label>
            <label class="admin-ancho">
                Detalle
                <textarea name="detalle" maxlength="2000"><?= e($datos['detalle']) ?></textarea>
            </label>
            <label class="admin-ancho">
                Características, una por línea
                <textarea name="caracteristicas" maxlength="2000"><?= e($datos['caracteristicas_texto']) ?></textarea>
            </label>
            <label class="admin-ancho">
                Imagen existente
                <select name="imagen">
                    <?php foreach ($imagenes as $imagen): ?>
                        <option value="<?= e($imagen) ?>" <?= ($datos['imagen'] ?? '') === $imagen ? 'selected' : '' ?>>
                            <?= e($imagen) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="admin-ancho">
                O sube una imagen nueva
                <input type="file" name="imagen_archivo" accept="image/jpeg,image/png,image/webp,image/gif">
            </label>
        </div>

        <?php if (!empty($datos['imagen'])): ?>
            <img class="admin-preview" src="../<?= e($datos['imagen']) ?>" alt="Vista previa">
        <?php endif; ?>

        <button class="admin-btn" type="submit"><?= $editando ? 'Guardar cambios' : 'Crear producto' ?></button>
    </form>
</section>

<?php adminPie(); ?>
