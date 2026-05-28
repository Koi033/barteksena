/**
 * js/dashboard.js — Bartek · JavaScript del panel interno
 * Inicializa DataTables, el toggle del sidebar y el cierre de modales.
 * Requiere jQuery y DataTables cargados previamente en el layout.
 */

'use strict';

$(function () {

    /* ── Inicializar DataTables en todas las tablas marcadas ──────── */
    /**
     * Configuración común para todas las DataTables del proyecto.
     * - language: traducción al español
     * - responsive: adaptación a pantallas pequeñas
     * - pageLength: 10 filas por defecto
     */
    const dtConfig = {
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        responsive: true,
        // Desactivar paginación propia de DT cuando se usa paginación server-side
        // (si la tabla tiene la clase 'dt-no-paging' se deshabilita)
        initComplete: function () {
            // Mover el buscador de DT al costado del search-bar si existe
        }
    };

    // Inicializar cada tabla marcada con la clase .bartek-datatable
    $('.bartek-datatable').each(function () {
        const $tabla = $(this);
        // Si la tabla ya fue inicializada (en vistas con múltiples renders), saltar
        if ($.fn.DataTable.isDataTable($tabla)) return;

        let config = Object.assign({}, dtConfig);

        // Tablas con paginación server-side no necesitan paginación de DT
        if ($tabla.hasClass('dt-server-side')) {
            config.paging    = false;
            config.searching = false;
            config.info      = false;
        }

        $tabla.DataTable(config);
    });

    /* ── Toggle del Sidebar en móvil ──────────────────────────────── */
    const btnToggle = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('sidebar');

    if (btnToggle && sidebar) {
        /**
         * Alterna la visibilidad del menú lateral en pantallas pequeñas.
         */
        btnToggle.addEventListener('click', function () {
            sidebar.classList.toggle('sidebar-collapsed');
        });
    }

    /* ── Cerrar modales haciendo click fuera ─────────────────────── */
    /**
     * Si el usuario hace click directamente sobre el overlay (fondo oscuro)
     * del modal, éste se oculta.
     */
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    });

    /* ── Cerrar modal con tecla Escape ───────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay').forEach(function (m) {
                m.style.display = 'none';
            });
        }
    });

    /* ── Confirmación de eliminación ─────────────────────────────── */
    /**
     * Formularios marcados con data-confirm muestran un diálogo antes
     * de enviar, como refuerzo adicional a los atributos onsubmit inline.
     */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const mensaje = form.dataset.confirm || '¿Estás seguro?';
            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });

    /* ── Resaltar enlace activo del sidebar (extra) ──────────────── */
    /**
     * Compara la URL actual con el href de cada enlace del menú
     * y agrega la clase 'active' al más específico que coincida.
     */
    const rutaActual = window.location.pathname;
    document.querySelectorAll('.sidebar-menu a').forEach(function (enlace) {
        const href = enlace.getAttribute('href');
        if (href && rutaActual.startsWith(href) && href !== '/') {
            enlace.classList.add('active');
        }
    });

});
