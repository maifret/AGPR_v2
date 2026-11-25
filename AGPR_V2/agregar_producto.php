<?php
session_start();
require_once __DIR__ . '/conexion.php';

// Verificar que exista una sesión
if (empty($_SESSION['agpr_usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verificar que el usuario sea administrador
$consulta = $conexion->prepare(
    "SELECT rol FROM usuarios WHERE id = ?"
);

$consulta->execute([$_SESSION['agpr_usuario_id']]);
$usuario = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['rol'] !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. Solo administradores.');
}

// Obtener categorías
$consultaCategorias = $conexion->query(
    "SELECT id, nombre FROM categorias ORDER BY nombre"
);

$categorias = $consultaCategorias->fetchAll(PDO::FETCH_ASSOC);

// Protección CSRF
$_SESSION['agpr_csrf'] ??= bin2hex(random_bytes(32));

$error = '';
$exito = '';

if (isset($_SESSION['producto_agregado'])) {
    $exito = 'Producto agregado correctamente.';
    unset($_SESSION['producto_agregado']);
}

// Guardar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = trim($_POST['precio'] ?? '');
    $stock = filter_var(
        $_POST['stock'] ?? '',
        FILTER_VALIDATE_INT
    );
    $imagen = trim($_POST['imagen'] ?? '');
    $categoria = filter_var(
        $_POST['categoria_id'] ?? '',
        FILTER_VALIDATE_INT
    );

    $categoriasValidas = array_map(
        'intval',
        array_column($categorias, 'id')
    );

    if (!hash_equals(
        $_SESSION['agpr_csrf'],
        $_POST['csrf'] ?? ''
    )) {
        $error = 'Solicitud no válida.';

    } elseif (
        $nombre === '' ||
        mb_strlen($nombre) > 150 ||
        !preg_match('/^\d{1,8}(\.\d{1,2})?$/D', $precio) ||
        (float)$precio <= 0 ||
        $stock === false ||
        $stock < 0 ||
        $stock > 2147483647 ||
        !in_array($categoria, $categoriasValidas, true)
    ) {
        $error = 'Revisa los datos del producto.';

    } elseif (
        mb_strlen($imagen) > 255 ||
        ($imagen !== '' &&
        (!filter_var($imagen, FILTER_VALIDATE_URL) ||
        !in_array(
            strtolower(parse_url($imagen, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        )))
    ) {
        $error = 'Introduce una URL de imagen válida.';

    } else {

        try {

            $consulta = $conexion->prepare(
                "INSERT INTO productos
                (nombre, descripcion, precio, imagen, stock, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $consulta->execute([
                $nombre,
                $descripcion,
                $precio,
                $imagen !== '' ? $imagen : null,
                $stock,
                $categoria
            ]);

            $_SESSION['producto_agregado'] = true;

            header('Location: agregar_producto.php');
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $error = 'No se pudo guardar el producto.';

        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>AGPR | Agregar producto</title>

    <link rel="icon" href="img/isotipo.png">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pulido.css">

    <style>

        body {
            background: #121212;
            color: #ffffff;
        }

        .contenedor-producto {
            max-width: 750px;
            margin: 160px auto 60px;
            padding: 35px;
            background: #1c1c1c;
            border: 1px solid #383838;
            border-radius: 15px;
            box-sizing: border-box;
        }

        .contenedor-producto h1 {
            margin-bottom: 10px;
            font-size: 32px;
        }

        .etiqueta-admin {
            color: #ed1c24;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .descripcion-admin {
            color: #b5b5b5;
            margin-bottom: 30px;
        }

        .formulario-producto {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .formulario-producto label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-weight: bold;
        }

        .formulario-producto input,
        .formulario-producto textarea,
        .formulario-producto select {
            width: 100%;
            padding: 13px;
            background: #141414;
            border: 1px solid #444;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            box-sizing: border-box;
        }

        .formulario-producto textarea {
            min-height: 110px;
            resize: vertical;
        }

        .boton-guardar {
            background: #e31824;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .boton-guardar:hover {
            background: #b91019;
        }

        .boton-volver {
            display: block;
            margin-top: 15px;
            padding: 15px;
            background: #303030;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
        }

        .mensaje-exito {
            background: #173c29;
            color: #5aee9b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .mensaje-error {
            background: #482024;
            color: #ff7777;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

<header class="encabezado">

    <div class="contenedor-header">

        <a class="marca" href="index.php">
            <img class="logo" src="img/logo.png" alt="AGPR">
        </a>

        <nav class="menu">

            <a href="index.php">Inicio</a>
            <a href="catalogo.php">Productos</a>
            <a href="admin.php">Administración</a>

        </nav>

        <div class="acciones-header">

            <a class="cuenta-header-link" href="cuenta.php">
                Mi cuenta
            </a>

        </div>

    </div>

</header>

<main class="contenedor-producto">

    <span class="etiqueta-admin">
        ADMINISTRACIÓN AGPR
    </span>

    <h1>Agregar producto</h1>

    <p class="descripcion-admin">
        Registra un nuevo producto en el catálogo de AGPR.
    </p>

    <?php if ($exito): ?>

        <div class="mensaje-exito">
            <?= htmlspecialchars($exito) ?>
        </div>

    <?php endif; ?>

    <?php if ($error): ?>

        <div class="mensaje-error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST" class="formulario-producto">

        <input type="hidden" name="csrf"
               value="<?= htmlspecialchars($_SESSION['agpr_csrf']) ?>">

        <label>
            Nombre del producto

            <input type="text"
                   name="nombre"
                   maxlength="150"
                   placeholder="Ejemplo: Casco deportivo MT"
                   required>
        </label>

        <label>
            Descripción

            <textarea name="descripcion"
                      placeholder="Características del producto"></textarea>
        </label>

        <label>
            Precio (MXN)

            <input type="number"
                   name="precio"
                   min="0.01"
                   max="99999999.99"
                   step="0.01"
                   placeholder="1500.00"
                   required>
        </label>

        <label>
            Cantidad disponible

            <input type="number"
                   name="stock"
                   min="0"
                   max="2147483647"
                   value="0"
                   required>
        </label>

        <label>
            Categoría

            <select name="categoria_id" required>

                <option value="">
                    Selecciona una categoría
                </option>

                <?php foreach ($categorias as $categoria): ?>

                    <option value="<?= (int)$categoria['id'] ?>">

                        <?= htmlspecialchars($categoria['nombre']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

        </label>

        <label>
            URL de la imagen

            <input type="url"
                   name="imagen"
                   maxlength="255"
                   placeholder="https://ejemplo.com/casco.jpg">
        </label>

        <button type="submit" class="boton-guardar">
            Guardar producto
        </button>

    </form>

    <a href="admin.php" class="boton-volver">
        Volver al panel de administrador
    </a>

</main>

</body>

</html>