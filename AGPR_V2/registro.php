<?php
session_name('AGPR_V2_SESSION');
session_start();

require_once __DIR__ . '/conexion.php';

$error = '';

$_SESSION['agpr_csrf'] ??= bin2hex(random_bytes(32));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['agpr_csrf'], $token)) {

        $error = 'Solicitud no válida. Actualiza la página.';

    } else {

        $nombre = trim($_POST['nombre'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $clave = $_POST['clave'] ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        if (
            mb_strlen($nombre) < 2 ||
            mb_strlen($nombre) > 80 ||
            !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            strlen($correo) > 150 ||
            strlen($clave) < 8 ||
            strlen($clave) > 72 ||
            $clave !== $confirmar
        ) {

            $error = 'Revisa tus datos. Utiliza un correo válido y una contraseña de entre 8 y 72 bytes.';

        } else {

            try {

                // Verificar si el correo ya existe
                $consulta = $conexion->prepare(
                    "SELECT id FROM usuarios WHERE correo = ? LIMIT 1"
                );

                $consulta->execute([$correo]);

                if ($consulta->fetch()) {

                    $error = 'Este correo electrónico ya está registrado.';

                } else {

                    // Proteger la contraseña
                    $hash = password_hash($clave, PASSWORD_DEFAULT);

                    // Registrar al usuario en MySQL
                    $registro = $conexion->prepare(
                        "INSERT INTO usuarios
                        (nombre, correo, contrasena, rol)
                        VALUES (?, ?, ?, 'cliente')"
                    );

                    $registro->execute([
                        $nombre,
                        $correo,
                        $hash
                    ]);

                    // Enviar al usuario al inicio de sesión
                    header('Location: login.php?registro=exitoso');
                    exit;

                }

            } catch (PDOException $e) {

                error_log($e->getMessage());

                if ($e->getCode() === '23000') {
                    $error = 'Este correo electrónico ya está registrado.';
                } else {
                    $error = 'No se pudo crear la cuenta. Inténtalo nuevamente.';
                }

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AGPR | Crear cuenta</title>

    <link rel="icon" href="img/isotipo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">

    <link rel="stylesheet" href="css/pulido.css">

</head>

<body>

<header class="encabezado">

    <div class="contenedor-header">

        <a class="marca" href="index.php">
            <img class="logo" src="img/logo.png" alt="AGPR">
        </a>

        <nav class="menu" aria-label="Navegación principal">

            <a href="index.php">Inicio</a>

            <a href="catalogo.php">Productos</a>

            <a href="index.php#nosotros">Nosotros</a>

            <a href="index.php#contacto">Contacto</a>

        </nav>

        <div class="acciones-header">

            <a class="cuenta-header-link" href="login.php">
                Mi cuenta
            </a>

        </div>

    </div>

</header>

<main class="auth-page">

    <section class="auth-card">

        <div class="auth-logo">
            ✦ AGPR / CLIENTES
        </div>

        <span class="seccion-etiqueta">
            ÚNETE A LA RUTA
        </span>

        <h1>Crea tu cuenta</h1>

        <p>Regístrate y forma parte de la comunidad AGPR.</p>

        <?php if ($error): ?>

            <p class="auth-error" role="alert">

                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

            </p>

        <?php endif; ?>

        <form method="post" action="registro.php">

            <input
                type="hidden"
                name="csrf"
                value="<?= htmlspecialchars($_SESSION['agpr_csrf'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <label>
                Nombre completo

                <input
                    type="text"
                    name="nombre"
                    autocomplete="name"
                    minlength="2"
                    maxlength="80"
                    required
                >

            </label>

            <label>
                Correo electrónico

                <input
                    type="email"
                    name="correo"
                    autocomplete="email"
                    maxlength="150"
                    required
                >

            </label>

            <label>
                Contraseña

                <input
                    type="password"
                    name="clave"
                    autocomplete="new-password"
                    minlength="8"
                    maxlength="72"
                    required
                >

            </label>

            <label>
                Confirmar contraseña

                <input
                    type="password"
                    name="confirmar"
                    autocomplete="new-password"
                    minlength="8"
                    maxlength="72"
                    required
                >

            </label>

            <button type="submit" class="btn-principal">
                Crear cuenta
            </button>

        </form>

        <p>
            ¿Ya tienes cuenta?
            <a href="login.php">Iniciar sesión</a>
        </p>

        <small>
            Tus datos se almacenan en nuestra base de datos.
        </small>

    </section>

</main>

<footer class="footer-agpr">

    <div class="footer-agpr-grid">

        <div class="footer-marca">

            <img src="img/logo.png" alt="AGPR">

            <p>
                Equipa tu ruta. Accesorios para acompañarte
                en cada kilómetro.
            </p>

            <div class="footer-redes" aria-label="Redes sociales ilustrativas">

                <span title="Instagram (demostración)" aria-label="Instagram">◎</span>

                <span title="Facebook (demostración)" aria-label="Facebook">f</span>

                <span title="WhatsApp (demostración)" aria-label="WhatsApp">✆</span>

            </div>

        </div>

        <div>

            <h3>Explora</h3>

            <a href="index.php">Inicio</a>

            <a href="catalogo.php">Catálogo</a>

            <a href="index.php#nosotros">Nosotros</a>

            <a href="index.php#contacto">Contacto</a>

        </div>

        <div>

            <h3>Categorías</h3>

            <a href="catalogo.php?categoria=cascos">Cascos</a>

            <a href="catalogo.php?categoria=guantes">Guantes</a>

            <a href="catalogo.php?categoria=soportes">Soportes</a>

            <a href="catalogo.php?categoria=intercomunicadores">Intercomunicadores</a>

        </div>

        <div>

            <h3>Atención</h3>

            <p>Morelia, Michoacán</p>

            <p>Lun–Sáb · 9:00 a 18:00</p>

            <p>contacto@agpr.example</p>

            <a href="login.php">Mi cuenta</a>

            <small>Datos de demostración</small>

        </div>

    </div>

    <div class="footer-agpr-base">

        © 2026 AGPR · Equipa tu ruta

        <span>Proyecto académico · Tienda de demostración</span>

    </div>

</footer>

<script src="js/script.js"></script>

</body>

</html>