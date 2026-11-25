<?php

function agprTablasListas(PDO $db): bool
{
    try {
        $db->query('SELECT 1 FROM productos LIMIT 1');
        $db->query('SELECT 1 FROM empleados LIMIT 1');

        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function agprRequiereTablas(PDO $db): void
{
    if (agprTablasListas($db)) {
        return;
    }

    http_response_code(503);
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $destino = strpos($script, '/admin/') !== false ? '../instalar.php' : 'instalar.php';
    exit('Falta preparar la base de datos. Abre ' . $destino . ' para crearla.');
}

function listarCategorias(PDO $db): array
{
    return $db->query(
        'SELECT id, clave, nombre FROM categorias ORDER BY id'
    )->fetchAll();
}

function mapearProducto(array $fila): array
{
    $lineas = preg_split('/\R/', (string) ($fila['caracteristicas'] ?? '')) ?: [];
    $caracteristicas = [];

    foreach ($lineas as $linea) {
        $linea = trim($linea);
        if ($linea !== '') {
            $caracteristicas[] = $linea;
        }
    }

    return [
        'id' => (int) $fila['id'],
        'categoria_id' => (int) $fila['categoria_id'],
        'categoria' => $fila['categoria'],
        'categoria_nombre' => $fila['categoria_nombre'],
        'nombre' => $fila['nombre'],
        'precio' => (float) $fila['precio'],
        'imagen' => $fila['imagen'],
        'descripcion' => $fila['descripcion'],
        'detalle' => $fila['detalle'],
        'caracteristicas' => $caracteristicas,
        'caracteristicas_texto' => implode("\n", $caracteristicas),
        'etiqueta' => (string) ($fila['etiqueta'] ?? ''),
        'stock' => (int) $fila['stock'],
        'activo' => (int) $fila['activo'],
    ];
}

function listarProductos(PDO $db, array $filtro = []): array
{
    $sql = 'SELECT p.id, p.categoria_id, p.nombre, p.precio, p.imagen,
                   p.descripcion, p.detalle, p.caracteristicas, p.etiqueta,
                   p.stock, p.activo, c.clave AS categoria, c.nombre AS categoria_nombre
            FROM productos p
            INNER JOIN categorias c ON c.id = p.categoria_id
            WHERE 1 = 1';
    $parametros = [];

    if (!empty($filtro['solo_activos'])) {
        $sql .= ' AND p.activo = 1';
    }

    if (!empty($filtro['categoria']) && $filtro['categoria'] !== 'todos') {
        $sql .= ' AND c.clave = ?';
        $parametros[] = $filtro['categoria'];
    }

    if (!empty($filtro['buscar'])) {
        $like = '%' . addcslashes((string) $filtro['buscar'], '%_\\') . '%';
        $sql .= ' AND (p.nombre LIKE ? OR c.nombre LIKE ? OR c.clave LIKE ?)';
        $parametros[] = $like;
        $parametros[] = $like;
        $parametros[] = $like;
    }

    if (!empty($filtro['excepto_id'])) {
        $sql .= ' AND p.id <> ?';
        $parametros[] = (int) $filtro['excepto_id'];
    }

    $sql .= ' ORDER BY p.id ASC';

    $consulta = $db->prepare($sql);
    $consulta->execute($parametros);

    return array_map('mapearProducto', $consulta->fetchAll());
}

function obtenerProducto(PDO $db, int $id, bool $soloActivo = false): ?array
{
    $sql = 'SELECT p.id, p.categoria_id, p.nombre, p.precio, p.imagen,
                   p.descripcion, p.detalle, p.caracteristicas, p.etiqueta,
                   p.stock, p.activo, c.clave AS categoria, c.nombre AS categoria_nombre
            FROM productos p
            INNER JOIN categorias c ON c.id = p.categoria_id
            WHERE p.id = ?';

    if ($soloActivo) {
        $sql .= ' AND p.activo = 1';
    }

    $consulta = $db->prepare($sql);
    $consulta->execute([$id]);
    $fila = $consulta->fetch();

    return $fila ? mapearProducto($fila) : null;
}

function imagenesDeCatalogo(): array
{
    $raiz = dirname(__DIR__);
    $rutas = [
        'img/cascos.jpg',
        'img/guantes.jpg',
        'img/soportes.jpg',
        'img/intercomunicadores.jpg',
        'img/moto-portada.jpg',
    ];

    $directorio = $raiz . '/img/productos';
    if (is_dir($directorio)) {
        $archivos = scandir($directorio) ?: [];
        foreach ($archivos as $archivo) {
            if (preg_match('/\.(jpe?g|png|webp|gif)$/i', $archivo)) {
                $rutas[] = 'img/productos/' . $archivo;
            }
        }
    }

    $existentes = [];
    foreach ($rutas as $ruta) {
        if (is_file($raiz . '/' . $ruta)) {
            $existentes[$ruta] = $ruta;
        }
    }

    $lista = array_values($existentes);
    sort($lista);

    return $lista;
}

function guardarImagenProducto(array $archivo): array
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'vacio' => true, 'ruta' => ''];
    }

    if (($archivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'vacio' => false, 'error' => 'No se pudo subir la imagen.'];
    }

    if (($archivo['size'] ?? 0) > 2000000) {
        return ['ok' => false, 'vacio' => false, 'error' => 'La imagen debe pesar menos de 2 MB.'];
    }

    $info = @getimagesize($archivo['tmp_name']);
    $tipos = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
        IMAGETYPE_GIF => 'gif',
    ];

    $tipo = $info[2] ?? 0;
    if (!$info || !isset($tipos[$tipo])) {
        return ['ok' => false, 'vacio' => false, 'error' => 'Usa una imagen JPG, PNG, WEBP o GIF.'];
    }

    $carpeta = dirname(__DIR__) . '/img/productos';
    if (!is_dir($carpeta) && !mkdir($carpeta, 0775, true) && !is_dir($carpeta)) {
        return ['ok' => false, 'vacio' => false, 'error' => 'No se pudo crear la carpeta de imágenes.'];
    }

    $nombre = 'prod_' . bin2hex(random_bytes(8)) . '.' . $tipos[$tipo];
    $destino = $carpeta . '/' . $nombre;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        return ['ok' => false, 'vacio' => false, 'error' => 'No se pudo guardar la imagen.'];
    }

    return ['ok' => true, 'vacio' => false, 'ruta' => 'img/productos/' . $nombre];
}

function borrarImagenSubida(string $ruta): void
{
    if (strpos($ruta, 'img/productos/') !== 0) {
        return;
    }

    $absoluta = dirname(__DIR__) . '/' . $ruta;
    if (is_file($absoluta)) {
        unlink($absoluta);
    }
}

function guardarProducto(PDO $db, array $datos): int
{
    $etiqueta = $datos['etiqueta'] !== '' ? $datos['etiqueta'] : null;

    if (!empty($datos['id'])) {
        $consulta = $db->prepare(
            'UPDATE productos
             SET categoria_id = ?, nombre = ?, precio = ?, imagen = ?,
                 descripcion = ?, detalle = ?, caracteristicas = ?,
                 etiqueta = ?, stock = ?, activo = ?
             WHERE id = ?'
        );
        $consulta->execute([
            $datos['categoria_id'],
            $datos['nombre'],
            $datos['precio'],
            $datos['imagen'],
            $datos['descripcion'],
            $datos['detalle'],
            $datos['caracteristicas'],
            $etiqueta,
            $datos['stock'],
            $datos['activo'],
            $datos['id'],
        ]);

        return (int) $datos['id'];
    }

    $consulta = $db->prepare(
        'INSERT INTO productos
            (categoria_id, nombre, precio, imagen, descripcion, detalle,
             caracteristicas, etiqueta, stock, activo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $consulta->execute([
        $datos['categoria_id'],
        $datos['nombre'],
        $datos['precio'],
        $datos['imagen'],
        $datos['descripcion'],
        $datos['detalle'],
        $datos['caracteristicas'],
        $etiqueta,
        $datos['stock'],
        $datos['activo'],
    ]);

    return (int) $db->lastInsertId();
}

function eliminarProducto(PDO $db, int $id): ?string
{
    $producto = obtenerProducto($db, $id);
    if (!$producto) {
        return null;
    }

    $consulta = $db->prepare('DELETE FROM productos WHERE id = ?');
    $consulta->execute([$id]);
    borrarImagenSubida($producto['imagen']);

    return $producto['nombre'];
}
