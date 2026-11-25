
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AGPR: accesorios para motociclistas. Equipa tu ruta con estilo, comodidad y seguridad.">
    <title>AGPR | Equipa tu ruta</title>

    <link rel="icon" type="image/png" href="img/isotipo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pulido.css">

    <style>
        /* RAIDER: tarjeta de presentación en Nosotros */

        .nosotros-imagen.tarjeta-raider {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            min-height: 430px;
            padding: 0;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid #4c3033;
            border-radius: 20px;
            background:
                radial-gradient(
                    ellipse at 50% 43%,
                    rgba(180, 24, 34, .25),
                    transparent 65%
                ),
                linear-gradient(145deg, #19191c, #09090b);
            cursor: pointer;
            transition:
                border-color .25s ease,
                box-shadow .25s ease,
                transform .25s ease;
        }

        .tarjeta-raider:hover,
        .tarjeta-raider:focus-visible {
            border-color: #e32630;
            box-shadow: 0 12px 38px rgba(175, 15, 28, .22);
            transform: translateY(-3px);
        }

        .tarjeta-raider:focus-visible {
            outline: 2px solid #ff5962;
            outline-offset: 4px;
        }

        .tarjeta-raider::before {
            content: "";
            position: absolute;
            width: 280px;
            height: 280px;
            top: 32px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 50%;
            background: rgba(205, 20, 35, .15);
            filter: blur(65px);
            pointer-events: none;
        }

        /* La imagen NO puede salir de su tarjeta */
        .nosotros-imagen.tarjeta-raider .raider-presentacion {
            position: relative !important;
            inset: auto !important;
            display: block !important;
            width: auto !important;
            height: 345px !important;
            max-width: 95% !important;
            max-height: 345px !important;
            min-width: 0 !important;
            min-height: 0 !important;
            object-fit: contain !important;
            object-position: bottom center;
            flex-shrink: 1;
            filter: drop-shadow(0 12px 20px rgba(0, 0, 0, .5));
            transition: transform .3s ease;
        }

        .tarjeta-raider:hover .raider-presentacion {
            transform: scale(1.035);
        }

        .tarjeta-raider .raider-informacion {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 16px 15px 22px;
            text-align: center;
            background: linear-gradient(
                0deg,
                #0b0b0e 55%,
                rgba(11, 11, 14, .85) 85%,
                transparent
            );
        }

        .tarjeta-raider .raider-nombre {
            position: static;
            display: block;
            font-family: 'Rajdhani', sans-serif;
            font-size: 32px;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: 3px;
            color: #fff;
        }

        .tarjeta-raider .raider-subtitulo {
            position: static;
            display: block;
            margin-top: 3px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: .3px;
            color: #ff777e;
        }

        .tarjeta-raider .raider-accion {
            position: static;
            display: inline-block;
            margin-top: 12px;
            padding: 8px 17px;
            border: 1px solid #b82a32;
            border-radius: 20px;
            color: #fff;
            background: rgba(153, 23, 32, .28);
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        @media (max-width: 600px) {
            .nosotros-imagen.tarjeta-raider {
                min-height: 365px;
            }

            .nosotros-imagen.tarjeta-raider .raider-presentacion {
                height: 280px !important;
                max-height: 280px !important;
            }

            .tarjeta-raider .raider-nombre {
                font-size: 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .tarjeta-raider,
            .tarjeta-raider .raider-presentacion {
                transition: none;
            }
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <header class="encabezado">
        <div class="contenedor-header">

            <a href="index.php" class="marca" aria-label="AGPR - Inicio">
                <img src="img/logo.png" alt="Logotipo AGPR" class="logo">
            </a>

            <nav class="menu" aria-label="Navegación principal">
                <a href="index.php" class="activo">Inicio</a>
                <a href="catalogo.php">Productos</a>
                <a href="index.php#nosotros">Nosotros</a>
                <a href="index.php#contacto">Contacto</a>
            </nav>

            <div class="acciones-header">
                <form class="form-buscador" action="catalogo.php" method="get" role="search">
                    <input
                        type="search"
                        name="buscar"
                        id="buscador"
                        placeholder="Buscar productos..."
                        aria-label="Buscar productos">
                </form>

                <button
                    type="button"
                    class="boton-icono"
                    id="btn-cuenta"
                    aria-label="Mi cuenta"
                    title="Mi cuenta">
                    &#9786;
                </button>

                <button
                    type="button"
                    class="boton-icono boton-carrito"
                    id="abrir-carrito"
                    aria-label="Abrir carrito"
                    title="Carrito de compras">
                    &#128722;
                    <span id="contador-carrito" class="contador-carrito">0</span>
                </button>
            </div>
        </div>
    </header>

    <main>

        <!-- PORTADA -->
        <section id="inicio" class="hero">
            <div class="hero-contenido">
                <span class="hero-etiqueta">ACCESORIOS PARA MOTOCICLETAS</span>

                <h1>EQUIPA <span>TU RUTA</span></h1>

                <p>
                    Calidad, estilo y rendimiento en cada kilómetro.
                    Encuentra los mejores accesorios para tu motocicleta.
                </p>

                <a href="catalogo.php" class="btn-principal">
                    Explorar productos
                </a>
            </div>
        </section>

        <!-- CATEGORÍAS -->
        <section id="productos" class="categorias">

            <span class="seccion-etiqueta">ENCUENTRA TU EQUIPAMIENTO</span>
            <h2>Nuestras categorías</h2>

            <div class="contenedor-categorias">

                <article class="categoria">
                    <div class="categoria-imagen">
                        <img src="img/cascos.jpg" alt="Cascos para motociclistas" loading="lazy">
                    </div>
                    <div class="categoria-contenido">
                        <h3>Cascos</h3>
                        <p>Protección y estilo para cada viaje.</p>
                        <a href="catalogo.php?categoria=cascos">
                            Ver productos
                        </a>
                    </div>
                </article>

                <article class="categoria">
                    <div class="categoria-imagen">
                        <img src="img/guantes.jpg" alt="Guantes para motociclistas" loading="lazy">
                    </div>
                    <div class="categoria-contenido">
                        <h3>Guantes</h3>
                        <p>Comodidad y seguridad en tus manos.</p>
                        <a href="catalogo.php?categoria=guantes">
                            Ver productos
                        </a>
                    </div>
                </article>

                <article class="categoria">
                    <div class="categoria-imagen">
                        <img src="img/soportes.jpg" alt="Soportes para celular" loading="lazy">
                    </div>
                    <div class="categoria-contenido">
                        <h3>Soportes</h3>
                        <p>Accesorios prácticos para tu celular.</p>
                        <a href="catalogo.php?categoria=soportes">
                            Ver productos
                        </a>
                    </div>
                </article>

                <article class="categoria">
                    <div class="categoria-imagen">
                        <img src="img/intercomunicadores.jpg" alt="Intercomunicadores para motociclistas" loading="lazy">
                    </div>
                    <div class="categoria-contenido">
                        <h3>Intercomunicadores</h3>
                        <p>Comunicación durante tus recorridos.</p>
                        <a href="catalogo.php?categoria=intercomunicadores">
                            Ver productos
                        </a>
                    </div>
                </article>

            </div>

            <a href="catalogo.php" class="btn-principal btn-ver-catalogo">
                Ver catálogo completo
            </a>
        </section>

        <!-- BENEFICIOS -->
        <section class="beneficios">
            <div>
                <h3>Envíos seguros</h3>
                <p>A todo México</p>
            </div>
            <div>
                <h3>Productos de calidad</h3>
                <p>Accesorios seleccionados</p>
            </div>
            <div>
                <h3>Asesoría personalizada</h3>
                <p>Te ayudamos a elegir</p>
            </div>
            <div>
                <h3>Compras seguras</h3>
                <p>Tu confianza es importante</p>
            </div>
        </section>

        <!-- NOSOTROS Y PRESENTACIÓN DE RAIDER -->
        <section id="nosotros" class="nosotros nosotros-premium">
            <div class="seccion-contenedor nosotros-grid">

                <!-- TARJETA INTERACTIVA DE RAIDER -->
                <button
                    type="button"
                    id="presentacion-raider"
                    class="nosotros-imagen tarjeta-raider"
                    aria-label="Conoce a Raider y abre el asistente virtual"
                    title="Habla con Raider">

                    <img
                        src="img/raider.png"
                        alt="Raider, pantera motociclista de AGPR"
                        class="raider-presentacion"
                        loading="lazy">

                    <div class="raider-informacion">
                        <strong class="raider-nombre">RAIDER</strong>
                        <small class="raider-subtitulo">
                            Tu copiloto inteligente
                        </small>
                        <span class="raider-accion">
                            HABLA CON RAIDER
                        </span>
                    </div>
                </button>

                <!-- HISTORIA DE AGPR -->
                <div class="nosotros-texto">
                    <span class="seccion-etiqueta">
                        NUESTRA PASIÓN
                    </span>

                    <h2>
                        Más que accesorios,
                        <em>pasión por rodar.</em>
                    </h2>

                    <p>
                        AGPR nace de una idea sencilla: que cada
                        motociclista encuentre el equipamiento ideal
                        para disfrutar cada kilómetro con confianza,
                        comodidad y estilo.
                    </p>

                    <p>
                        Reunimos cascos, guantes, soportes e
                        intercomunicadores en un solo lugar.
                        Nuestro objetivo es hacer que elegir tu
                        próximo accesorio sea tan emocionante
                        como preparar tu siguiente ruta.
                    </p>

                    <a class="btn-principal" href="catalogo.php">
                        Descubre el catálogo
                    </a>
                </div>

            </div>
        </section>

        <!-- CONTACTO -->
        <section id="contacto" class="contacto contacto-premium">
            <div class="seccion-contenedor">

                <div class="contacto-titulo">
                    <span class="seccion-etiqueta">
                        HABLEMOS DE TU PRÓXIMA RUTA
                    </span>
                    <h2>Estamos para ayudarte</h2>
                    <p>
                        ¿Tienes dudas sobre un accesorio?
                        Escríbenos y conoce nuestros canales
                        de atención de demostración.
                    </p>
                </div>

                <div class="contacto-grid">
                    <div class="contacto-info">

                        <div class="contacto-dato">
                            <span>✉</span>
                            <div>
                                <strong>Correo electrónico</strong>
                                <p>agpr@gmail.com</p>
                            </div>
                        </div>

                        <div class="contacto-dato">
                            <span>⌖</span>
                            <div>
                                <strong>Ubicación</strong>
                                <p>Morelia, Michoacán, México</p>
                            </div>
                        </div>

                        <div class="contacto-dato">
                            <span>◷</span>
                            <div>
                                <strong>Horario de atención</strong>
                                <p>Lunes a sábado · 9:00 a 18:00</p>
                            </div>
                        </div>

                        <p class="contacto-aclaracion"></p>
                    </div>

                    <form
                        class="contacto-form"
                        id="form-contacto"
                        novalidate>

                        <h3>Envíanos un mensaje</h3>

                        <div class="contacto-campos">
                            <label>
                                Nombre completo
                                <input
                                    type="text"
                                    name="nombre"
                                    required
                                    maxlength="80"
                                    placeholder="Tu nombre">
                            </label>

                            <label>
                                Correo electrónico
                                <input
                                    type="email"
                                    name="correo"
                                    required
                                    maxlength="120"
                                    placeholder="nombre@correo.com">
                            </label>
                        </div>

                        <label>
                            Asunto
                            <input
                                type="text"
                                name="asunto"
                                required
                                maxlength="120"
                                placeholder="¿En qué podemos ayudarte?">
                        </label>

                        <label>
                            Mensaje
                            <textarea
                                name="mensaje"
                                rows="5"
                                required
                                maxlength="1000"
                                placeholder="Cuéntanos qué necesitas..."></textarea>
                        </label>

                        <button type="submit" class="btn-principal">
                            Revisar mensaje
                        </button>

                        <p
                            class="contacto-estado"
                            id="contacto-estado"
                            role="status"
                            aria-live="polite"></p>
                    </form>
                </div>
            </div>
        </section>

    </main>

    <!-- CARRITO -->
    <div id="fondo-carrito" class="fondo-carrito" hidden></div>

    <aside
        id="panel-carrito"
        class="panel-carrito"
        role="dialog"
        aria-modal="true"
        aria-labelledby="titulo-carrito"
        aria-hidden="true"
        tabindex="-1">

        <div class="carrito-encabezado">
            <h2 id="titulo-carrito">Tu carrito</h2>

            <button
                type="button"
                id="cerrar-carrito"
                aria-label="Cerrar carrito">
                &times;
            </button>
        </div>

        <div id="productos-carrito" class="productos-carrito"></div>

        <div class="carrito-resumen">
            <div class="carrito-total">
                <span>Subtotal</span>
                <strong id="total-carrito">$0 MXN</strong>
            </div>

            <p>Precios de demostración. Envío no incluido.</p>

            <button
                type="button"
                id="finalizar-compra"
                class="btn-principal btn-finalizar">
                Finalizar compra
            </button>
        </div>
    </aside>

    <!-- BOTÓN FLOTANTE DE RAIDER -->
    <div class="asistente-ia">
        <button
            type="button"
            class="boton-asistente"
            aria-label="Abrir Raider"
            title="Raider, tu copiloto inteligente">
            &#128172;
        </button>
    </div>

    <!-- NOTIFICACIONES -->
    <div
        id="notificacion"
        class="notificacion"
        role="status"
        aria-live="polite"></div>

    <!-- PIE DE PÁGINA -->
    <footer class="footer-agpr">
        <div class="footer-agpr-grid">

            <div class="footer-marca">
                <img src="img/logo.png" alt="AGPR">

                <p>
                    Equipa tu ruta. Accesorios para acompañarte
                    en cada kilómetro.
                </p>

                <div
                    class="footer-redes"
                    aria-label="Redes sociales ilustrativas">

                    <span
                        title="Instagram (demostración)"
                        aria-label="Instagram">◎</span>

                    <span
                        title="Facebook (demostración)"
                        aria-label="Facebook">f</span>

                    <span
                        title="WhatsApp (demostración)"
                        aria-label="WhatsApp">✆</span>
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
                <small>Datos de demostración</small>
            </div>

        </div>

        <div class="footer-agpr-base">
            © 2026 AGPR · Equipa tu ruta
            <span>Proyecto académico · Tienda de demostración</span>
        </div>
    </footer>

    <!-- FUNCIONES EXISTENTES -->
    <script src="js/script.js"></script>
    <script src="js/contacto.js"></script>
    <script src="js/checkout-enlace.js"></script>

    <!-- INTEGRACIÓN DE RAIDER CON SU TARJETA -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tarjeta = document.getElementById(
                'presentacion-raider'
            );

            const botonRaider = document.querySelector(
                '.boton-asistente'
            );

            // La tarjeta abre el chat existente.
            if (tarjeta && botonRaider) {
                tarjeta.addEventListener('click', function () {
                    const chat = document.getElementById('agpr-chat');

                    // Evita cerrar el chat si ya está abierto.
                    if (chat && !chat.hidden) {
                        const entrada = chat.querySelector(
                            '.agpr-chat-entrada'
                        );

                        if (entrada) entrada.focus();
                        return;
                    }

                    botonRaider.click();
                });
            }

            // El botón flotante conserva el isotipo.
            // Dentro del chat aparece el personaje Raider.
            const avatar = document.querySelector(
                '.raider-avatar-imagen'
            );

            if (avatar) {
                avatar.src = 'img/raider.png';
                avatar.alt = 'Raider, asistente de AGPR';

                avatar.style.setProperty(
                    'width', '100%', 'important'
                );
                avatar.style.setProperty(
                    'height', '100%', 'important'
                );
                avatar.style.setProperty(
                    'max-width', '100%', 'important'
                );
                avatar.style.setProperty(
                    'max-height', '100%', 'important'
                );
                avatar.style.setProperty(
                    'object-fit', 'cover', 'important'
                );
                avatar.style.setProperty(
                    'object-position', 'center 18%', 'important'
                );
            }
        });
    </script>

</body>
</html>
