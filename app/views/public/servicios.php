<?php /* app/views/servicios/index.php — Sección de Servicios Bartek */ ?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

  :root {
    --bg-dark: #121212;
    --card-bg: #1e1e1e;
    --card-inner: #262626;
    --orange-main: #ff7b00;
    --orange-hover: #e66e00;
    --orange-glow: rgba(255, 123, 0, 0.45);
    --text-white: #ffffff;
    --text-muted: #a0a0a0;
    --border-color: #333333;
  }

  body {
    background-color: var(--bg-dark);
    font-family: 'Plus Jakarta Sans', sans-serif;
  }

  /* Contenedor Principal Centrado */
  .services-wrapper {
    max-width: 1100px;
    margin: 2.5rem auto;
    padding: 0 1.5rem;
  }

  /* Encabezado */
  .services-header {
    text-align: center;
    margin-bottom: 3rem;
  }

  .services-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 123, 0, 0.12);
    border: 1px solid var(--orange-main);
    color: var(--orange-main);
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.8rem;
  }

  .services-header h1 {
    font-size: 2.3rem;
    font-weight: 800;
    color: var(--text-white);
    margin: 0;
    letter-spacing: -0.5px;
  }

  .services-header h1 span {
    color: var(--orange-main);
    text-shadow: 0 0 15px var(--orange-glow);
  }

  .services-header p {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin-top: 0.5rem;
  }

  /* Grid de 2x2 para los servicios */
  .services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 1.5rem;
  }

  /* Tarjetas Interactivas */
  .service-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2.2rem;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Borde neón superior al pasar el mouse */
  .service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--orange-main), #ffb700);
    opacity: 0.4;
    transition: opacity 0.3s ease, height 0.3s ease;
  }

  .service-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 123, 0, 0.6);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.8), 0 0 25px var(--orange-glow);
  }

  .service-card:hover::before {
    opacity: 1;
    height: 5px;
  }

  /* Encabezado interno de la tarjeta */
  .service-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
  }

  .service-icon-box {
    width: 60px;
    height: 60px;
    background: radial-gradient(circle at 30% 30%, #2e2e2e 0%, #1a1a1a 100%);
    border: 2px solid var(--orange-main);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: var(--orange-main);
    box-shadow: 0 0 18px var(--orange-glow);
    transition: transform 0.3s ease;
  }

  .service-card:hover .service-icon-box {
    transform: scale(1.1) rotate(4deg);
  }

  .service-num {
    font-size: 2.2rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.06);
    letter-spacing: -1px;
    transition: color 0.3s ease;
  }

  .service-card:hover .service-num {
    color: rgba(255, 123, 0, 0.25);
  }

  /* Títulos y Descripciones */
  .service-card h2 {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--text-white);
    margin: 0 0 0.75rem 0;
  }

  .service-card p {
    color: var(--text-muted);
    font-size: 0.92rem;
    line-height: 1.65;
    margin: 0;
  }

  /* Responsive para móviles */
  @media (max-width: 550px) {
    .services-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="services-wrapper">

    <!-- CABECERA -->
    <header class="services-header">
        <div class="services-tag">
            <i class="fas fa-cubes"></i> Soluciones Modulares
        </div>
        <h1>Nuestros <span>Servicios</span></h1>
        <p>Herramientas potentes para optimizar la gestión de tu establecimiento en tiempo real.</p>
    </header>

    <!-- GRID 2x2 DE SERVICIOS -->
    <div class="services-grid">

        <!-- SERVICIO 1 -->
        <article class="service-card">
            <div>
                <div class="service-card-top">
                    <div class="service-icon-box">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <span class="service-num">01</span>
                </div>
                <h2>Inventario y Alertas de Stock</h2>
                <p>
                    Control de unidades disponibles con alertas automáticas cuando el stock baja del mínimo configurado. Nunca te quedes sin tus bebidas más vendidas.
                </p>
            </div>
        </article>

        <!-- SERVICIO 2 -->
        <article class="service-card">
            <div>
                <div class="service-card-top">
                    <div class="service-icon-box">
                        <i class="fas fa-users-gear"></i>
                    </div>
                    <span class="service-num">02</span>
                </div>
                <h2>Gestión de Empleados y Horarios</h2>
                <p>
                    Administra tu personal, asigna turnos y aprueba horarios desde un panel centralizado y responsivo.
                </p>
            </div>
        </article>

        <!-- SERVICIO 3 -->
        <article class="service-card">
            <div>
                <div class="service-card-top">
                    <div class="service-icon-box">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="service-num">03</span>
                </div>
                <h2>Análisis de Ventas</h2>
                <p>
                    Visualiza las transacciones del día, el mes y las bebidas más vendidas. Genera reportes para tomar mejores decisiones.
                </p>
            </div>
        </article>

        <!-- SERVICIO 4 -->
<article class="service-card">
    <div>
        <div class="service-card-top">
            <div class="service-icon-box">
                <!-- Icono actualizado para garantizar compatibilidad -->
                <i class="fas fa-glass-martini-alt"></i>
            </div>
            <span class="service-num">04</span>
        </div>
        <h2>Menú Interactivo</h2>
        <p>
            Crea y gestiona las categorías y bebidas de tu menú digital, integrado directamente con el inventario en tiempo real.
        </p>
    </div>
</article>
    </div>

</div>