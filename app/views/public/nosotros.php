<?php /* app/views/nosotros/index.php — Sección Sobre Nosotros Bartek */ ?>

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

  /* Contenedor Principal */
  .about-wrapper {
    max-width: 1100px;
    margin: 2.5rem auto;
    padding: 0 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }

  /* ENCABEZADO TIPO BANNER TECH */
  .about-hero-banner {
    background: linear-gradient(135deg, #1f1f1f 0%, #151515 100%);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
  }

  .about-hero-banner::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--orange-main), #ffb700);
    box-shadow: 0 0 15px var(--orange-glow);
  }

  .hero-tag {
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
    margin-bottom: 1rem;
  }

  .hero-content h1 {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--text-white);
    margin: 0 0 1rem 0;
    line-height: 1.2;
  }

  .hero-content h1 span {
    color: var(--orange-main);
    text-shadow: 0 0 15px var(--orange-glow);
  }

  .hero-content p {
    color: var(--text-muted);
    font-size: 1rem;
    line-height: 1.7;
    margin: 0;
    max-width: 850px;
  }

  /* GRID PRINCIPAL: MISIÓN Y VISIÓN EN BLOQUES DE IMPACTO */
  .mv-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
  }

  .mv-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 20px;
    padding: 2.2rem;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
  }

  .mv-card:hover {
    transform: translateY(-5px);
    border-color: var(--orange-main);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.8), 0 0 25px var(--orange-glow);
  }

  .mv-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .mv-icon-glow {
    width: 65px;
    height: 65px;
    background: radial-gradient(circle at 30% 30%, #2e2e2e 0%, #1a1a1a 100%);
    border: 2px solid var(--orange-main);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--orange-main);
    box-shadow: 0 0 20px var(--orange-glow);
    transition: transform 0.3s ease;
  }

  .mv-card:hover .mv-icon-glow {
    transform: scale(1.1) rotate(-5deg);
  }

  .mv-number-badge {
    font-size: 2.5rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.05);
    letter-spacing: -1px;
    transition: color 0.3s ease;
  }

  .mv-card:hover .mv-number-badge {
    color: rgba(255, 123, 0, 0.2);
  }

  .mv-card h2 {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text-white);
    margin: 0 0 0.8rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .mv-card p {
    color: var(--text-muted);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
  }

  /* BARRA DECORATIVA EN BASE DE TARJETAS */
  .mv-bottom-bar {
    width: 100%;
    height: 3px;
    background: var(--border-color);
    margin-top: 1.8rem;
    border-radius: 2px;
    position: relative;
    overflow: hidden;
  }

  .mv-bottom-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, var(--orange-main), transparent);
    transition: left 0.5s ease;
  }

  .mv-card:hover .mv-bottom-bar::after {
    left: 100%;
  }
</style>

<div class="about-wrapper">
    
    <!-- BANNER HERO DE NUESTRA HISTORIA -->
    <section class="about-hero-banner">
        <div class="hero-tag">
            <i class="fas fa-beer-mug-empty"></i> Proyecto Bartek
        </div>
        <div class="hero-content">
            <h1>Nuestra <span>Historia</span></h1>
            <p>
                Somos <strong>Bartek</strong>, un proyecto de desarrollo web nacido en el SENA ADSO. Nuestro objetivo es ofrecer soluciones digitales para bares a través de una plataforma de gestión completa, intuitiva y accesible.
            </p>
        </div>
    </section>

    <!-- GRID DE MISIÓN Y VISIÓN -->
    <div class="mv-grid">
        
        <!-- TARJETA MISIÓN -->
        <article class="mv-card">
            <div>
                <div class="mv-header">
                    <div class="mv-icon-glow">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <span class="mv-number-badge">01</span>
                </div>
                <h2>Misión</h2>
                <p>
                    Proporcionar a los bares una plataforma eficiente y fácil de usar para gestionar sus operaciones diarias, mejorar la experiencia del cliente y aumentar su rentabilidad.
                </p>
            </div>
            <div class="mv-bottom-bar"></div>
        </article>

        <!-- TARJETA VISIÓN -->
        <article class="mv-card">
            <div>
                <div class="mv-header">
                    <div class="mv-icon-glow">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <span class="mv-number-badge">02</span>
                </div>
                <h2>Visión</h2>
                <p>
                    Convertirnos en la plataforma de gestión de bares líder en el mercado, reconocida por ser útil, segura y altamente escalable.
                </p>
            </div>
            <div class="mv-bottom-bar"></div>
        </article>

    </div>

</div>