<?php

session_name('AGPR_V2_SESSION');
session_start();

require_once __DIR__ . '/conexion.php';

// Verificar que exista una sesión
if (empty($_SESSION['agpr_usuario_id'])) {
    header('Location: login.php');
    exit;
}

try {

    // Comprobar que el usuario sea administrador
    $consulta = $conexion->prepare(
        "SELECT id, nombre, correo, rol
         FROM usuarios
         WHERE id = ?
         LIMIT 1"
    );

    $consulta->execute([
        $_SESSION['agpr_usuario_id']
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || $usuario['rol'] !== 'admin') {
        http_response_code(403);
        exit('Acceso denegado. Solo administradores.');
    }

    // Contar productos
    $totalProductos = $conexion->query(
        "SELECT COUNT(*) FROM productos"
    )->fetchColumn();

    // Contar clientes
    $totalClientes = $conexion->query(
        "SELECT COUNT(*) FROM usuarios
         WHERE rol = 'cliente'"
    )->fetchColumn();

    // Contar categorías
    $totalCategorias = $conexion->query(
        "SELECT COUNT(*) FROM categorias"
    )->fetchColumn();

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(503);
    exit('No se pudo cargar el panel de administrador.');

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AGPR | Panel de administrador</title>

    <link rel="icon" href="img/isotipo.png">

    <link rel="stylesheet" href="css/estilos.css">

    <link rel="stylesheet" href="css/pulido.css">

    <style>

        body {
            background: #111111;
            color: white;
        }

        .panel-admin {
            width: min(1200px, 92%);
            margin: 0 auto;
            padding: 160px 0 60px;
        }

        .panel-admin h1 {
            font-size: 38px;
            margin-bottom: 15px;
            color: white;
        }

        .panel-admin p {
            color: #bbbbbb;
        }

        .estadisticas-admin {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin: 35px 0;
        }

        .tarjeta-admin {
            background: #1d1d1d;
            border: 1px solid #383838;
            border-radius: 14px;
            padding: 30px;
        }

        .tarjeta-admin h2 {
            font-size: 20px;
            color: #dddddd;
            margin-bottom: 15px;
        }

        .numero-admin {
            font-size: 45px;
            font-weight: bold;
            color: #e31b23;
        }

        .acciones-admin {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .boton-admin {
            display: block;
            background: #e31b23;
            color: white;
            padding: 18px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            transition: background 0.2s;
        }

        .boton-admin:hover {
            background: #b9151c;
            color: white;
        }

        .boton-secundario {
            background: #292929;
        }

        .boton-secundario:hover {
            background: #414141;
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

<main class="panel-admin">

    <span class="seccion-etiqueta">
        ADMINISTRACIÓN AGPR
    </span>

    <h1>Panel de administrador</h1>

    <p>
        Bienvenido,
        <?= htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8') ?>.
        Desde aquí puedes administrar tu tienda.
    </p>

    <section class="estadisticas-admin">

        <div class="tarjeta-admin">

            <h2>📦 Total de productos</h2>

            <div class="numero-admin">
                <?= (int) $totalProductos ?>
            </div>

        </div>

        <div class="tarjeta-admin">

            <h2>👥 Total de clientes</h2>

            <div class="numero-admin">
                <?= (int) $totalClientes ?>
            </div>

        </div>

        <div class="tarjeta-admin">

            <h2>📋 Total de categorías</h2>

            <div class="numero-admin">
                <?= (int) $totalCategorias ?>
            </div>

        </div>

    </section>

    <h2>Administrar tienda</h2>

    <section class="acciones-admin">

        <a class="boton-admin" href="agregar_producto.php">
            ➕ Agregar producto
        </a>

        <a class="boton-admin" href="administrar_productos.php">
            📦 Administrar productos
        </a>

        <a class="boton-admin boton-secundario" href="catalogo.php">
            Ver catálogo
        </a>

        <a class="boton-admin boton-secundario" href="cuenta.php">
            Volver a mi cuenta
        </a>

    </section>

</main>

<footer class="footer-agpr">

    <div class="footer-agpr-base">

        © 2026 AGPR · Equipa tu ruta

        <span>Panel de administración</span>

    </div>

</footer>

</body>

</html>