<?php

define('AGPR_SIN_CONEXION', true);
require_once __DIR__ . '/conexion.php';

$mensaje = '';
$error = '';
$adminCreado = false;
const AGPR_ADMIN_CORREO = 'admin@agpr.local';
const AGPR_ADMIN_CLAVE = 'AdminAgpr2026';

function sentenciasDeEsquema(string $ruta): array
{
    $sql = file_get_contents($ruta);
    $sql = preg_replace('/^\s*--.*$/m', '', $sql);
    $partes = explode(';', $sql);
    $sentencias = [];

    foreach ($partes as $parte) {
        $parte = trim($parte);
        if ($parte !== '') {
            $sentencias[] = $parte;
        }
    }

    return $sentencias;
}

function sembrarProductos(PDO $db): void
{
    $hay = (int) $db->query('SELECT COUNT(*) FROM productos')->fetchColumn();
    if ($hay > 0) {
        return;
    }

    $categorias = [];
    foreach ($db->query('SELECT id, clave FROM categorias') as $fila) {
        $categorias[$fila['clave']] = (int) $fila['id'];
    }

    $productos = [
        ['Casco Integral Sport', 'cascos', 1899, 'img/cascos.jpg', 'Diseño deportivo para tus recorridos.', 'Un casco de diseño deportivo pensado para quienes buscan una apariencia moderna durante sus recorridos.', "Diseño integral de estilo deportivo\nAcabado moderno\nUso orientativo: recorridos urbanos y carretera", 'oferta', 8],
        ['Casco Urbano Pro', 'cascos', 1499, 'img/cascos.jpg', 'Una opción práctica para la ciudad.', 'Una opción de estilo urbano para acompañarte en tus trayectos cotidianos.', "Diseño urbano\nAcabado moderno\nPensado para recorridos cotidianos", 'nuevo', 6],
        ['Guantes Racing', 'guantes', 649, 'img/guantes.jpg', 'Estilo deportivo y comodidad.', 'Guantes de apariencia deportiva para complementar tu equipamiento de motociclista.', "Estilo deportivo\nDiseño para motociclistas\nAcabado de demostración", 'mas-vendido', 12],
        ['Guantes Urbanos', 'guantes', 449, 'img/guantes.jpg', 'Para el uso diario en motocicleta.', 'Guantes de estilo urbano para quienes buscan completar su equipo de uso diario.', "Diseño urbano\nEstilo discreto\nPensados para uso cotidiano", 'destacado', 10],
        ['Soporte Universal', 'soportes', 299, 'img/soportes.jpg', 'Mantén tu celular a la vista.', 'Un soporte para celular pensado para facilitar la consulta de navegación durante tus viajes.', "Diseño para celular\nEstilo práctico\nMontaje sencillo", 'oferta', 15],
        ['Soporte Ajustable Pro', 'soportes', 399, 'img/soportes.jpg', 'Montaje práctico para tus recorridos.', 'Una alternativa de soporte ajustable para organizar mejor tus accesorios de viaje.', "Diseño ajustable\nEstilo moderno\nMontaje práctico", 'nuevo', 9],
        ['Intercomunicador Rider', 'intercomunicadores', 1299, 'img/intercomunicadores.jpg', 'Comunicación para tus aventuras.', 'Un intercomunicador para explorar opciones de comunicación durante tus recorridos.', "Diseño para motociclistas\nComunicación durante viajes\nUso individual", 'destacado', 5],
        ['Intercomunicador Duo', 'intercomunicadores', 2199, 'img/intercomunicadores.jpg', 'Una opción para viajar acompañado.', 'Una propuesta de intercomunicador orientada a quienes disfrutan viajar acompañados.', "Comunicación en pareja\nDiseño para motociclistas\nUso en ruta", 'mas-vendido', 4],
    ];

    $insertar = $db->prepare(
        'INSERT INTO productos
            (categoria_id, nombre, precio, imagen, descripcion, detalle, caracteristicas, etiqueta, stock, activo)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
    );

    foreach ($productos as $producto) {
        $insertar->execute([
            $categorias[$producto[1]],
            $producto[0],
            $producto[2],
            $producto[3],
            $producto[4],
            $producto[5],
            $producto[6],
            $producto[7],
            $producto[8],
        ]);
    }
}

function asegurarAdministrador(PDO $db): bool
{
    $hay = (int) $db->query('SELECT COUNT(*) FROM empleados')->fetchColumn();
    if ($hay > 0) {
        return false;
    }

    $puesto = $db->query("SELECT id FROM puestos WHERE nombre = 'Administrador'")->fetchColumn();
    $consulta = $db->prepare(
        'INSERT INTO empleados (nombre, correo, contrasena, puesto_id, activo)
         VALUES (?, ?, ?, ?, 1)'
    );
    $consulta->execute([
        'Administrador AGPR',
        AGPR_ADMIN_CORREO,
        password_hash(AGPR_ADMIN_CLAVE, PASSWORD_DEFAULT),
        $puesto,
    ]);

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!preg_match('/^[A-Za-z0-9_]+$/', $base_datos)) {
        $error = 'El nombre de la base de datos no es válido.';
    } else {
        try {
            $servidorAdmin = new PDO(
                "mysql:host=$servidor;charset=utf8mb4",
                $usuario,
                $contrasena
            );
            $servidorAdmin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $servidorAdmin->exec(
                "CREATE DATABASE IF NOT EXISTS `$base_datos` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );

            $db = new PDO(
                "mysql:host=$servidor;dbname=$base_datos;charset=utf8mb4",
                $usuario,
                $contrasena
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            foreach (sentenciasDeEsquema(__DIR__ . '/sql/esquema.sql') as $sentencia) {
                $db->exec($sentencia);
            }

            sembrarProductos($db);
            $adminCreado = asegurarAdministrador($db);
            $mensaje = $adminCreado
                ? 'La base quedó lista y se creó el administrador inicial.'
                : 'La base ya tenía empleados. Se actualizaron las tablas sin cambiar las cuentas.';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'No se pudo preparar la base. Revisa que MySQL esté encendido y que el usuario de conexion.php tenga permiso para crear la base.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalar AGPR</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Rajdhani:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">
<main class="admin-main">
    <section class="admin-panel">
        <span class="admin-kicker">AGPR</span>
        <h1>Preparar base de datos</h1>
        <p class="admin-nota">Crea las tablas de productos, empleados, puestos y privilegios. No borra cuentas que ya existan.</p>

        <?php if ($mensaje): ?>
            <p class="admin-flash admin-flash-ok"><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="admin-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($adminCreado): ?>
            <p>Entra al panel con estos datos y cambia la contraseña después:</p>
            <p><strong>Correo:</strong> <?= AGPR_ADMIN_CORREO ?><br>
            <strong>Contraseña:</strong> <?= AGPR_ADMIN_CLAVE ?></p>
            <p><a class="admin-btn" href="login.php">Ir a iniciar sesión</a></p>
        <?php else: ?>
            <form method="post">
                <button class="admin-btn" type="submit">Instalar ahora</button>
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
