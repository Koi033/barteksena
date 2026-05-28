/**
 * js/main.js — Bartek · JavaScript global (páginas públicas)
 * Carrusel de imágenes y comportamientos comunes.
 */

'use strict';

/* ── Carrusel ─────────────────────────────────────────────────────── */
let indice = 0;

/**
 * Mueve el carrusel hacia la derecha (imagen siguiente).
 */
function moverDerecha() {
    const track = document.getElementById('track');
    if (!track) return;
    indice = (indice + 1) % track.children.length;
    actualizarPosicion();
}

/**
 * Mueve el carrusel hacia la izquierda (imagen anterior).
 */
function moverIzquierda() {
    const track = document.getElementById('track');
    if (!track) return;
    indice = (indice - 1 + track.children.length) % track.children.length;
    actualizarPosicion();
}

/**
 * Aplica la transformación CSS para mostrar la imagen en el índice actual.
 */
function actualizarPosicion() {
    const track = document.getElementById('track');
    if (!track) return;
    track.style.transform = `translateX(${indice * -100}%)`;
}

/* ── Slideshow de fondo (nosotros / servicios) ───────────────────── */
(function initSlideshow() {
    const slideshow = document.getElementById('background-slideshow');
    if (!slideshow) return;

    const imagenes = [
        'images/stock4.jpg',
        'images/stock5.jpg',
        'images/stock6.jpg',
    ];
    let idx = 0;

    /** Cambia la imagen de fondo del slideshow. */
    function cambiarFondo() {
        slideshow.style.backgroundImage = `url(${imagenes[idx]})`;
        idx = (idx + 1) % imagenes.length;
    }

    cambiarFondo();
    setInterval(cambiarFondo, 6000);
})();

/* ── Auto-cierre de mensajes flash ───────────────────────────────── */
(function autoCloseFlash() {
    setTimeout(() => {
        document.querySelectorAll('.flash').forEach(el => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 5000); // Desaparece a los 5 segundos
})();
