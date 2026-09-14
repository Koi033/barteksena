# Bartek — Sistema de Gestión de Bares

**Versión:** 1.0.0 · **Lenguaje:** PHP 8.1+ · **BD:** MySQL 8 · **Patrón:** MVC  
**Grupo 2 · SENA ADSO · Ficha 3171693**

---

## Estructura del proyecto

```
barteksena/
├── .gitignore
├── .htaccess                         ← Reglas de acceso y reescritura en la raíz
├── Dockerfile                        ← Configuración de la imagen Docker
├── composer.json                     ← Dependencias PHP
├── composer.lock                     ← Versiones bloqueadas de dependencias
├── index.php                         ← Punto de entrada de la aplicación
├── README.md
├── app/
│   ├── controllers/                  ← Controladores y lógica HTTP
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── ContactoController.php
│   │   ├── DashboardController.php
│   │   ├── EmpleadoController.php
│   │   ├── HorarioController.php
│   │   ├── InventarioController.php
│   │   ├── MenuController.php
│   │   ├── PasswordController.php
│   │   ├── PerfilController.php
│   │   ├── PuntosController.php
│   │   ├── ReporteController.php
│   │   ├── ReservaController.php
│   │   └── VentaController.php
│   ├── models/                       ← Modelos y acceso a la base de datos
│   │   ├── BaseModel.php
│   │   ├── ContactoModel.php
│   │   ├── EmpleadoModel.php
│   │   ├── HorarioModel.php
│   │   ├── InventarioModel.php
│   │   ├── MenuModel.php
│   │   ├── NotificacionModel.php
│   │   ├── PuntosModel.php
│   │   ├── ReservaModel.php
│   │   ├── UsuarioModel.php
│   │   └── VentaModel.php
│   └── views/                        ← Vistas PHP
│       ├── auth/
│       │   ├── login.php
│       │   ├── recuperar_password.php
│       │   ├── registro.php
│       │   └── restablecer_password.php
│       ├── dashboard/index.php
│       ├── empleados/
│       │   ├── formulario.php
│       │   └── index.php
│       ├── horarios/
│       │   ├── index.php
│       │   └── mi_horario.php
│       ├── inventario/
│       │   ├── formulario.php
│       │   └── index.php
│       ├── layouts/
│       │   ├── auth.php
│       │   ├── dashboard.php
│       │   └── public.php
│       ├── menu/index.php
│       ├── mesas/
│       │   ├── dashboard_mesas.php
│       │   └── mesa_detalle.php
│       ├── perfil/index.php
│       ├── puntos/
│       │   ├── editar.php
│       │   ├── index.php
│       │   └── listado.php
│       ├── public/
│       │   ├── 404.php
│       │   ├── contacto.php
│       │   ├── inicio.php
│       │   ├── nosotros.php
│       │   └── servicios.php
│       ├── reportes/index.php
│       ├── reservas/reservas.php
│       └── ventas/index.php
├── config/
│   ├── app.php                       ← Configuración general
│   ├── database.php                  ← Conexión Singleton PDO
│   ├── sesion.php                    ← Sesiones y protección CSRF
│   └── setting.php                   ← Ajustes de la aplicación
├── public/                           ← Document root del servidor web
│   ├── .htaccess                     ← URL rewriting y cabeceras de seguridad
│   ├── index.php                     ← Front Controller público
│   ├── publico.php                   ← Entrada para páginas públicas
│   ├── api/
│   │   ├── check_mesa.php
│   │   └── crear_reserva.php
│   ├── css/
│   │   ├── auth.css
│   │   ├── calendario-horario.css
│   │   ├── dashboard.css
│   │   ├── datatables-custom.css
│   │   ├── mesa-detalle.css
│   │   ├── mesas.css
│   │   ├── public.css
│   │   ├── recuperar.css
│   │   ├── reservas.css
│   │   ├── style.css
│   │   └── ventas.css
│   ├── images/
│   │   ├── README.txt
│   │   ├── img.jpg, login_side.jpg, logo.1.png
│   │   └── stock1.jpg ... stock8.jpg
│   ├── js/
│   │   ├── dashboard.js
│   │   ├── main.js
│   │   └── validaciones-auth.js
│   └── uploads/                      ← Archivos subidos por usuarios
│       └── .gitkeep y archivos multimedia
├── sql/
│   ├── bartek_db_1.4.sql
│   └── bartek_db_1.5.sql             ← Versiones del esquema de BD
└── vendor/                           ← Dependencias instaladas por Composer
    ├── autoload.php
    ├── composer/
    └── phpmailer/phpmailer/
```

> `vendor/` y los archivos de `public/uploads/` pueden variar según las dependencias instaladas y el contenido cargado en cada entorno.

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
