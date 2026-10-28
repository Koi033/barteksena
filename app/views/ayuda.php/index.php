<?php /* app/views/ayuda/index.php - Manual de usuario */ ?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-circle-question" aria-hidden="true"></i> Centro de Ayuda</h1>
    <p class="page-subtitle">Manual de usuario de Bartek.</p>
</div>

<div class="ayuda-lista">

    <details class="ayuda-item" id="Introduccion">
        <summary><i class="fas fa-house"></i> Introducción</summary>
        <div class="ayuda-contenido">
            <p>El presente manual de usuario tiene como finalidad explicar el funcionamiento del sistema Bartek, una herramienta diseñada para facilitar la administración y organización de bares. El sistema permite gestionar diferentes actividades del negocio, como las mesas, el inventario, los empleados, las ventas, los horarios, los reportes y los puntos de fidelización de los clientes.
                Esta vista sirve como guía para que los usuarios puedan conocer las funciones del sistema y aprender a utilizarlas de manera sencilla.
                .</p>
        </div>
    </details>
    <details class="ayuda-item" id="Objetivo">
        <summary><i class="fas fa-house"></i> Objetivo</summary>
        <div class="ayuda-contenido">
            <p>Orientar a los usuarios en el manejo del sistema Bartek mediante instrucciones claras que les permitan utilizar sus diferentes módulos y realizar las actividades necesarias para la gestión del bar.
            </p>
        </div>
    </details>

    <?php if ($rolUsuario === 'dueno'): ?>
        <details class="ayuda-item" id="empleados">
            <summary><i class="fas fa-users"></i> Empleados</summary>
            <div class="ayuda-contenido">
                <ol>
                    <li>Pulsa <strong>+ Agregar Empleado</strong>.</li>
                    <li>Completa los datos y las credenciales de acceso.</li>
                    <li>Guarda. Se crea la cuenta con rol "empleado".</li>
                </ol>
            </div>
        </details>
    <?php endif; ?>

        <details class="ayuda-item" id="modulos">
            <summary><i class="fas fa-th-large"></i> modulos</summary>
            <div class="ayuda-contenido">
                <p>En este apartado se puede evidenciar todos los modulos con los cuales cuenta el aplicativo</p>
            </div>
        
    <details class="ayuda-subitem" id="mesas">
        <summary><i class="fas fa-utensils"></i> mesas</summary>
        <div class="ayuda-contenido">
            <p>Este módulo permite consultar y organizar la información de las mesas del establecimiento, de acuerdo con las opciones disponibles en el sistema.
            </p>
        </div>
    </details>



        <details class="ayuda-subitem" id="inventario">
            <summary><i class="fas fa-boxes"></i> Inventario</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>

        <details class="ayuda-subitem" id="ventas">
            <summary><i class="fas fa-cash-register"></i> Ventas y mesas</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>

        <details class="ayuda-subitem" id="reservas">
            <summary><i class="fas fa-calendar-alt"></i> Reservas</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>

        <details class="ayuda-subitem" id="puntos">
            <summary><i class="fas fa-coins"></i> Sistema de puntos</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>

        <details class="ayuda-subitem" id="horarios">
            <summary><i class="fas fa-clock"></i> Horarios</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>

        <?php if ($rolUsuario === 'dueno'): ?>
            <details class="ayuda-item" id="reportes">
                <summary><i class="fas fa-chart-bar"></i> Reportes</summary>
                <div class="ayuda-contenido">
                    <p>...</p>
                </div>
            </details>
        <?php endif; ?>

        <details class="ayuda-subitem" id="perfil">
            <summary><i class="fas fa-user-circle"></i> Mi perfil</summary>
            <div class="ayuda-contenido">
                <p>...</p>
            </div>
        </details>
</div>
</details>

<script>
    // Abre automáticamente la sección indicada en la URL (/ayuda#inventario)
    (function() {
        const id = location.hash.slice(1);
        const el = id && document.getElementById(id);
        if (el && el.tagName === 'DETAILS') {
            el.open = true;
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    })();
</script>