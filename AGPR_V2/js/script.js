
/* ============================================
   AGPR - EQUIPA TU RUTA
   Carrito y asistente Raider
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  const productos = [
    { id: 1, nombre: 'Casco Integral Sport', categoria: 'cascos', precio: 1899, imagen: 'img/cascos.jpg' },
    { id: 2, nombre: 'Casco Urbano Pro', categoria: 'cascos', precio: 1499, imagen: 'img/cascos.jpg' },
    { id: 3, nombre: 'Guantes Racing', categoria: 'guantes', precio: 649, imagen: 'img/guantes.jpg' },
    { id: 4, nombre: 'Guantes Urbanos', categoria: 'guantes', precio: 449, imagen: 'img/guantes.jpg' },
    { id: 5, nombre: 'Soporte Universal', categoria: 'soportes', precio: 299, imagen: 'img/soportes.jpg' },
    { id: 6, nombre: 'Soporte Ajustable Pro', categoria: 'soportes', precio: 399, imagen: 'img/soportes.jpg' },
    { id: 7, nombre: 'Intercomunicador Rider', categoria: 'intercomunicadores', precio: 1299, imagen: 'img/intercomunicadores.jpg' },
    { id: 8, nombre: 'Intercomunicador Duo', categoria: 'intercomunicadores', precio: 2199, imagen: 'img/intercomunicadores.jpg' }
  ];

  const categorias = [
    'cascos',
    'guantes',
    'soportes',
    'intercomunicadores'
  ];

  const precioMXN = new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency: 'MXN',
    maximumFractionDigits: 0
  });

  const buscarProducto = id =>
    productos.find(p => p.id === Number(id));

  function crear(etiqueta, clase = '', texto) {
    const elemento = document.createElement(etiqueta);

    if (clase) elemento.className = clase;

    if (texto !== undefined) {
      elemento.textContent = texto;
    }

    return elemento;
  }

  /* ============================================
     IDENTIDAD VISUAL
     ============================================ */

  const VERSION_IMAGENES = 'agpr-raider-20261113';

  function rutaImagen(nombre) {
    return `img/${nombre}.png?v=${VERSION_IMAGENES}`;
  }

  // Actualiza las imágenes del logo en la página.
  // Esto evita reutilizar una versión antigua de caché.
  document.querySelectorAll('img').forEach(imagen => {
    const ruta = imagen.getAttribute('src') || '';

    if (/^(?:\.\/)?img\/logo\.png(?:\?.*)?$/i.test(ruta)) {
      imagen.src = rutaImagen('logo');
    }
  });

  // Actualiza el favicon cuando existe.
  document.querySelectorAll('link[rel*="icon"]').forEach(enlace => {
    const ruta = enlace.getAttribute('href') || '';

    if (/isotipo\.png/i.test(ruta)) {
      enlace.href = rutaImagen('isotipo');
    }
  });

  // Protege las imágenes de Raider de reglas CSS
  // que les asignen dimensiones gigantes.
  function limitarImagenRaider(imagen) {
    const reglas = {
      display: 'block',
      width: '100%',
      height: '100%',
      maxWidth: '100%',
      maxHeight: '100%',
      minWidth: '0',
      minHeight: '0',
      objectFit: 'contain',
      position: 'static',
      flexShrink: '1'
    };

    Object.entries(reglas).forEach(([propiedad, valor]) => {
      imagen.style.setProperty(
        propiedad.replace(/[A-Z]/g, letra =>
          '-' + letra.toLowerCase()
        ),
        valor,
        'important'
      );
    });

    return imagen;
  }

  /* ============================================
     CARRITO
     ============================================ */

  const CLAVE_CARRITO = 'agpr_carrito_demo';

  let carrito = [];

  try {
    const guardado = JSON.parse(
      localStorage.getItem(CLAVE_CARRITO) || '[]'
    );

    if (Array.isArray(guardado)) {
      carrito = guardado.map(item => ({
        id: Number(item.id),
        cantidad: Math.min(
          99,
          Math.max(0, Math.floor(Number(item.cantidad) || 0))
        )
      })).filter(item =>
        buscarProducto(item.id) && item.cantidad > 0
      );
    }
  } catch (error) {
    carrito = [];
  }

  const panel = document.getElementById('panel-carrito');
  const fondo = document.getElementById('fondo-carrito');
  const abrirBtn = document.getElementById('abrir-carrito');
  const cerrarBtn = document.getElementById('cerrar-carrito');
  const contenidoCarrito = document.getElementById('productos-carrito');
  const totalCarrito = document.getElementById('total-carrito');
  const contador = document.getElementById('contador-carrito');
  const finalizar = document.getElementById('finalizar-compra');
  const notificacion = document.getElementById('notificacion');

  let avisoTemporizador;
  let focoAnterior = null;

  function avisar(mensaje) {
    if (!notificacion) return;

    notificacion.textContent = mensaje;
    notificacion.classList.add('visible');

    clearTimeout(avisoTemporizador);

    avisoTemporizador = setTimeout(() => {
      notificacion.classList.remove('visible');
    }, 3000);
  }

  function guardarCarrito() {
    try {
      localStorage.setItem(
        CLAVE_CARRITO,
        JSON.stringify(carrito)
      );
    } catch (error) {
      avisar('No se pudo guardar el carrito.');
    }
  }

  function modificarCantidad(id, cambio) {
    const producto = buscarProducto(id);

    if (!producto) return;

    const item = carrito.find(p => p.id === producto.id);

    if (item) {
      item.cantidad = Math.min(99, item.cantidad + cambio);

      if (item.cantidad <= 0) {
        carrito = carrito.filter(p => p.id !== producto.id);
      }
    } else if (cambio > 0) {
      carrito.push({
        id: producto.id,
        cantidad: Math.min(99, cambio)
      });
    }

    guardarCarrito();
    actualizarCarrito();
  }

  function actualizarCarrito() {
    const cantidadTotal = carrito.reduce(
      (suma, item) => suma + item.cantidad,
      0
    );

    const total = carrito.reduce((suma, item) => {
      const producto = buscarProducto(item.id);

      return suma + (
        producto ? producto.precio * item.cantidad : 0
      );
    }, 0);

    if (contador) {
      contador.textContent = String(cantidadTotal);
    }

    if (totalCarrito) {
      totalCarrito.textContent = precioMXN.format(total);
    }

    if (finalizar) {
      finalizar.disabled = carrito.length === 0;
    }

    if (!contenidoCarrito) return;

    contenidoCarrito.replaceChildren();

    if (!carrito.length) {
      contenidoCarrito.append(
        crear(
          'p',
          'carrito-vacio',
          'Tu carrito está vacío. ¡Explora nuestros accesorios!'
        )
      );
      return;
    }

    carrito.forEach(item => {
      const producto = buscarProducto(item.id);

      if (!producto) return;

      const fila = crear('article', 'item-carrito');

      const imagen = crear('img');
      imagen.src = producto.imagen;
      imagen.alt = producto.nombre;

      const datos = crear('div', 'item-carrito-datos');

      datos.append(
        crear('h3', '', producto.nombre),
        crear(
          'p',
          '',
          precioMXN.format(producto.precio * item.cantidad)
        )
      );

      const controles = crear('div', 'item-controles');

      const menos = crear('button', '', '−');
      menos.type = 'button';
      menos.setAttribute(
        'aria-label',
        `Quitar una unidad de ${producto.nombre}`
      );

      menos.addEventListener('click', () => {
        modificarCantidad(producto.id, -1);
      });

      const cantidad = crear(
        'span',
        '',
        String(item.cantidad)
      );

      const mas = crear('button', '', '+');
      mas.type = 'button';
      mas.setAttribute(
        'aria-label',
        `Agregar una unidad de ${producto.nombre}`
      );

      mas.addEventListener('click', () => {
        modificarCantidad(producto.id, 1);
      });

      const quitar = crear(
        'button',
        'quitar-producto',
        'Quitar'
      );

      quitar.type = 'button';

      quitar.addEventListener('click', () => {
        carrito = carrito.filter(
          p => p.id !== producto.id
        );

        guardarCarrito();
        actualizarCarrito();
      });

      controles.append(menos, cantidad, mas, quitar);
      datos.append(controles);
      fila.append(imagen, datos);

      contenidoCarrito.append(fila);
    });
  }

  function abrirCarrito() {
    if (!panel) return;

    cerrarChat();

    focoAnterior = document.activeElement;

    if (fondo) fondo.hidden = false;

    panel.classList.add('abierto');
    panel.setAttribute('aria-hidden', 'false');

    document.body.classList.add('carrito-abierto');

    (cerrarBtn || panel).focus();
  }

  function cerrarCarrito() {
    if (!panel || !panel.classList.contains('abierto')) {
      return;
    }

    panel.classList.remove('abierto');
    panel.setAttribute('aria-hidden', 'true');

    if (fondo) fondo.hidden = true;

    document.body.classList.remove('carrito-abierto');

    if (focoAnterior && focoAnterior.isConnected) {
      focoAnterior.focus();
    }
  }

  abrirBtn?.addEventListener('click', abrirCarrito);
  cerrarBtn?.addEventListener('click', cerrarCarrito);
  fondo?.addEventListener('click', cerrarCarrito);

  finalizar?.addEventListener('click', () => {
    avisar(
      'El proceso de compra estará disponible próximamente.'
    );
  });

  document.getElementById('btn-cuenta')
    ?.addEventListener('click', () => {
      window.location.href = 'cuenta.php';
    });

  document.addEventListener('click', evento => {
    const objetivo = evento.target;

    if (!(objetivo instanceof Element)) return;

    const boton = objetivo.closest(
      '.btn-agregar[data-id]'
    );

    if (!boton) return;

    const producto = buscarProducto(boton.dataset.id);

    if (!producto) return;

    modificarCantidad(producto.id, 1);

    avisar(`${producto.nombre} agregado al carrito`);
  });

  /* ============================================
     RAIDER - ASISTENTE VIRTUAL
     ============================================ */

  const estilo = document.createElement('link');

  estilo.rel = 'stylesheet';
  estilo.href = 'css/asistente.css?v=raider-20261113';

  document.head.append(estilo);

  const ruta = window.location.pathname.toLowerCase();
  const parametros = new URLSearchParams(
    window.location.search
  );

  const esCatalogo = ruta.endsWith('catalogo.php');
  const esProducto = ruta.endsWith('producto.php');

  const productoActual = esProducto
    ? buscarProducto(parametros.get('id'))
    : null;

  const categoriaParametro = parametros.get('categoria');

  const categoriaActual = productoActual?.categoria ||
    (
      esCatalogo && categorias.includes(categoriaParametro)
        ? categoriaParametro
        : null
    );

  const botonFlotante = document.querySelector(
    '.boton-asistente'
  );

  if (!botonFlotante) {
    actualizarCarrito();
    return;
  }

  botonFlotante.setAttribute(
    'aria-label',
    'Abrir Raider, tu copiloto inteligente'
  );

  botonFlotante.setAttribute('aria-expanded', 'false');
  botonFlotante.setAttribute('aria-controls', 'agpr-chat');

  // Se limpia el botón para colocar el nuevo isotipo.
  botonFlotante.replaceChildren();

  // Dimensiones del botón.
  botonFlotante.style.setProperty(
    'width', '66px', 'important'
  );

  botonFlotante.style.setProperty(
    'height', '66px', 'important'
  );

  botonFlotante.style.setProperty(
    'overflow', 'hidden', 'important'
  );

  botonFlotante.style.setProperty(
    'display', 'flex', 'important'
  );

  botonFlotante.style.setProperty(
    'align-items', 'center', 'important'
  );

  botonFlotante.style.setProperty(
    'justify-content', 'center', 'important'
  );

  // Isotipo de la pantera motorizada.
  const iconoBoton = crear('img', 'raider-icono');

  iconoBoton.src = rutaImagen('isotipo');
  iconoBoton.alt = '';

  limitarImagenRaider(iconoBoton);

  botonFlotante.append(iconoBoton);

  /* Ventana principal */

  const ventana = crear('section', 'agpr-chat');

  ventana.id = 'agpr-chat';
  ventana.setAttribute('role', 'dialog');

  ventana.setAttribute(
    'aria-label',
    'Raider, tu copiloto inteligente'
  );

  ventana.hidden = true;

  const cabecera = crear('div', 'agpr-chat-cabecera');
  const avatar = crear('span', 'agpr-chat-avatar');

  const imagenAvatar = crear(
    'img',
    'raider-avatar-imagen'
  );

  imagenAvatar.src = rutaImagen('isotipo');
  imagenAvatar.alt = '';

  limitarImagenRaider(imagenAvatar);

  avatar.append(imagenAvatar);
  avatar.setAttribute('aria-hidden', 'true');

  const tituloContenedor = crear(
    'div',
    'agpr-chat-identidad'
  );

  tituloContenedor.append(
    crear('strong', '', 'RAIDER'),
    crear(
      'small',
      '',
      'Tu copiloto inteligente · Demo'
    )
  );

  const cerrarChatBtn = crear(
    'button',
    'agpr-chat-cerrar',
    '×'
  );

  cerrarChatBtn.type = 'button';

  cerrarChatBtn.setAttribute(
    'aria-label',
    'Cerrar asistente'
  );

  cabecera.append(
    avatar,
    tituloContenedor,
    cerrarChatBtn
  );

  const mensajes = crear('div', 'agpr-chat-mensajes');

  mensajes.setAttribute('role', 'log');
  mensajes.setAttribute('aria-live', 'polite');
  mensajes.setAttribute('aria-relevant', 'additions');

  const opciones = crear('div', 'agpr-chat-opciones');
  const formulario = crear('form', 'agpr-chat-formulario');

  const entrada = crear('input', 'agpr-chat-entrada');

  entrada.type = 'text';
  entrada.maxLength = 250;
  entrada.placeholder = 'Pregúntame por accesorios...';

  entrada.setAttribute(
    'aria-label',
    'Escribe tu pregunta'
  );

  const enviar = crear(
    'button',
    'agpr-chat-enviar',
    '➤'
  );

  enviar.type = 'submit';

  enviar.setAttribute(
    'aria-label',
    'Enviar mensaje'
  );

  formulario.append(entrada, enviar);

  ventana.append(
    cabecera,
    mensajes,
    opciones,
    formulario
  );

  document.body.append(ventana);

  /* Invitación automática */

  const globo = crear(
    'button',
    'agpr-chat-invitacion'
  );

  globo.type = 'button';

  globo.setAttribute(
    'aria-label',
    'Abrir recomendación de Raider'
  );

  globo.hidden = true;

  document.body.append(globo);

  let chatIniciado = false;
  let chatAbierto = false;
  let invitacionMostrada = false;

  let invitacionTimer;
  let ocultarInvitacionTimer;

  function ocultarInvitacion() {
    clearTimeout(ocultarInvitacionTimer);
    globo.hidden = true;
  }

  /* ============================================
     MENSAJES Y RECOMENDACIONES
     ============================================ */

  function agregarMensaje(texto, emisor = 'bot') {
    const clase = emisor === 'usuario'
      ? 'agpr-chat-usuario'
      : 'agpr-chat-bot';

    const mensaje = crear(
      'div',
      `agpr-chat-mensaje ${clase}`,
      texto
    );

    mensajes.append(mensaje);
    mensajes.scrollTop = mensajes.scrollHeight;
  }

  function agregarEnlace(producto) {
    const enlace = crear('a', 'agpr-chat-producto');

    enlace.href = `producto.php?id=${producto.id}`;

    const imagen = crear('img');

    imagen.src = producto.imagen;
    imagen.alt = '';

    const info = crear(
      'span',
      'agpr-chat-producto-info'
    );

    info.append(
      crear('strong', '', producto.nombre),
      crear(
        'small',
        '',
        `${precioMXN.format(producto.precio)} · Ver detalles`
      )
    );

    enlace.append(imagen, info);

    mensajes.append(enlace);
    mensajes.scrollTop = mensajes.scrollHeight;
  }

  function mostrarOpciones(lista) {
    opciones.replaceChildren();

    lista.forEach(({ texto, accion }) => {
      const boton = crear(
        'button',
        'agpr-chat-opcion',
        texto
      );

      boton.type = 'button';

      boton.addEventListener('click', () => {
        agregarMensaje(texto, 'usuario');
        accion();
      });

      opciones.append(boton);
    });
  }

  function recomendarCategoria(categoria) {
    const lista = productos.filter(
      p => p.categoria === categoria
    );

    if (!lista.length) return;

    agregarMensaje(
      `Estas son las opciones de ${categoria} que tenemos en nuestro catálogo de demostración:`
    );

    lista.forEach(agregarEnlace);

    agregarMensaje(
      'Los precios y las características son ilustrativos; todavía no tenemos existencias confirmadas.'
    );

    mostrarOpciones([
      {
        texto: 'Ver otra categoría',
        accion: opcionesGenerales
      },
      {
        texto: '¿Cuál es más económico?',
        accion: () => recomendarEconomico(categoria)
      }
    ]);
  }

  function recomendarEconomico(categoria) {
    const lista = productos
      .filter(p =>
        !categoria || p.categoria === categoria
      )
      .sort((a, b) => a.precio - b.precio);

    const elegido = lista[0];

    if (!elegido) return;

    agregarMensaje(
      `Entre esas opciones, ${elegido.nombre} tiene el precio de demostración más bajo: ${precioMXN.format(elegido.precio)}.`
    );

    agregarEnlace(elegido);
  }

  function opcionesGenerales() {
    agregarMensaje(
      '¿Qué tipo de accesorio te gustaría explorar?'
    );

    mostrarOpciones(
      categorias.map(categoria => ({
        texto:
          categoria.charAt(0).toUpperCase() +
          categoria.slice(1),

        accion: () => recomendarCategoria(categoria)
      }))
    );
  }

  /* ============================================
     BIENVENIDA CONTEXTUAL
     ============================================ */

  function bienvenida() {
    if (chatIniciado) return;

    chatIniciado = true;

    agregarMensaje(
      '¡Qué onda! 🏍️ Soy Raider, tu compañero de ruta. Puedo ayudarte a conocer nuestros accesorios de demostración.'
    );

    if (productoActual) {
      agregarMensaje(
        `Veo que estás revisando ${productoActual.nombre}. Su precio de demostración es ${precioMXN.format(productoActual.precio)}. ¿Quieres conocer otra opción de ${productoActual.categoria}?`
      );

      mostrarOpciones([
        {
          texto: 'Comparar alternativas',
          accion: () =>
            recomendarCategoria(productoActual.categoria)
        },
        {
          texto: 'Ver más categorías',
          accion: opcionesGenerales
        }
      ]);

    } else if (categoriaActual) {
      agregarMensaje(
        `¡Excelente! Estás explorando ${categoriaActual}. ¿Te muestro las opciones que tenemos?`
      );

      mostrarOpciones([
        {
          texto: `Ver ${categoriaActual}`,
          accion: () =>
            recomendarCategoria(categoriaActual)
        },
        {
          texto: 'La opción más económica',
          accion: () =>
            recomendarEconomico(categoriaActual)
        },
        {
          texto: 'Otras categorías',
          accion: opcionesGenerales
        }
      ]);

    } else {
      opcionesGenerales();
    }
  }

  /* ============================================
     ABRIR Y CERRAR RAIDER
     ============================================ */

  function abrirChat() {
    cerrarCarrito();

    clearTimeout(invitacionTimer);
    ocultarInvitacion();

    ventana.hidden = false;
    chatAbierto = true;

    botonFlotante.setAttribute(
      'aria-expanded',
      'true'
    );

    bienvenida();
    entrada.focus();
  }

  function cerrarChat() {
    if (!chatAbierto) return;

    ventana.hidden = true;
    chatAbierto = false;

    botonFlotante.setAttribute(
      'aria-expanded',
      'false'
    );

    botonFlotante.focus();
  }

  botonFlotante.addEventListener('click', () => {
    if (chatAbierto) {
      cerrarChat();
    } else {
      abrirChat();
    }
  });

  cerrarChatBtn.addEventListener(
    'click',
    cerrarChat
  );

  globo.addEventListener('click', abrirChat);

  document.addEventListener('click', evento => {
    if (
      !globo.hidden &&
      !globo.contains(evento.target) &&
      !botonFlotante.contains(evento.target)
    ) {
      ocultarInvitacion();
    }
  });

  /* ============================================
     RESPUESTAS DE RAIDER
     ============================================ */

  function procesarPregunta(pregunta) {
    const texto = pregunta
      .toLocaleLowerCase('es-MX')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');

    const categoria =
      categorias.find(c => texto.includes(c)) ||
      (
        texto.includes('casco') ? 'cascos' :
        texto.includes('guante') ? 'guantes' :
        texto.includes('soporte') ||
        texto.includes('celular') ? 'soportes' :
        texto.includes('intercomunicador')
          ? 'intercomunicadores'
          : null
      );

    if (categoria) {
      recomendarCategoria(categoria);
      return;
    }

    if (
      /barato|economico|precio|cuanto cuesta|costo/.test(texto)
    ) {
      recomendarEconomico(categoriaActual);
      return;
    }

    if (/carrito|comprar|pedido|pagar/.test(texto)) {
      agregarMensaje(
        'Puedes agregar productos al carrito desde el catálogo o desde sus detalles. El pago todavía no está disponible porque esta es una demostración.'
      );

      mostrarOpciones([
        {
          texto: 'Explorar productos',
          accion: opcionesGenerales
        }
      ]);

      return;
    }

    if (/hola|buenas|hey|que onda/.test(texto)) {
      agregarMensaje(
        '¡Qué onda! 😎 ¿Buscas cascos, guantes, soportes o intercomunicadores?'
      );

      mostrarOpciones(
        categorias.map(categoria => ({
          texto: categoria,
          accion: () =>
            recomendarCategoria(categoria)
        }))
      );

      return;
    }

    agregarMensaje(
      'Todavía estoy aprendiendo. Por ahora puedo recomendar accesorios, mostrar precios de demostración y ayudarte a explorar el catálogo.'
    );

    mostrarOpciones([
      {
        texto: 'Ver categorías',
        accion: opcionesGenerales
      }
    ]);
  }

  formulario.addEventListener('submit', evento => {
    evento.preventDefault();

    const pregunta = entrada.value.trim();

    if (!pregunta) return;

    agregarMensaje(pregunta, 'usuario');

    entrada.value = '';

    procesarPregunta(pregunta);
  });

  /* ============================================
     ACCESIBILIDAD Y TECLADO
     ============================================ */

  document.addEventListener('keydown', evento => {
    if (evento.key === 'Escape') {
      if (chatAbierto) {
        cerrarChat();
      } else {
        cerrarCarrito();
      }
    }

    const contenedorActivo = chatAbierto
      ? ventana
      : panel?.classList.contains('abierto')
        ? panel
        : null;

    if (
      evento.key !== 'Tab' ||
      !contenedorActivo
    ) {
      return;
    }

    const focos = [
      ...contenedorActivo.querySelectorAll(
        'button:not([disabled]), a[href], input:not([disabled])'
      )
    ];

    if (!focos.length) return;

    const primero = focos[0];
    const ultimo = focos[focos.length - 1];

    if (
      evento.shiftKey &&
      document.activeElement === primero
    ) {
      evento.preventDefault();
      ultimo.focus();

    } else if (
      !evento.shiftKey &&
      document.activeElement === ultimo
    ) {
      evento.preventDefault();
      primero.focus();
    }
  });

  /* ============================================
     INVITACIÓN PROACTIVA
     ============================================ */

  try {
    if (
      !sessionStorage.getItem(
        'agpr_invitacion_mostrada'
      )
    ) {
      invitacionTimer = setTimeout(() => {
        if (
          chatAbierto ||
          panel?.classList.contains('abierto') ||
          invitacionMostrada
        ) {
          return;
        }

        invitacionMostrada = true;

        const texto = productoActual
          ? `¿Te ayudo a comparar ${productoActual.nombre}? 🏍️`
          : categoriaActual
            ? `¿Buscas ${categoriaActual}? Te puedo recomendar opciones. 🏍️`
            : '¡Hey! 🏍️ ¿Te ayudo a elegir tu próximo accesorio?';

        globo.textContent = texto;
        globo.hidden = false;

        sessionStorage.setItem(
          'agpr_invitacion_mostrada',
          '1'
        );

        ocultarInvitacionTimer = setTimeout(
          ocultarInvitacion,
          6000
        );

      }, 3500);
    }
  } catch (error) {
    // El chat continúa funcionando si el navegador
    // bloquea el almacenamiento de sesión.
  }

  actualizarCarrito();
});
