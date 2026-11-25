<?php

require_once __DIR__ . '/includes/sesion.php';
require_once __DIR__ . '/conexion.php';

if (!empty($_SESSION['agpr_empleado_id']) && empty($_SESSION['agpr_usuario_id'])) {
    header('Location: admin/index.php');
    exit;
}

if (empty($_SESSION['agpr_usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Generar token de seguridad
$_SESSION['agpr_csrf'] ??= bin2hex(random_bytes(32));

// Cerrar sesión de forma segura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salir'])) {

    $token = $_POST['csrf'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['agpr_csrf'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    unset(
        $_SESSION['agpr_usuario_id'],
        $_SESSION['agpr_usuario'],
        $_SESSION['agpr_rol'],
        $_SESSION['agpr_demo_cliente'],
        $_SESSION['agpr_empleado_id'],
        $_SESSION['agpr_empleado_nombre'],
        $_SESSION['agpr_empleado_puesto'],
        $_SESSION['agpr_csrf']
    );

    session_regenerate_id(true);

    header('Location: login.php');
    exit;
}

try {

    // Obtener los datos reales del usuario
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

    if (!$usuario) {

        unset(
            $_SESSION['agpr_usuario_id'],
            $_SESSION['agpr_usuario'],
            $_SESSION['agpr_rol']
        );

        header('Location: login.php');
        exit;
    }

    $nombre = $usuario['nombre'];
    $correo = $usuario['correo'];
    $rol = $usuario['rol'];

    $_SESSION['agpr_usuario'] = $nombre;
    $_SESSION['agpr_rol'] = $rol;

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(503);
    exit('No se pudo consultar la cuenta.');
}

function escapar($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Mi cuenta | AGPR</title>

    <link
        rel="icon"
        type="image/png"
        href="img/isotipo.png">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pulido.css">

    <style>
        .cuenta-contenedor {
            max-width: 1150px;
            margin: 45px auto 80px;
            padding: 0 20px;
            color: #f5f5f5;
            font-family: 'Inter', sans-serif;
        }

        .cuenta-encabezado {
            margin-bottom: 30px;
        }

        .cuenta-etiqueta {
            color: #e32735;
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .cuenta-encabezado h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: clamp(36px, 5vw, 52px);
            margin: 10px 0;
        }

        .cuenta-encabezado p {
            color: #aaaab1;
            line-height: 1.7;
        }

        .cuenta-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            align-items: start;
        }

        .cuenta-panel {
            background: #1b1b1e;
            border: 1px solid #38383e;
            border-radius: 14px;
            padding: 26px;
        }

        .cuenta-perfil {
            text-align: center;
        }

        .cuenta-avatar {
            width: 88px;
            height: 88px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #e32735;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Rajdhani', sans-serif;
            font-size: 42px;
            font-weight: 700;
            color: white;
        }

        .cuenta-perfil h2 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 29px;
            margin: 0 0 8px;
            overflow-wrap: anywhere;
        }

        .cuenta-correo {
            color: #aaaab1;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .cuenta-estado {
            display: inline-block;
            margin-top: 18px;
            padding: 8px 14px;
            border-radius: 30px;
            background: rgba(91,224,161,.1);
            color: #5be0a1;
            font-size: 12px;
            font-weight: 700;
        }

        .cuenta-enlaces {
            margin-top: 26px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cuenta-enlaces a {
            display: block;
            padding: 13px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            background: #28282d;
            color: white;
            transition: background .2s;
        }

        .cuenta-enlaces a:hover {
            background: #414148;
        }

        .cuenta-enlaces .salir {
            background: rgba(227,39,53,.12);
            color: #ff6971;
        }

        .cuenta-enlaces .salir:hover {
            background: rgba(227,39,53,.25);
        }

        .cuenta-contenido h2 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 28px;
            margin: 0 0 20px;
        }

        .cuenta-tarjetas {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .cuenta-tarjeta {
            background: #202024;
            border: 1px solid #37373d;
            border-radius: 12px;
            padding: 24px;
            transition: border-color .2s;
        }

        .cuenta-tarjeta:hover {
            border-color: #e32735;
        }

        .cuenta-icono {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(227,39,53,.12);
            color: #ff6971;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .cuenta-tarjeta h3 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 24px;
            margin: 0 0 10px;
        }

        .cuenta-tarjeta p {
            color: #aaaab1;
            font-size: 13px;
            line-height: 1.7;
            margin: 0 0 15px;
        }

        .cuenta-proximamente {
            display: inline-block;
            font-size: 11px;
            color: #ffb85c;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .cuenta-aviso {
            margin-top: 22px;
            padding: 18px;
            background: rgba(227,39,53,.07);
            border: 1px solid rgba(227,39,53,.3);
            border-radius: 10px;
            color: #d2d2d7;
            font-size: 13px;
            line-height: 1.8;
        }

        @media (max-width: 800px) {
            .cuenta-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .cuenta-tarjetas {
                grid-template-columns: 1fr;
            }

            .cuenta-panel {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <header class="encabezado">
        <div class="contenedor-header">

            <a class="marca" href="index.php">
                <img
                    class="logo"
                    src="img/logo.png"
                    alt="AGPR">
            </a>

            <nav
                class="menu"
                aria-label="Navegación principal">

                <a href="index.php">Inicio</a>

                <a href="catalogo.php">
                    Productos
                </a>

                <a href="index.php#nosotros">
                    Nosotros
                </a>

                <a href="index.php#contacto">
                    Contacto
                </a>

            </nav>

            <div class="acciones-header">
                <a
                    class="cuenta-header-link"
                    href="cuenta.php">
                    Mi cuenta
                </a>
            </div>

        </div>
    </header>

    <main class="cuenta-contenedor">

        <div class="cuenta-encabezado">

            <span class="cuenta-etiqueta">
                AGPR / MI CUENTA
            </span>

            <h1>
                ¡BIENVENIDO A TU RUTA!
            </h1>

            <p>
                Administra tu perfil y explora las
                próximas funciones de tu cuenta AGPR.
            </p>

        </div>

        <div class="cuenta-grid">

            <!-- PERFIL DEL CLIENTE -->

            <aside class="cuenta-panel cuenta-perfil">

                <div class="cuenta-avatar">
                    <?= escapar(
                        strtoupper(
                            substr($nombre, 0, 1)
                        )
                    ) ?>
                </div>

                <h2>
                    <?= escapar($nombre) ?>
                </h2>

  <p class="cuenta-correo">
    <?= escapar($correo) ?>
</p>

<p class="cuenta-correo" style="margin-top: 12px;">
    Tipo de cuenta:
    <strong>
        <?= $rol === 'admin' ? 'Administrador' : 'Cliente' ?>
    </strong>
</p>

                <span class="cuenta-estado">
                    ● Sesión iniciada
                </span>
<div class="cuenta-enlaces">

    <a href="catalogo.php">
        Explorar productos
    </a>

    <form method="POST" action="cuenta.php">

        <input type="hidden" name="salir" value="1">

        <input
            type="hidden"
            name="csrf"
            value="<?= escapar($_SESSION['agpr_csrf']) ?>"
        >

        <button
            type="submit"
            class="salir"
            style="
                width: 100%;
                padding: 13px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
            "
        >
            Cerrar sesión
        </button>

    </form>

</div>
            </aside>

            <!-- FUNCIONES DEL CLIENTE -->

            <section class="cuenta-panel cuenta-contenido">

                <h2>Panel del motociclista</h2>

                <div class="cuenta-tarjetas">

                    <article class="cuenta-tarjeta">

                        <span class="cuenta-icono">
                            📦
                        </span>

                        <h3>Mis pedidos</h3>

                        <p>
                            Consulta el historial de tus
                            compras y revisa su estado.
                        </p>

                        <span class="cuenta-proximamente">
                            PRÓXIMAMENTE
                        </span>

                    </article>

                    <article class="cuenta-tarjeta">

                        <span class="cuenta-icono">
                            📍
                        </span>

                        <h3>Mis direcciones</h3>

                        <p>
                            Guarda y administra tus
                            direcciones de entrega.
                        </p>

                        <span class="cuenta-proximamente">
                            PRÓXIMAMENTE
                        </span>

                    </article>

                    <article class="cuenta-tarjeta">

                        <span class="cuenta-icono">
                            ❤️
                        </span>

                        <h3>Mis favoritos</h3>

                        <p>
                            Encuentra rápidamente los
                            accesorios que más te gustan.
                        </p>

                        <span class="cuenta-proximamente">
                            PRÓXIMAMENTE
                        </span>

                    </article>

                    <article class="cuenta-tarjeta">

                        <span class="cuenta-icono">
                            ⚙️
                        </span>

                        <h3>Configuración</h3>

                        <p>
                            Actualiza tus datos personales
                            y preferencias de cuenta.
                        </p>

                        <span class="cuenta-proximamente">
                            PRÓXIMAMENTE
                        </span>

                    </article>

                </div>
<div class="cuenta-aviso">

    <strong>Bienvenido a AGPR:</strong>

    Tu cuenta está registrada en nuestra base de datos.
    Próximamente podrás consultar tus pedidos,
    administrar tus direcciones y guardar tus
    productos favoritos.

</div>

            </section>

        </div>

    </main>

    <footer class="footer-agpr">

        <div class="footer-agpr-grid">

            <div class="footer-marca">

                <img
                    src="img/logo.png"
                    alt="AGPR">

                <p>
                    Equipa tu ruta. Accesorios para
                    acompañarte en cada kilómetro.
                </p>

                <div
                    class="footer-redes"
                    aria-label="Redes sociales ilustrativas">

                    <span title="Instagram">◎</span>
                    <span title="Facebook">f</span>
                    <span title="WhatsApp">✆</span>

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

                <a href="catalogo.php?categoria=cascos">
                    Cascos
                </a>

                <a href="catalogo.php?categoria=guantes">
                    Guantes
                </a>

                <a href="catalogo.php?categoria=soportes">
                    Soportes
                </a>

                <a href="catalogo.php?categoria=intercomunicadores">
                    Intercomunicadores
                </a>
            </div>

            <div>
                <h3>Atención</h3>

                <p>Morelia, Michoacán</p>
                <p>Lun–Sáb · 9:00 a 18:00</p>
                <p>contacto@agpr.example</p>

                <a href="login.php">Mi cuenta</a>

                <small>
                    Datos de demostración
                </small>
            </div>

        </div>

        <div class="footer-agpr-base">

            © 2026 AGPR · Equipa tu ruta

            <span>
                Proyecto académico · Tienda de demostración
            </span>

        </div>

    </footer>

    <script src="js/script.js"></script>

</body>
</html>
