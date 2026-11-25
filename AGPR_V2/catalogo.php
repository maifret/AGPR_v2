
<?php
// AGPR - Catálogo de productos de demostración

$productos = [
    [
        "id" => 1,
        "nombre" => "Casco Integral Sport",
        "categoria" => "cascos",
        "precio" => 1899,
        "imagen" => "img/cascos.jpg",
        "descripcion" => "Diseño deportivo para tus recorridos.",
        "etiqueta" => "oferta"
    ],
    [
        "id" => 2,
        "nombre" => "Casco Urbano Pro",
        "categoria" => "cascos",
        "precio" => 1499,
        "imagen" => "img/cascos.jpg",
        "descripcion" => "Una opción práctica para la ciudad.",
        "etiqueta" => "nuevo"
    ],
    [
        "id" => 3,
        "nombre" => "Guantes Racing",
        "categoria" => "guantes",
        "precio" => 649,
        "imagen" => "img/guantes.jpg",
        "descripcion" => "Estilo deportivo y comodidad.",
        "etiqueta" => "mas-vendido"
    ],
    [
        "id" => 4,
        "nombre" => "Guantes Urbanos",
        "categoria" => "guantes",
        "precio" => 449,
        "imagen" => "img/guantes.jpg",
        "descripcion" => "Para el uso diario en motocicleta.",
        "etiqueta" => "destacado"
    ],
    [
        "id" => 5,
        "nombre" => "Soporte Universal",
        "categoria" => "soportes",
        "precio" => 299,
        "imagen" => "img/soportes.jpg",
        "descripcion" => "Mantén tu celular a la vista.",
        "etiqueta" => "oferta"
    ],
    [
        "id" => 6,
        "nombre" => "Soporte Ajustable Pro",
        "categoria" => "soportes",
        "precio" => 399,
        "imagen" => "img/soportes.jpg",
        "descripcion" => "Montaje práctico para tus recorridos.",
        "etiqueta" => "nuevo"
    ],
    [
        "id" => 7,
        "nombre" => "Intercomunicador Rider",
        "categoria" => "intercomunicadores",
        "precio" => 1299,
        "imagen" => "img/intercomunicadores.jpg",
        "descripcion" => "Comunicación para tus aventuras.",
        "etiqueta" => "destacado"
    ],
    [
        "id" => 8,
        "nombre" => "Intercomunicador Duo",
        "categoria" => "intercomunicadores",
        "precio" => 2199,
        "imagen" => "img/intercomunicadores.jpg",
        "descripcion" => "Una opción para viajar acompañado.",
        "etiqueta" => "mas-vendido"
    ]
];

$categorias = [
    "todos" => "Todos los productos",
    "cascos" => "Cascos",
    "guantes" => "Guantes",
    "soportes" => "Soportes",
    "intercomunicadores" => "Intercomunicadores"
];

$descripciones = [
    "todos" => "Encuentra accesorios para equipar cada kilómetro de tu ruta.",
    "cascos" => "Explora opciones para acompañarte en cada recorrido.",
    "guantes" => "Comodidad y estilo para llevar el control de tu ruta.",
    "soportes" => "Lleva tu celular a la vista con nuestros soportes para motocicleta.",
    "intercomunicadores" => "Mantente comunicado durante tus recorridos."
];

$etiquetas = [
    "oferta" => "OFERTA",
    "nuevo" => "NUEVO",
    "mas-vendido" => "MÁS VENDIDO",
    "destacado" => "DESTACADO"
];

$categoria = $_GET["categoria"] ?? "todos";
$busqueda = $_GET["buscar"] ?? "";

if (
    !is_string($categoria) ||
    !array_key_exists($categoria, $categorias)
) {
    $categoria = "todos";
}

if (!is_string($busqueda)) {
    $busqueda = "";
}

$busqueda = trim($busqueda);

$productosFiltrados = array_filter(
    $productos,
    function ($producto) use ($categoria, $busqueda) {
        $coincideCategoria =
            $categoria === "todos" ||
            $producto["categoria"] === $categoria;

        $coincideBusqueda =
            $busqueda === "" ||
            stripos($producto["nombre"], $busqueda) !== false ||
            stripos($producto["categoria"], $busqueda) !== false;

        return $coincideCategoria && $coincideBusqueda;
    }
);

function escapar($valor) {
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}

function urlCategoria($categoria, $busqueda) {
    $parametros = [];

    if ($categoria !== "todos") {
        $parametros["categoria"] = $categoria;
    }

    if ($busqueda !== "") {
        $parametros["buscar"] = $busqueda;
    }

    $query = http_build_query($parametros);

    return "catalogo.php" . ($query ? "?" . $query : "");
}

$titulo = $categorias[$categoria];
$descripcionCategoria = $descripciones[$categoria];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Explora el catálogo de accesorios para motociclistas de AGPR.">

    <title><?= escapar($titulo) ?> | AGPR</title>

    <link rel="icon" type="image/png" href="img/isotipo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/pulido.css">

    <style>
        /* =====================================
           CATÁLOGO AGPR
           ===================================== */

        .catalogo.catalogo-refinado {
            padding: 42px 5% 90px;
            background: linear-gradient(
                180deg,
                rgba(16, 16, 16, .96),
                rgba(16, 16, 16, .98)
            );
            text-align: center;
        }

        .catalogo-refinado-interior {
            max-width: 1350px;
            margin: 0 auto;
        }

        .catalogo-migas {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 13px;
            margin-bottom: 26px;
        }

        .catalogo-migas a {
            color: #bdbdbd;
            text-decoration: none;
        }

        .catalogo-migas a:hover {
            color: #f04a50;
        }

        .catalogo-migas span[aria-current] {
            color: #eee;
        }

        .catalogo-refinado-titulo {
            max-width: 780px;
            margin: 0 auto 28px;
            text-align: center;
        }

        .catalogo-refinado-titulo .seccion-etiqueta {
            display: block;
            margin-bottom: 10px;
        }

        .catalogo-refinado-titulo h1 {
            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-size: clamp(42px, 5vw, 64px);
            line-height: 1.05;
            margin: 0 0 10px;
        }

        .catalogo-refinado-titulo p {
            color: #c4c4c4;
            font-size: 15px;
            line-height: 1.7;
            margin: 0 auto;
            max-width: 650px;
        }

        .catalogo-refinado .filtros {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 0 0 24px;
            padding-bottom: 23px;
            border-bottom: 1px solid #343434;
        }

        .catalogo-refinado .filtro {
            padding: 10px 17px;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
        }

        .catalogo-refinado-resultados {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 25px;
            text-align: center;
        }

        .catalogo-refinado .resultados-contador {
            margin: 0;
            max-width: none;
            color: #bcbcbc;
            font-size: 13px;
        }

        .catalogo-limpiar {
            color: #ff666c;
            font-size: 13px;
            text-decoration: none;
        }

        .catalogo-limpiar:hover {
            text-decoration: underline;
        }

        .catalogo-refinado .contenedor-productos {
            max-width: none;
            text-align: left;
        }

        .catalogo-refinado .producto-info {
            text-align: left;
        }

        .catalogo-refinado .sin-resultados {
            text-align: center;
        }

        /* =====================================
           ETIQUETAS AGPR RACING
           ===================================== */

        .catalogo-refinado .producto-imagen {
            position: relative;
        }

        .catalogo-refinado .producto-etiqueta {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 2;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: auto;
            max-width: calc(100% - 32px);
            min-height: 35px;
            padding: 7px 12px;

            border: 1px solid transparent;
            border-radius: 6px;

            color: #fff;
            font-family: 'Rajdhani', sans-serif;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: .5px;
            text-align: center;
            text-transform: uppercase;

            box-shadow: 0 4px 14px rgba(0, 0, 0, .35);

            transition:
                box-shadow .25s ease,
                transform .25s ease,
                filter .25s ease;
        }

        /* OFERTA - ROJO RACING */
        .catalogo-refinado .producto-etiqueta.etiqueta-oferta {
            background: #E32735;
            border-color: #FF6971;
            color: #FFFFFF;
        }

        /* NUEVO - TURQUESA ELÉCTRICO */
        .catalogo-refinado .producto-etiqueta.etiqueta-nuevo {
            background: #00A8A0;
            border-color: #44E3D4;
            color: #FFFFFF;
        }

        /* MÁS VENDIDO - ÁMBAR */
        .catalogo-refinado .producto-etiqueta.etiqueta-mas-vendido {
            background: #F5AD25;
            border-color: #FFD36E;
            color: #171717;
        }

        /* DESTACADO - BLANCO TITANIO */
        .catalogo-refinado .producto-etiqueta.etiqueta-destacado {
            background: #E9EDF2;
            border-color: #FFFFFF;
            color: #202027;
        }

        /* BRILLO SUTIL AL PASAR EL CURSOR */
        .catalogo-refinado .producto:hover
        .producto-etiqueta.etiqueta-oferta {
            box-shadow:
                0 0 17px rgba(227, 39, 53, .55),
                0 4px 14px rgba(0, 0, 0, .35);
        }

        .catalogo-refinado .producto:hover
        .producto-etiqueta.etiqueta-nuevo {
            box-shadow:
                0 0 17px rgba(0, 168, 160, .55),
                0 4px 14px rgba(0, 0, 0, .35);
        }

        .catalogo-refinado .producto:hover
        .producto-etiqueta.etiqueta-mas-vendido {
            box-shadow:
                0 0 17px rgba(245, 173, 37, .55),
                0 4px 14px rgba(0, 0, 0, .35);
        }

        .catalogo-refinado .producto:hover
        .producto-etiqueta.etiqueta-destacado {
            box-shadow:
                0 0 17px rgba(233, 237, 242, .45),
                0 4px 14px rgba(0, 0, 0, .35);
        }

        @media (max-width: 650px) {
            .catalogo.catalogo-refinado {
                padding: 26px 5% 65px;
            }

            .catalogo-migas {
                margin-bottom: 19px;
            }

            .catalogo-refinado-titulo {
                margin-bottom: 22px;
            }

            .catalogo-refinado .filtros {
                gap: 8px;
            }

            .catalogo-refinado .filtro {
                padding: 9px 12px;
                font-size: 15px;
            }

            .catalogo-refinado .producto-etiqueta {
                top: 10px;
                left: 10px;
                max-width: calc(100% - 20px);
                padding: 6px 10px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <header class="encabezado">
        <div class="contenedor-header">

            <a href="index.php" class="marca" aria-label="AGPR - Inicio">
                <img
                    src="img/logo.png"
                    alt="Logotipo AGPR"
                    class="logo">
            </a>

            <nav class="menu" aria-label="Navegación principal">
                <a href="index.php">Inicio</a>
                <a href="catalogo.php" class="activo">Productos</a>
                <a href="index.php#nosotros">Nosotros</a>
                <a href="index.php#contacto">Contacto</a>
            </nav>

            <div class="acciones-header">

                <form
                    class="form-buscador"
                    action="catalogo.php"
                    method="get"
                    role="search">

                    <?php if ($categoria !== "todos"): ?>
                        <input
                            type="hidden"
                            name="categoria"
                            value="<?= escapar($categoria) ?>">
                    <?php endif; ?>

                    <input
                        type="search"
                        name="buscar"
                        id="buscador"
                        value="<?= escapar($busqueda) ?>"
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
        <section id="catalogo" class="catalogo catalogo-refinado">

            <div class="catalogo-refinado-interior">

                <nav
                    class="catalogo-migas"
                    aria-label="Ruta de navegación">

                    <a href="index.php">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="catalogo.php">Productos</a>

                    <?php if ($categoria !== "todos"): ?>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">
                            <?= escapar($titulo) ?>
                        </span>
                    <?php endif; ?>

                </nav>

                <div class="catalogo-refinado-titulo">

                    <span class="seccion-etiqueta">
                        CATÁLOGO AGPR
                    </span>

                    <h1><?= escapar($titulo) ?></h1>

                    <p>
                        <?= escapar($descripcionCategoria) ?>
                    </p>

                </div>

                <!-- FILTROS -->
                <nav
                    class="filtros"
                    aria-label="Categorías de productos">

                    <?php foreach ($categorias as $clave => $nombre): ?>

                        <a
                            href="<?= escapar(urlCategoria($clave, $busqueda)) ?>"
                            class="filtro <?= $categoria === $clave ? 'activo' : '' ?>"
                            <?= $categoria === $clave ? 'aria-current="page"' : '' ?>>

                            <?= escapar($nombre) ?>

                        </a>

                    <?php endforeach; ?>

                </nav>

                <!-- CONTADOR -->
                <div class="catalogo-refinado-resultados">

                    <p class="resultados-contador">
                        <?= count($productosFiltrados) ?>
                        <?= count($productosFiltrados) === 1
                            ? "producto encontrado"
                            : "productos encontrados" ?>
                    </p>

                    <?php if ($busqueda !== ""): ?>
                        <a
                            class="catalogo-limpiar"
                            href="<?= escapar(urlCategoria($categoria, "")) ?>">
                            Limpiar búsqueda:
                            «<?= escapar($busqueda) ?>» ×
                        </a>
                    <?php endif; ?>

                </div>

                <!-- PRODUCTOS -->
                <div
                    class="contenedor-productos"
                    id="lista-productos">

                    <?php foreach ($productosFiltrados as $producto): ?>

                        <article
                            class="producto"
                            data-id="<?= (int) $producto["id"] ?>">

                            <a
                                class="producto-enlace-imagen"
                                href="producto.php?id=<?= (int) $producto["id"] ?>"
                                aria-label="Ver detalles de <?= escapar($producto["nombre"]) ?>">

                                <div class="producto-imagen">

                                    <img
                                        src="<?= escapar($producto["imagen"]) ?>"
                                        alt="<?= escapar($producto["nombre"]) ?>"
                                        loading="lazy">

                                    <!-- ETIQUETA AGPR RACING -->
                                    <span
                                        class="producto-etiqueta etiqueta-<?= escapar($producto["etiqueta"]) ?>"
                                        title="Etiqueta ilustrativa del proyecto">

                                        <?= escapar($etiquetas[$producto["etiqueta"]]) ?>

                                    </span>

                                </div>
                            </a>

                            <div class="producto-info">

                                <span class="producto-tipo">
                                    <?= escapar(strtoupper($producto["categoria"])) ?>
                                </span>

                                <h3>
                                    <a
                                        class="producto-enlace-titulo"
                                        href="producto.php?id=<?= (int) $producto["id"] ?>">

                                        <?= escapar($producto["nombre"]) ?>

                                    </a>
                                </h3>

                                <p>
                                    <?= escapar($producto["descripcion"]) ?>
                                </p>

                                <span class="producto-precio">
                                    $<?= number_format($producto["precio"], 0) ?> MXN
                                </span>

                                <span class="producto-disponible">
                                    Producto de demostración
                                </span>

                                <a
                                    class="btn-detalles"
                                    href="producto.php?id=<?= (int) $producto["id"] ?>">
                                    Ver detalles
                                </a>

                                <button
                                    type="button"
                                    class="btn-agregar"
                                    data-id="<?= (int) $producto["id"] ?>">
                                    Agregar al carrito
                                </button>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

                <?php if (empty($productosFiltrados)): ?>

                    <p class="sin-resultados">
                        No encontramos productos con esos criterios.
                    </p>

                    <div class="centrar-accion">
                        <a
                            href="catalogo.php"
                            class="btn-principal">
                            Ver todos los productos
                        </a>
                    </div>

                <?php endif; ?>

            </div>
        </section>
    </main>

    <!-- CARRITO -->
    <div
        id="fondo-carrito"
        class="fondo-carrito"
        hidden>
    </div>

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

        <div
            id="productos-carrito"
            class="productos-carrito">
        </div>

        <div class="carrito-resumen">

            <div class="carrito-total">
                <span>Subtotal</span>
                <strong id="total-carrito">$0 MXN</strong>
            </div>

            <p>
                Precios de demostración. Envío no incluido.
            </p>

            <button
                type="button"
                id="finalizar-compra"
                class="btn-principal btn-finalizar">
                Finalizar compra
            </button>

        </div>
    </aside>

    <!-- ASISTENTE RAIDER -->
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
        aria-live="polite">
    </div>

    <!-- PIE DE PÁGINA -->
    <footer class="footer-agpr">

        <div class="footer-agpr-grid">

            <div class="footer-marca">

                <img src="img/logo.png" alt="AGPR">

                <p>
                    Equipa tu ruta. Accesorios para
                    acompañarte en cada kilómetro.
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
            <span>
                Proyecto académico · Tienda de demostración
            </span>
        </div>

    </footer>

    <script src="js/script.js?v=2"></script>
    <script src="js/checkout-enlace.js"></script>


</body>
</html>
