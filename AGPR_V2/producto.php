
<?php
// Esto es para la interfaz de cada producto y los datos solo son de prueba
// hasta que los pongas en la base de datos
// Datos temporales mientras se integra MySQL.

$productos = [
    1 => [
        "id" => 1,
        "nombre" => "Casco Integral Sport",
        "categoria" => "cascos",
        "precio" => 1899,
        "imagen" => "img/cascos.jpg",
        "descripcion" => "Un casco de diseño deportivo pensado para quienes buscan una apariencia moderna durante sus recorridos.",
        "caracteristicas" => [
            "Diseño integral de estilo deportivo",
            "Acabado moderno",
            "Uso orientativo: recorridos urbanos y carretera",
            "Tallas y certificaciones por confirmar"
        ]
    ],
    2 => [
        "id" => 2,
        "nombre" => "Casco Urbano Pro",
        "categoria" => "cascos",
        "precio" => 1499,
        "imagen" => "img/cascos.jpg",
        "descripcion" => "Una opción de estilo urbano para acompañarte en tus trayectos cotidianos.",
        "caracteristicas" => [
            "Diseño urbano",
            "Acabado moderno",
            "Pensado para recorridos cotidianos",
            "Tallas y certificaciones por confirmar"
        ]
    ],
    3 => [
        "id" => 3,
        "nombre" => "Guantes Racing",
        "categoria" => "guantes",
        "precio" => 649,
        "imagen" => "img/guantes.jpg",
        "descripcion" => "Guantes de apariencia deportiva para complementar tu equipamiento de motociclista.",
        "caracteristicas" => [
            "Estilo deportivo",
            "Diseño para motociclistas",
            "Acabado de demostración",
            "Materiales y tallas por confirmar"
        ]
    ],
    4 => [
        "id" => 4,
        "nombre" => "Guantes Urbanos",
        "categoria" => "guantes",
        "precio" => 449,
        "imagen" => "img/guantes.jpg",
        "descripcion" => "Guantes de estilo urbano para quienes buscan completar su equipo de uso diario.",
        "caracteristicas" => [
            "Diseño urbano",
            "Estilo discreto",
            "Pensados para uso cotidiano",
            "Materiales y tallas por confirmar"
        ]
    ],
    5 => [
        "id" => 5,
        "nombre" => "Soporte Universal",
        "categoria" => "soportes",
        "precio" => 299,
        "imagen" => "img/soportes.jpg",
        "descripcion" => "Un soporte para celular pensado para facilitar la consulta de navegación durante tus viajes.",
        "caracteristicas" => [
            "Diseño para celular",
            "Estilo práctico",
            "Montaje por confirmar",
            "Compatibilidad por confirmar"
        ]
    ],
    6 => [
        "id" => 6,
        "nombre" => "Soporte Ajustable Pro",
        "categoria" => "soportes",
        "precio" => 399,
        "imagen" => "img/soportes.jpg",
        "descripcion" => "Una alternativa de soporte ajustable para organizar mejor tus accesorios de viaje.",
        "caracteristicas" => [
            "Diseño ajustable",
            "Estilo moderno",
            "Montaje por confirmar",
            "Compatibilidad por confirmar"
        ]
    ],
    7 => [
        "id" => 7,
        "nombre" => "Intercomunicador Rider",
        "categoria" => "intercomunicadores",
        "precio" => 1299,
        "imagen" => "img/intercomunicadores.jpg",
        "descripcion" => "Un intercomunicador de demostración para explorar opciones de comunicación durante tus recorridos.",
        "caracteristicas" => [
            "Diseño para motociclistas",
            "Concepto de comunicación durante viajes",
            "Autonomía por confirmar",
            "Compatibilidad por confirmar"
        ]
    ],
    8 => [
        "id" => 8,
        "nombre" => "Intercomunicador Duo",
        "categoria" => "intercomunicadores",
        "precio" => 2199,
        "imagen" => "img/intercomunicadores.jpg",
        "descripcion" => "Una propuesta de intercomunicador orientada a quienes disfrutan viajar acompañados.",
        "caracteristicas" => [
            "Concepto de comunicación en pareja",
            "Diseño para motociclistas",
            "Alcance por confirmar",
            "Compatibilidad por confirmar"
        ]
    ]
];

function escapar($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT,
    ["options" => ["min_range" => 1]]
);

$producto = $productos[$id] ?? null;

if (!$producto) {
    http_response_code(404);
}

$nombresCategorias = [
    "cascos" => "Cascos",
    "guantes" => "Guantes",
    "soportes" => "Soportes",
    "intercomunicadores" => "Intercomunicadores"
];

$relacionados = [];

if ($producto) {
    foreach ($productos as $otro) {
        if (
            $otro["categoria"] === $producto["categoria"] &&
            $otro["id"] !== $producto["id"]
        ) {
            $relacionados[] = $otro;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $producto ? escapar($producto["nombre"]) : "Producto no encontrado" ?> | AGPR
    </title>

    <link rel="icon" type="image/png" href="img/isotipo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pulido.css">
</head>
<body>

    <!-- ENCABEZADO -->
    <header class="encabezado">
        <div class="contenedor-header">

            <a href="index.php" class="marca" aria-label="AGPR - Inicio">
                <img src="img/logo.png" alt="Logotipo AGPR" class="logo">
            </a>

            <nav class="menu" aria-label="Navegación principal">
                <a href="index.php">Inicio</a>
                <a href="catalogo.php" class="activo">Productos</a>
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

    <main class="pagina-detalle">

        <?php if ($producto): ?>

            <!-- RUTA DE NAVEGACIÓN -->
            <div class="detalle-ruta">
                <a href="index.php">Inicio</a>
                <span>/</span>
                <a href="catalogo.php?categoria=<?= escapar($producto["categoria"]) ?>">
                    <?= escapar($nombresCategorias[$producto["categoria"]]) ?>
                </a>
                <span>/</span>
                <span><?= escapar($producto["nombre"]) ?></span>
            </div>

            <!-- INFORMACIÓN PRINCIPAL -->
            <section class="detalle-producto">

                <div class="detalle-imagen">
                    <img
                        src="<?= escapar($producto["imagen"]) ?>"
                        alt="<?= escapar($producto["nombre"]) ?>">
                </div>

                <div class="detalle-informacion">

                    <span class="seccion-etiqueta">
                        <?= escapar(strtoupper($producto["categoria"])) ?>
                    </span>

                    <h1><?= escapar($producto["nombre"]) ?></h1>

                    <p class="detalle-descripcion">
                        <?= escapar($producto["descripcion"]) ?>
                    </p>

                    <div class="detalle-precio">
                        $<?= number_format($producto["precio"], 0) ?> MXN
                    </div>

                    <p class="detalle-estado">
                        ● Producto de demostración
                    </p>

                    <p class="detalle-aviso">
                        Este artículo forma parte del catálogo de prueba.
                        Su precio, características y disponibilidad real
                        deberán confirmarse cuando integremos la base de datos.
                    </p>

                    <button
                        type="button"
                        class="btn-agregar detalle-boton"
                        data-id="<?= (int) $producto["id"] ?>">
                        Agregar al carrito
                    </button>

                    <a
                        href="catalogo.php?categoria=<?= escapar($producto["categoria"]) ?>"
                        class="detalle-volver">
                        ← Volver a <?= escapar($nombresCategorias[$producto["categoria"]]) ?>
                    </a>

                </div>
            </section>

            <!-- CARACTERÍSTICAS -->
            <section class="detalle-caracteristicas">

                <span class="seccion-etiqueta">
                    CONOCE TU EQUIPAMIENTO
                </span>

                <h2>Características del producto</h2>

                <ul>
                    <?php foreach ($producto["caracteristicas"] as $caracteristica): ?>
                        <li><?= escapar($caracteristica) ?></li>
                    <?php endforeach; ?>
                </ul>

            </section>

            <!-- PRODUCTOS RELACIONADOS -->
            <?php if (!empty($relacionados)): ?>
                <section class="detalle-relacionados">

                    <span class="seccion-etiqueta">
                        SIGUE EXPLORANDO
                    </span>

                    <h2>También podría interesarte</h2>

                    <div class="contenedor-productos">

                        <?php foreach ($relacionados as $relacionado): ?>
                            <article class="producto">

                                <a
                                    class="producto-enlace-imagen"
                                    href="producto.php?id=<?= (int) $relacionado["id"] ?>">

                                    <div class="producto-imagen">
                                        <img
                                            src="<?= escapar($relacionado["imagen"]) ?>"
                                            alt="<?= escapar($relacionado["nombre"]) ?>"
                                            loading="lazy">
                                    </div>
                                </a>

                                <div class="producto-info">

                                    <span class="producto-tipo">
                                        <?= escapar(strtoupper($relacionado["categoria"])) ?>
                                    </span>

                                    <h3>
                                        <a
                                            class="producto-enlace-titulo"
                                            href="producto.php?id=<?= (int) $relacionado["id"] ?>">
                                            <?= escapar($relacionado["nombre"]) ?>
                                        </a>
                                    </h3>

                                    <span class="producto-precio">
                                        $<?= number_format($relacionado["precio"], 0) ?> MXN
                                    </span>

                                    <a
                                        class="btn-detalles"
                                        href="producto.php?id=<?= (int) $relacionado["id"] ?>">
                                        Ver detalles
                                    </a>

                                </div>
                            </article>
                        <?php endforeach; ?>

                    </div>
                </section>
            <?php endif; ?>

        <?php else: ?>

            <section class="detalle-no-encontrado">
                <h1>Producto no encontrado</h1>

                <p>
                    El producto que buscas no existe en nuestro
                    catálogo de demostración.
                </p>

                <a href="catalogo.php" class="btn-principal">
                    Volver al catálogo
                </a>
            </section>

        <?php endif; ?>

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

    <!-- ASISTENTE VIRTUAL -->
    <div class="asistente-ia">
        <button
            type="button"
            class="boton-asistente"
            aria-label="Asistente virtual AGPR"
            title="Asistente virtual">
            &#128172;
        </button>
    </div>

    <!-- NOTIFICACIONES -->
    <div
        id="notificacion"
        class="notificacion"
        role="status"
        aria-live="polite"></div>

    <footer class="footer-agpr">
  <div class="footer-agpr-grid">
    <div class="footer-marca"><img src="img/logo.png" alt="AGPR"><p>Equipa tu ruta. Accesorios para acompañarte en cada kilómetro.</p><div class="footer-redes" aria-label="Redes sociales ilustrativas"><span title="Instagram (demostración)" aria-label="Instagram">◎</span><span title="Facebook (demostración)" aria-label="Facebook">f</span><span title="WhatsApp (demostración)" aria-label="WhatsApp">✆</span></div></div>
    <div><h3>Explora</h3><a href="index.php">Inicio</a><a href="catalogo.php">Catálogo</a><a href="index.php#nosotros">Nosotros</a><a href="index.php#contacto">Contacto</a></div>
    <div><h3>Categorías</h3><a href="catalogo.php?categoria=cascos">Cascos</a><a href="catalogo.php?categoria=guantes">Guantes</a><a href="catalogo.php?categoria=soportes">Soportes</a><a href="catalogo.php?categoria=intercomunicadores">Intercomunicadores</a></div>
    <div><h3>Atención</h3><p>Morelia, Michoacán</p><p>Lun–Sáb · 9:00 a 18:00</p><p>contacto@agpr.example</p><a href="login.php">Mi cuenta</a><small>Datos de demostración</small></div>
  </div><div class="footer-agpr-base">© 2026 AGPR · Equipa tu ruta <span>Proyecto académico · Tienda de demostración</span></div>
</footer>

    <script src="js/script.js?v=2"></script>

</body>
</html>
