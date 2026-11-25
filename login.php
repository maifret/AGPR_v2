<?php

require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/personal.php';

$error = '';
csrfToken();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!empty($_SESSION['agpr_empleado_id'])) {
        header('Location: admin/index.php');
        exit;
    }

    if (!empty($_SESSION['agpr_usuario_id'])) {
        header('Location: cuenta.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';

    if (!csrfValido($token)) {
        $error = 'Solicitud no válida. Actualiza la página.';
    } else {
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $clave = $_POST['clave'] ?? '';

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || $clave === '') {
            $error = 'Introduce un correo y una contraseña válidos.';
        } else {
            try {
                $empleado = buscarEmpleadoPorCorreo($conexion, $correo);

                if ($empleado) {
                    if (!password_verify($clave, $empleado['contrasena'])) {
                        $error = 'Correo electrónico o contraseña incorrectos.';
                    } elseif ((int) $empleado['activo'] !== 1) {
                        $error = 'Tu acceso de empleado está desactivado.';
                    } else {
                        entrarEmpleado($empleado);
                        header('Location: admin/index.php');
                        exit;
                    }
                } else {
                    $consulta = $conexion->prepare(
                        'SELECT id, nombre, correo, contrasena, rol
                         FROM usuarios
                         WHERE correo = ?
                         LIMIT 1'
                    );
                    $consulta->execute([$correo]);
                    $usuario = $consulta->fetch();

                    if ($usuario && password_verify($clave, $usuario['contrasena'])) {
                        entrarCliente($usuario);
                        header('Location: cuenta.php');
                        exit;
                    }

                    $error = 'Correo electrónico o contraseña incorrectos.';
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'No se pudo iniciar sesión. Si la base es nueva, abre instalar.php.';
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

    <title>AGPR | Iniciar sesión</title>

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

            BIENVENIDO DE NUEVO

        </span>

        <h1>Inicia sesión</h1>

        <p>Accede a tu cuenta y continúa equipando tu ruta.</p>

        <?php if (($_GET['registro'] ?? '') === 'exitoso'): ?>

            <p style="color: #70d68c;">

                Cuenta creada correctamente. Ya puedes iniciar sesión.

            </p>

        <?php endif; ?>

        <?php if ($error): ?>

            <p class="auth-error" role="alert">

                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>

            </p>

        <?php endif; ?>

        <form method="post" action="login.php">

            <input
                type="hidden"
                name="csrf"
                value="<?= htmlspecialchars($_SESSION['agpr_csrf'], ENT_QUOTES, 'UTF-8') ?>"
            >

            <label>

                Correo electrónico

                <input
                    type="email"
                    name="correo"
                    autocomplete="email"
                    required
                    placeholder="tu@correo.com"
                >

            </label>

            <label>

                Contraseña

                <input
                    type="password"
                    name="clave"
                    autocomplete="current-password"
                    required
                    placeholder="Tu contraseña"
                >

            </label>

            <button type="submit" class="btn-principal">

                Entrar a mi cuenta

            </button>

        </form>

        <p>

            ¿Aún no tienes cuenta?

            <a href="registro.php">Crear cuenta</a>

        </p>

        <small>

            Accede con tu correo electrónico y contraseña.

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