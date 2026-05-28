-- ============================================================
-- BARTEK - Sistema de Gestión de Bares
-- Script SQL para MySQL
-- Versión: 1.0
-- Autor: Grupo 2 SENA ADSO Ficha 3171693
-- ============================================================

CREATE DATABASE IF NOT EXISTS bartek_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bartek_db;

-- ============================================================
-- TABLA: usuarios
-- Almacena los datos de acceso y rol de cada usuario del sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(100)    NOT NULL,
    email       VARCHAR(150)    NOT NULL UNIQUE,
    telefono    VARCHAR(20)     DEFAULT NULL,
    usuario     VARCHAR(60)     NOT NULL UNIQUE,
    contrasena  VARCHAR(255)    NOT NULL,          -- bcrypt hash
    rol         ENUM('dueno','empleado')  NOT NULL DEFAULT 'empleado',
    activo      TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: empleados
-- Información laboral del personal del bar
-- ============================================================
CREATE TABLE IF NOT EXISTS empleados (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    usuario_id      INT UNSIGNED    DEFAULT NULL,   -- FK opcional a usuarios
    nombre_completo VARCHAR(150)    NOT NULL,
    puesto          VARCHAR(80)     NOT NULL,
    departamento    VARCHAR(80)     NOT NULL,
    email           VARCHAR(150)    NOT NULL,
    telefono        VARCHAR(20)     DEFAULT NULL,
    activo          TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: categorias_menu
-- Categorías de bebidas del menú interactivo
-- ============================================================
CREATE TABLE IF NOT EXISTS categorias_menu (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(80)     NOT NULL,
    descripcion TEXT            DEFAULT NULL,
    activa      TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: inventario (bebidas)
-- Control de stock de cada bebida disponible en el bar
-- ============================================================
CREATE TABLE IF NOT EXISTS inventario (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    categoria_id        INT UNSIGNED        NOT NULL,
    codigo              VARCHAR(20)         NOT NULL UNIQUE,
    nombre              VARCHAR(120)        NOT NULL,
    stock_actual        INT UNSIGNED        NOT NULL DEFAULT 0,
    stock_minimo        INT UNSIGNED        NOT NULL DEFAULT 5,   -- nivel para alertas
    precio_unitario     DECIMAL(10, 2)      NOT NULL DEFAULT 0.00,
    activo              TINYINT(1)          NOT NULL DEFAULT 1,
    actualizado_en      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_en           DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (categoria_id) REFERENCES categorias_menu(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: ventas
-- Cabecera de cada transacción / apertura de mesa
-- ============================================================
CREATE TABLE IF NOT EXISTS ventas (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    empleado_id     INT UNSIGNED    DEFAULT NULL,
    mesa            VARCHAR(30)     DEFAULT NULL,
    total           DECIMAL(10, 2)  NOT NULL DEFAULT 0.00,
    estado          ENUM('abierto','cerrado','cancelado') NOT NULL DEFAULT 'abierto',
    creado_en       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cerrado_en      DATETIME        DEFAULT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: detalle_ventas
-- Líneas de cada venta (qué bebida, cuántas, a qué precio)
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_ventas (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    venta_id        INT UNSIGNED    NOT NULL,
    inventario_id   INT UNSIGNED    NOT NULL,
    cantidad        INT UNSIGNED    NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2)  NOT NULL,
    subtotal        DECIMAL(10, 2)  NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (venta_id)      REFERENCES ventas(id)     ON DELETE CASCADE,
    FOREIGN KEY (inventario_id) REFERENCES inventario(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: horarios
-- Turnos de trabajo asignados a cada empleado
-- ============================================================
CREATE TABLE IF NOT EXISTS horarios (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    empleado_id     INT UNSIGNED    NOT NULL,
    fecha           DATE            NOT NULL,
    hora_inicio     TIME            NOT NULL,
    hora_fin        TIME            NOT NULL,
    estado          ENUM('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
    creado_en       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: contacto_mensajes
-- Mensajes enviados a través del formulario de contacto
-- ============================================================
CREATE TABLE IF NOT EXISTS contacto_mensajes (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(100)    NOT NULL,
    correo      VARCHAR(150)    NOT NULL,
    mensaje     TEXT            NOT NULL,
    leido       TINYINT(1)      NOT NULL DEFAULT 0,
    creado_en   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA: notificaciones
-- Avisos internos del sistema (pedidos, stock bajo, etc.)
-- ============================================================
CREATE TABLE IF NOT EXISTS notificaciones (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    tipo        ENUM('pedido','stock','empleado','caja','sistema') NOT NULL DEFAULT 'sistema',
    titulo      VARCHAR(200)    NOT NULL,
    descripcion TEXT            DEFAULT NULL,
    leida       TINYINT(1)      NOT NULL DEFAULT 0,
    creado_en   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATOS DE EJEMPLO
-- ============================================================

-- Usuario administrador por defecto (contraseña: Admin1234!)
INSERT INTO usuarios (nombre, email, telefono, usuario, contrasena, rol) VALUES
('Administrador', 'admin@bartek.com', '555-0001', 'admin',
 '$2y$12$YourHashHere', 'dueno');

-- Categorías de menú
INSERT INTO categorias_menu (nombre, descripcion) VALUES
('Cervezas',   'Variedad de cervezas nacionales e importadas.'),
('Vinos',      'Selección de vinos tintos, blancos y rosados.'),
('Cócteles',   'Cócteles clásicos y de autor para cada gusto.'),
('Licores',    'Whiskies, rones, vodkas y más.'),
('Refrescos',  'Bebidas sin alcohol para todos.');

-- Inventario inicial
INSERT INTO inventario (categoria_id, codigo, nombre, stock_actual, stock_minimo, precio_unitario) VALUES
(4, 'BEB001', 'Whisky Escocés',        15, 5, 16.00),
(1, 'BEB002', 'Cerveza Artesanal IPA',  8, 5,  7.50),
(2, 'BEB003', 'Vino Tinto Malbec',      3, 5, 12.00),
(4, 'BEB004', 'Ron Añejo',             25, 5, 10.00),
(4, 'BEB005', 'Vodka Premium',          6, 5,  4.00);

-- Notificaciones de ejemplo
INSERT INTO notificaciones (tipo, titulo, descripcion) VALUES
('pedido',   'Nuevo pedido en Mesa 5',    '2 Cervezas, 1 Ración de Patatas Bravas'),
('stock',    'Alerta de reposición',      'Quedaron pocas existencias de Cerveza Lager.'),
('empleado', 'Nuevo empleado contratado', 'Sofía Rodríguez se ha unido al equipo de camareros.'),
('caja',     'Cierre de caja diario',     'El cierre de caja del día ha sido completado.');
