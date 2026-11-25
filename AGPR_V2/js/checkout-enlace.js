
/*
 * AGPR - Enlace al checkout
 * Corrige el botón Finalizar compra
 * sin modificar el carrito ni el asistente Raider.
 */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    const botonFinalizar = document.getElementById(
        'finalizar-compra'
    );

    if (!botonFinalizar) {
        return;
    }

    /*
     * Capturamos el clic antes de que llegue
     * al controlador anterior de script.js.
     * Así evitamos el mensaje "próximamente".
     */
    botonFinalizar.addEventListener('click', (evento) => {
        if (botonFinalizar.disabled) {
            return;
        }

        evento.preventDefault();
        evento.stopImmediatePropagation();

        window.location.href = 'checkout.php';
    }, true);
});
