
<?php
/*
 * AGPR - Checkout y confirmación de pedido
 * Versión de demostración académica.
 *
 * No procesa pagos reales.
 * No almacena información bancaria.
 * No requiere conexión a MySQL.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finalizar compra | AGPR</title>

    <link rel="icon" type="image/png" href="img/isotipo.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rajdhani:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --rojo: #e32735;
            --rojo-hover: #b91e2b;
            --fondo: #101012;
            --panel: #1b1b1e;
            --panel-secundario: #242428;
            --borde: #343438;
            --texto: #f5f5f5;
            --suave: #b8b8bf;
            --verde: #5be0a1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--fondo);
            color: var(--texto);
            font-family: 'Inter', sans-serif;
        }

        button,
        input {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        h1,
        h2,
        h3 {
            font-family: 'Rajdhani', sans-serif;
        }

        a {
            color: inherit;
        }

        .oculto {
            display: none !important;
        }

        /* ENCABEZADO */

        .encabezado {
            background: #161618;
            border-bottom: 1px solid var(--borde);
            padding: 14px 6%;
        }

        .cabecera {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .logo {
            width: 150px;
            max-height: 75px;
            object-fit: contain;
        }

        .volver {
            color: #ddd;
            font-size: 14px;
            text-decoration: none;
            transition: color .2s;
        }

        .volver:hover {
            color: var(--rojo);
        }

        /* CONTENIDO PRINCIPAL */

        main {
            max-width: 1200px;
            margin: 45px auto 70px;
            padding: 0 20px;
        }

        .encabezado-pago {
            margin-bottom: 30px;
        }

        .eyebrow {
            color: var(--rojo);
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(36px, 5vw, 54px);
            line-height: 1.1;
            margin: 8px 0 12px;
        }

        .subtitulo {
            color: var(--suave);
            line-height: 1.7;
            max-width: 700px;
            font-size: 14px;
        }

        .layout {
            display: grid;
            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(290px, 1fr);
            gap: 24px;
            align-items: start;
        }

        .panel {
            background: var(--panel);
            border: 1px solid var(--borde);
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .panel h2 {
            font-size: 27px;
            margin: 0 0 20px;
        }

        /* DATOS DEL CLIENTE */

        .campos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .ancho-completo {
            grid-column: 1 / -1;
        }

        .campo label {
            font-size: 13px;
            font-weight: 600;
            color: #ddd;
        }

        .campo input {
            width: 100%;
            background: #101012;
            color: white;
            border: 1px solid #414147;
            border-radius: 8px;
            padding: 13px;
            outline: none;
            transition: border-color .2s;
        }

        .campo input:focus {
            border-color: var(--rojo);
            box-shadow: 0 0 0 3px rgba(227,39,53,.12);
        }

        /* MÉTODOS DE PAGO */

        .opcion {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 17px;
            border: 1px solid var(--borde);
            border-radius: 10px;
            background: #151517;
            margin-bottom: 12px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }

        .opcion:has(input:checked) {
            border-color: var(--rojo);
            background: rgba(227,39,53,.07);
        }

        .opcion input {
            width: 18px;
            height: 18px;
            accent-color: var(--rojo);
            flex-shrink: 0;
        }

        .opcion strong {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .opcion small {
            color: var(--suave);
            line-height: 1.5;
        }

        .aviso {
            background: var(--panel-secundario);
            color: #ccc;
            border-radius: 8px;
            padding: 14px;
            font-size: 13px;
            line-height: 1.7;
            margin-top: 14px;
        }

        /* RESUMEN DE PRODUCTOS */

        .producto {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 14px 0;
            border-bottom: 1px solid var(--borde);
        }

        .producto img {
            width: 66px;
            height: 66px;
            object-fit: contain;
            background: #080808;
            border-radius: 7px;
            flex-shrink: 0;
        }

        .producto-datos {
            flex: 1;
            min-width: 0;
        }

        .producto-datos strong {
            display: block;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 5px;
        }

        .producto-datos small {
            color: var(--suave);
        }

        .precio {
            white-space: nowrap;
            font-size: 13px;
            font-weight: 700;
        }

        .fila-total {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 20px;
            font-size: 20px;
            font-weight: 700;
        }

        .boton {
            display: inline-block;
            width: 100%;
            background: var(--rojo);
            border: 1px solid #ff6971;
            border-radius: 8px;
            color: white;
            padding: 15px 20px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 19px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: background .2s, transform .2s;
        }

        .boton:hover:not(:disabled) {
            background: var(--rojo-hover);
            transform: translateY(-1px);
        }

        .boton:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .boton-confirmar {
            margin-top: 20px;
        }

        .vacio {
            color: var(--suave);
            line-height: 1.7;
        }

        .vacio a {
            color: #ff6971;
        }

        /* =====================================
           NUEVA PANTALLA DE CONFIRMACIÓN
           ===================================== */

        .confirmacion {
            max-width: 780px;
            margin: 0 auto;
            text-align: center;
        }

        .confirmacion-icono {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            margin: 0 auto 22px;
            background: rgba(91,224,161,.12);
            border: 2px solid rgba(91,224,161,.45);
            color: var(--verde);
            font-size: 47px;
            font-weight: 700;
            box-shadow: 0 0 30px rgba(91,224,161,.08);
        }

        .confirmacion h1 {
            margin-bottom: 12px;
        }

        .confirmacion-intro {
            color: var(--suave);
            line-height: 1.8;
            font-size: 14px;
            max-width: 580px;
            margin: 0 auto 30px;
        }

        .confirmacion-panel {
            text-align: left;
            background: var(--panel);
            border: 1px solid var(--borde);
            border-radius: 14px;
            padding: 26px;
            margin-bottom: 22px;
        }

        .confirmacion-panel h2 {
            font-size: 26px;
            margin: 0 0 20px;
        }

        .datos-pedido {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            margin-bottom: 22px;
        }

        .dato-etiqueta {
            display: block;
            color: var(--suave);
            font-size: 12px;
            margin-bottom: 7px;
        }

        .dato-valor {
            display: block;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .numero-pedido {
            color: #ff6971;
            font-family: 'Rajdhani', sans-serif;
            font-size: 22px;
        }

        .separador {
            border: 0;
            border-top: 1px solid var(--borde);
            margin: 22px 0;
        }

        .confirmacion-productos .producto:last-child {
            border-bottom: 0;
        }

        .confirmacion-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            font-size: 20px;
            font-weight: 700;
            margin-top: 20px;
        }

        .confirmacion-total strong {
            color: var(--verde);
            font-size: 25px;
            font-family: 'Rajdhani', sans-serif;
        }

        .mensaje-demo {
            padding: 16px;
            background: rgba(227,39,53,.07);
            border: 1px solid rgba(227,39,53,.3);
            border-radius: 9px;
            color: #ddd;
            line-height: 1.7;
            font-size: 13px;
            margin-bottom: 24px;
        }

        .acciones-confirmacion {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .acciones-confirmacion .boton {
            width: auto;
            min-width: 210px;
        }

        .boton-secundario {
            background: #242428;
            border-color: #494950;
        }

        .boton-secundario:hover {
            background: #343438;
        }

        footer {
            text-align: center;
            padding: 28px 15px;
            border-top: 1px solid var(--borde);
            color: #8f8f96;
            font-size: 12px;
        }

        /* RESPONSIVE */

        @media (max-width: 800px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .campos,
            .datos-pedido {
                grid-template-columns: 1fr;
            }

            .panel,
            .confirmacion-panel {
                padding: 18px;
            }

            main {
                margin-top: 28px;
            }

            .producto {
                flex-wrap: wrap;
            }

            .precio {
                margin-left: auto;
            }

            .acciones-confirmacion .boton {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- ENCABEZADO -->
    <header class="encabezado">
        <div class="cabecera">
            <a href="index.php">
                <img
                    class="logo"
                    src="img/logo.png"
                    alt="Logotipo AGPR">
            </a>

            <a class="volver" href="catalogo.php">
                ← Volver al catálogo
            </a>
        </div>
    </header>

    <main>

        <!-- =====================================
             PANTALLA DE PAGO
             ===================================== -->

        <div id="checkout">

            <div class="encabezado-pago">
                <span class="eyebrow">
                    AGPR · EQUIPA TU RUTA
                </span>

                <h1>FINALIZAR COMPRA</h1>

                <p class="subtitulo">
                    Completa tus datos de entrega y selecciona
                    una forma de pago para simular tu pedido.
                </p>
            </div>

            <form id="formulario-pago">

                <div class="layout">

                    <div>

                        <!-- DATOS DE ENTREGA -->
                        <section class="panel">
                            <h2>01. Datos de entrega</h2>

                            <div class="campos">

                                <div class="campo">
                                    <label for="nombre">
                                        Nombre
                                    </label>
                                    <input
                                        id="nombre"
                                        name="nombre"
                                        required
                                        maxlength="60"
                                        autocomplete="given-name">
                                </div>

                                <div class="campo">
                                    <label for="apellido">
                                        Apellidos
                                    </label>
                                    <input
                                        id="apellido"
                                        name="apellido"
                                        required
                                        maxlength="80"
                                        autocomplete="family-name">
                                </div>

                                <div class="campo">
                                    <label for="correo">
                                        Correo electrónico
                                    </label>
                                    <input
                                        id="correo"
                                        name="correo"
                                        type="email"
                                        required
                                        autocomplete="email">
                                </div>

                                <div class="campo">
                                    <label for="telefono">
                                        Teléfono
                                    </label>
                                    <input
                                        id="telefono"
                                        name="telefono"
                                        type="tel"
                                        required
                                        maxlength="20"
                                        autocomplete="tel">
                                </div>

                                <div class="campo ancho-completo">
                                    <label for="direccion">
                                        Calle, número y colonia
                                    </label>
                                    <input
                                        id="direccion"
                                        name="direccion"
                                        required
                                        maxlength="180"
                                        autocomplete="street-address">
                                </div>

                                <div class="campo">
                                    <label for="ciudad">
                                        Ciudad
                                    </label>
                                    <input
                                        id="ciudad"
                                        name="ciudad"
                                        required
                                        autocomplete="address-level2">
                                </div>

                                <div class="campo">
                                    <label for="estado">
                                        Estado
                                    </label>
                                    <input
                                        id="estado"
                                        name="estado"
                                        required
                                        autocomplete="address-level1">
                                </div>

                                <div class="campo">
                                    <label for="cp">
                                        Código postal
                                    </label>
                                    <input
                                        id="cp"
                                        name="cp"
                                        required
                                        pattern="[0-9]{5}"
                                        maxlength="5"
                                        inputmode="numeric"
                                        title="Escribe cinco dígitos"
                                        autocomplete="postal-code">
                                </div>

                            </div>
                        </section>

                        <!-- FORMA DE PAGO -->
                        <section class="panel">
                            <h2>02. Forma de pago</h2>

                            <label class="opcion">
                                <input
                                    type="radio"
                                    name="metodo"
                                    value="tarjeta"
                                    checked>

                                <span>
                                    <strong>
                                        💳 Tarjeta de crédito o débito
                                    </strong>

                                    <small>
                                        Pago simulado con tarjeta
                                    </small>
                                </span>
                            </label>

                            <label class="opcion">
                                <input
                                    type="radio"
                                    name="metodo"
                                    value="transferencia">

                                <span>
                                    <strong>
                                        🏦 Transferencia bancaria
                                    </strong>

                                    <small>
                                        Pago simulado por transferencia
                                    </small>
                                </span>
                            </label>

                            <div class="aviso" id="aviso-metodo">
                                No solicitamos números de tarjeta,
                                CVV ni datos bancarios.
                            </div>

                        </section>
                    </div>

                    <!-- RESUMEN DEL CARRITO -->
                    <aside class="panel">

                        <h2>03. Tu pedido</h2>

                        <div id="lista-pedido"></div>

                        <div class="fila-total">
                            <span>Subtotal</span>
                            <strong id="subtotal">
                                $0 MXN
                            </strong>
                        </div>

                        <p class="aviso">
                            Envío no calculado.
                            Los precios corresponden
                            al catálogo de demostración.
                        </p>

                        <button
                            type="submit"
                            class="boton boton-confirmar"
                            id="confirmar"
                            disabled>
                            CONFIRMAR PEDIDO DE PRUEBA
                        </button>

                    </aside>

                </div>

            </form>

        </div>

        <!-- =====================================
             NUEVA CONFIRMACIÓN DE COMPRA
             ===================================== -->

        <section
            id="confirmacion"
            class="confirmacion oculto"
            aria-live="polite">

            <div class="confirmacion-icono"
                 aria-hidden="true">
                ✓
            </div>

            <span class="eyebrow">
                AGPR · EQUIPA TU RUTA
            </span>

            <h1>¡PEDIDO CONFIRMADO!</h1>

            <p class="confirmacion-intro"
               id="mensaje-confirmacion">
            </p>

            <div class="confirmacion-panel">

                <h2>Resumen de tu pedido</h2>

                <div class="datos-pedido">

                    <div>
                        <span class="dato-etiqueta">
                            Número de pedido
                        </span>

                        <strong
                            class="dato-valor numero-pedido"
                            id="numero-pedido">
                        </strong>
                    </div>

                    <div>
                        <span class="dato-etiqueta">
                            Fecha del pedido
                        </span>

                        <strong
                            class="dato-valor"
                            id="fecha-pedido">
                        </strong>
                    </div>

                    <div>
                        <span class="dato-etiqueta">
                            Método de pago
                        </span>

                        <strong
                            class="dato-valor"
                            id="metodo-confirmacion">
                        </strong>
                    </div>

                    <div>
                        <span class="dato-etiqueta">
                            Estado
                        </span>

                        <strong class="dato-valor">
                            Confirmado · Simulación
                        </strong>
                    </div>

                </div>

                <hr class="separador">

                <h2>Productos del pedido</h2>

                <div
                    class="confirmacion-productos"
                    id="productos-confirmados">
                </div>

                <hr class="separador">

                <div class="confirmacion-total">
                    <span>Total de productos</span>

                    <strong id="total-confirmado">
                        $0 MXN
                    </strong>
                </div>

            </div>

            <div class="mensaje-demo">
                Este pedido es una demostración académica.
                No se realizó ningún cobro ni transferencia,
                no se registró en una base de datos y
                no se generó ningún envío.
            </div>

            <div class="acciones-confirmacion">

                <a
                    href="catalogo.php"
                    class="boton">
                    SEGUIR COMPRANDO
                </a>

                <a
                    href="index.php"
                    class="boton boton-secundario">
                    VOLVER AL INICIO
                </a>

            </div>

        </section>

    </main>

    <footer>
        © 2026 AGPR · Equipa tu ruta
        · Proyecto académico de demostración
    </footer>

    <script>
        'use strict';

        /*
         * CATÁLOGO DE DEMOSTRACIÓN
         *
         * Se mantiene compatible con el carrito
         * de AGPR mientras llega la base de datos.
         */

        let productos = [];

        const CLAVE_CARRITO = 'agpr_carrito_demo';

        const formatoDinero = new Intl.NumberFormat(
            'es-MX',
            {
                style: 'currency',
                currency: 'MXN',
                maximumFractionDigits: 0
            }
        );

        const listaPedido =
            document.getElementById('lista-pedido');

        const subtotalElemento =
            document.getElementById('subtotal');

        const botonConfirmar =
            document.getElementById('confirmar');

        const formulario =
            document.getElementById('formulario-pago');

        const avisoMetodo =
            document.getElementById('aviso-metodo');

        let carrito = [];
        let totalPedido = 0;

        function leerCarrito() {
            carrito = [];

            try {
                const datos = JSON.parse(
                    localStorage.getItem(CLAVE_CARRITO) || '[]'
                );

                if (Array.isArray(datos)) {
                    carrito = datos
                        .map(item => ({
                            id: Number(item.id),
                            cantidad: Math.min(
                                99,
                                Math.max(
                                    0,
                                    Math.floor(Number(item.cantidad) || 0)
                                )
                            )
                        }))
                        .filter(item =>
                            productos.some(
                                producto => Number(producto.id) === item.id
                            ) &&
                            item.cantidad > 0
                        );
                }
            } catch (error) {
                carrito = [];
            }
        }

        /*
         * CREAR ELEMENTO VISUAL DE PRODUCTO
         */

        function crearFilaProducto(producto, cantidad) {
            const fila = document.createElement('div');
            fila.className = 'producto';

            const imagen = document.createElement('img');
            imagen.src = producto.imagen;
            imagen.alt = producto.nombre;

            const datos = document.createElement('div');
            datos.className = 'producto-datos';

            const nombre = document.createElement('strong');
            nombre.textContent = producto.nombre;

            const cantidadTexto =
                document.createElement('small');

            cantidadTexto.textContent =
                'Cantidad: ' + cantidad;

            datos.append(
                nombre,
                cantidadTexto
            );

            const precio = document.createElement('span');
            precio.className = 'precio';

            precio.textContent = formatoDinero.format(
                producto.precio * cantidad
            );

            fila.append(
                imagen,
                datos,
                precio
            );

            return fila;
        }

        /*
         * MOSTRAR RESUMEN EN CHECKOUT
         */

        function mostrarPedido() {
            listaPedido.replaceChildren();

            totalPedido = 0;

            if (carrito.length === 0) {
                const mensaje = document.createElement('p');
                mensaje.className = 'vacio';
                mensaje.textContent =
                    'Tu carrito está vacío. Agrega productos desde el catálogo.';

                const enlace = document.createElement('a');
                enlace.href = 'catalogo.php';
                enlace.textContent = 'Ir al catálogo';

                listaPedido.append(
                    mensaje,
                    enlace
                );

                subtotalElemento.textContent =
                    formatoDinero.format(0);

                botonConfirmar.disabled = true;
                return;
            }

            carrito.forEach(item => {
                const producto = productos.find(
                    producto => producto.id === item.id
                );

                if (!producto) {
                    return;
                }

                totalPedido +=
                    producto.precio * item.cantidad;

                listaPedido.appendChild(
                    crearFilaProducto(
                        producto,
                        item.cantidad
                    )
                );
            });

            subtotalElemento.textContent =
                formatoDinero.format(totalPedido);

            botonConfirmar.disabled = totalPedido <= 0;
        }

        /*
         * CAMBIAR MENSAJE SEGÚN MÉTODO DE PAGO
         */

        document.querySelectorAll(
            'input[name="metodo"]'
        ).forEach(radio => {

            radio.addEventListener('change', () => {

                const seleccionado =
                    document.querySelector(
                        'input[name="metodo"]:checked'
                    );

                if (!seleccionado) {
                    return;
                }

                if (seleccionado.value === 'tarjeta') {
                    avisoMetodo.textContent =
                        'Pago con tarjeta simulado. ' +
                        'No se solicitan números bancarios ' +
                        'ni se realizan cargos.';
                } else {
                    avisoMetodo.textContent =
                        'Transferencia simulada. ' +
                        'No se proporcionan cuentas bancarias ' +
                        'ni se transfiere dinero.';
                }
            });

        });

        /*
         * GENERAR NÚMERO DE PEDIDO DEMO
         *
         * Es un identificador visual aleatorio.
         * No es un folio real ni se guarda en MySQL.
         */

        function generarNumeroPedido() {
            const numero = Math.floor(
                100000 + Math.random() * 900000
            );

            return 'AGPR-DEMO-' + numero;
        }

        /*
         * CONFIRMAR COMPRA SIMULADA
         */

        formulario.addEventListener('submit', evento => {
            evento.preventDefault();

            if (!formulario.reportValidity()) {
                return;
            }

            if (carrito.length === 0 || totalPedido <= 0) {
                return;
            }

            const nombre = document.getElementById(
                'nombre'
            ).value.trim();

            const metodoSeleccionado =
                document.querySelector(
                    'input[name="metodo"]:checked'
                );

            if (!metodoSeleccionado) {
                return;
            }

            const metodo =
                metodoSeleccionado.value === 'tarjeta'
                    ? 'Tarjeta de crédito o débito'
                    : 'Transferencia bancaria';

            const fecha = new Date();

            const fechaFormateada =
                new Intl.DateTimeFormat(
                    'es-MX',
                    {
                        dateStyle: 'long',
                        timeStyle: 'short'
                    }
                ).format(fecha);

            /*
             * INFORMACIÓN GENERAL
             */

            document.getElementById(
                'numero-pedido'
            ).textContent = generarNumeroPedido();

            document.getElementById(
                'fecha-pedido'
            ).textContent = fechaFormateada;

            document.getElementById(
                'metodo-confirmacion'
            ).textContent = metodo;

            document.getElementById(
                'mensaje-confirmacion'
            ).textContent =
                '¡Gracias, ' + nombre + '! ' +
                'Tu pedido de demostración ha sido confirmado. ' +
                'Aquí puedes revisar los detalles de tu compra simulada.';

            /*
             * COPIAR PRODUCTOS A CONFIRMACIÓN
             */

            const productosConfirmados =
                document.getElementById(
                    'productos-confirmados'
                );

            productosConfirmados.replaceChildren();

            carrito.forEach(item => {
                const producto = productos.find(
                    producto => producto.id === item.id
                );

                if (!producto) {
                    return;
                }

                productosConfirmados.appendChild(
                    crearFilaProducto(
                        producto,
                        item.cantidad
                    )
                );
            });

            /*
             * MOSTRAR TOTAL
             */

            document.getElementById(
                'total-confirmado'
            ).textContent =
                formatoDinero.format(totalPedido);

            /*
             * CAMBIAR DE PANTALLA
             */

            document.getElementById(
                'checkout'
            ).classList.add('oculto');

            document.getElementById(
                'confirmacion'
            ).classList.remove('oculto');

            /*
             * VACIAR CARRITO DE DEMOSTRACIÓN
             */

            try {
                localStorage.removeItem(CLAVE_CARRITO);
            } catch (error) {
                // La confirmación puede continuar.
            }

            /*
             * REGRESAR AL INICIO DE LA PÁGINA
             */

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        /*
         * INICIALIZACIÓN
         */

        async function iniciarCheckout() {
            try {
                const respuesta = await fetch('api/productos.php', {
                    credentials: 'same-origin'
                });

                if (respuesta.ok) {
                    const datos = await respuesta.json();

                    if (Array.isArray(datos)) {
                        productos = datos.map(producto => ({
                            id: Number(producto.id),
                            nombre: String(producto.nombre || ''),
                            precio: Number(producto.precio) || 0,
                            imagen: String(producto.imagen || '')
                        })).filter(producto => producto.id > 0);
                    }
                }
            } catch (error) {
                productos = [];
            }

            leerCarrito();
            mostrarPedido();
        }

        iniciarCheckout();

    </script>

</body>
</html>
