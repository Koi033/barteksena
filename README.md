# Bartek — Sistema de Gestión de Bares

**Versión:** 1.0.0 · **Lenguaje:** PHP 8.1+ · **BD:** MySQL 8 · **Patrón:** MVC  
**Grupo 2 · SENA ADSO · Ficha 3171693**

---

## Estructura del proyecto

```
bartek/
├── app/
│   ├── controllers/        ← Controladores (lógica HTTP)
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── EmpleadoController.php
│   │   ├── InventarioController.php
│   │   ├── VentaController.php
│   │   ├── HorarioController.php
│   │   ├── MenuController.php
│   │   └── ContactoController.php
│   ├── models/             ← Modelos (acceso a BD con PDO)
│   │   ├── BaseModel.php
│   │   ├── UsuarioModel.php
│   │   ├── EmpleadoModel.php
│   │   ├── InventarioModel.php
│   │   ├── VentaModel.php
│   │   ├── HorarioModel.php
│   │   ├── MenuModel.php
│   │   ├── NotificacionModel.php
│   │   └── ContactoModel.php
│   └── views/              ← Vistas PHP (sin lógica de negocio)
│       ├── layouts/
│       │   ├── public.php
│       │   ├── auth.php
│       │   └── dashboard.php
│       ├── auth/           (login, registro)
│       ├── dashboard/
│       ├── empleados/
│       ├── inventario/
│       ├── ventas/
│       ├── horarios/
│       ├── menu/
│       └── public/         (inicio, nosotros, servicios, contacto, 404)
├── config/
│   ├── app.php             ← Constantes globales
│   ├── database.php        ← Singleton PDO
│   └── sesion.php          ← Manejo seguro de sesiones + CSRF
├── public/                 ← Document root del servidor web
│   ├── index.php           ← Front Controller (único punto de entrada)
│   ├── .htaccess           ← URL rewriting + cabeceras de seguridad
│   ├── css/
│   │   ├── style.css
│   │   ├── public.css
│   │   ├── auth.css
│   │   ├── dashboard.css
│   │   └── datatables-custom.css
│   ├── js/
│   │   ├── main.js
│   │   └── dashboard.js
│   └── images/             ← Imágenes (logos, stock, etc.)
└── sql/
    └── bartek.sql          ← Script de creación de BD
```

---



## Seguridad implementada

| Medida | Descripción |
|---|---|
| **Prepared Statements** | Todas las consultas usan PDO con parámetros ligados — sin concatenación SQL |
| **Bcrypt** | Contraseñas hasheadas con `password_hash()` (coste 12) |
| **Tokens CSRF** | Cada formulario genera y valida un token único de 32 bytes |
| **Sesiones seguras** | `httponly`, `samesite=Lax`, regeneración de ID al login |
| **XSS** | Todo output en vistas pasa por `htmlspecialchars()` con `ENT_QUOTES` |
| **Borrado lógico** | Empleados e inventario se marcan `activo=0`, no se eliminan físicamente |
| **Validación de entrada** | `filter_input()`, `strip_tags()`, longitudes máximas en todos los campos |
| **Cabeceras HTTP** | `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection` via `.htaccess` |
| **Logout completo** | Destruye `$_SESSION`, elimina cookie, llama a `session_destroy()` |

---

## Módulos del sistema

| Módulo | Ruta | Descripción |
|---|---|---|
| Inicio público | `/` | Landing page con carrusel |
| Login | `/login` | Autenticación con bcrypt |
| Registro | `/registro` | Creación de cuenta con validación |
| Dashboard | `/dashboard` | Métricas y notificaciones en tiempo real |
| Empleados | `/empleados` | CRUD completo con DataTables |
| Inventario | `/inventario` | Gestión de bebidas, alertas de stock |
| Ventas | `/ventas` | Transacciones y top bebidas |
| Horarios | `/horarios` | Turnos, aprobación/rechazo |
| Menú Interactivo | `/menu` | Categorías del menú digital |
| Contacto | `/contacto` | Formulario público con CSRF |

---

## Estándares de código

- **PSR-1 / PSR-12**: convenciones de nombrado y formato PHP
- **Comentarios PHPDoc** en todos los métodos
- **Sin lógica de negocio en vistas**: las vistas solo iteran arrays ya preparados
- **CSS separado por módulo** en `public/css/` — sin estilos inline en PHP
- **Escalabilidad**: agregar un módulo nuevo = nuevo Model + Controller + carpeta de vistas
