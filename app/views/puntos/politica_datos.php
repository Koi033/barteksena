<?php /* app/views/puntos/politica_datos.php - Política de Tratamiento de Datos Personales (Club de Fidelización) */ ?>
<section class="politica-section">
    <div class="politica-card">

        <div class="politica-header">
            <h1><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Política de Tratamiento de Datos Personales</h1>
            <p class="politica-subtitle">Club de Fidelización · Bartek</p>
        </div>

        <h2>1. Responsable del Tratamiento</h2>
        <p>
            <strong>Razón social:</strong> [PENDIENTE — Nombre legal del bar/empresa]<br>
            <strong>NIT:</strong> [PENDIENTE]<br>
            <strong>Domicilio:</strong> [PENDIENTE — Ciudad, dirección]<br>
            <strong>Correo de contacto:</strong> [PENDIENTE — ej. datos@bartek.com]<br>
            <strong>Teléfono:</strong> [PENDIENTE]
        </p>

        <h2>2. Finalidad del Tratamiento</h2>
        <p>Los datos personales recolectados a través del formulario de registro al Club de Fidelización serán utilizados para:</p>
        <ul>
            <li>Crear y administrar la cuenta del cliente dentro del programa de puntos.</li>
            <li>Registrar, acumular y canjear puntos generados por sus consumos en el establecimiento.</li>
            <li>Enviar comunicaciones relacionadas con promociones, beneficios y novedades del Club de Fidelización.</li>
            <li>Atender consultas, peticiones, quejas o reclamos relacionados con el programa.</li>
            <li>Dar cumplimiento a obligaciones legales y contractuales aplicables.</li>
        </ul>

        <h2>3. Datos Recolectados</h2>
        <p>
            Para el registro al Club de Fidelización se recolectan únicamente los siguientes datos:
            <strong>nombre completo</strong> y <strong>número de cédula</strong>. Estos datos se
            almacenan de forma segura y no se comparten con terceros ajenos a la operación del
            programa de fidelización, salvo requerimiento de autoridad competente.
        </p>

        <h2>4. Derechos del Titular de los Datos</h2>
        <p>Como titular de sus datos personales, usted tiene derecho a:</p>
        <ul>
            <li>Conocer, actualizar y rectificar sus datos personales.</li>
            <li>Solicitar prueba de la autorización otorgada para el tratamiento de sus datos.</li>
            <li>Ser informado sobre el uso que se le ha dado a sus datos personales.</li>
            <li>Presentar quejas ante la Superintendencia de Industria y Comercio (SIC) por infracciones a la ley de protección de datos.</li>
            <li>Revocar la autorización y/o solicitar la supresión de sus datos, cuando no exista un deber legal o contractual que impida su eliminación.</li>
            <li>Acceder de forma gratuita a sus datos personales que hayan sido objeto de tratamiento.</li>
        </ul>

        <h2>5. Procedimiento para Ejercer sus Derechos</h2>
        <p>
            Para ejercer cualquiera de los derechos mencionados, el titular puede enviar su solicitud
            al correo <strong>[PENDIENTE — correo de contacto]</strong>, indicando su nombre completo,
            número de cédula y una descripción clara de la solicitud. La respuesta se dará dentro de
            los términos establecidos por la ley (15 días hábiles para consultas y 10 días hábiles
            para reclamos, prorrogables según lo permita la normativa).
        </p>

        <h2>6. Vigencia</h2>
        <p>
            Los datos personales serán conservados mientras el titular haga parte activa del Club de
            Fidelización, o hasta que solicite su supresión conforme a lo indicado en esta política,
            salvo que exista una obligación legal de conservarlos por un periodo mayor.
        </p>

        <h2>7. Autorización</h2>
        <p>
            Al marcar la casilla de aceptación en el formulario de registro, usted declara que ha
            leído esta Política de Tratamiento de Datos Personales y que autoriza de manera libre,
            previa, expresa e informada a <strong>[PENDIENTE — Razón social]</strong> para tratar sus
            datos personales conforme a las finalidades aquí descritas.
        </p>

        <p class="politica-fecha">Última actualización: <?= date('d/m/Y') ?></p>

        <div class="politica-footer">
            <a href="<?= BASE_URL ?>/puntos/registro-publico" class="btn-form-secundario">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al registro
            </a>
        </div>

    </div>
</section>

<style>
    .politica-section {
        max-width: 780px;
        margin: 40px auto;
        padding: 0 20px;
    }
    .politica-card {
        background: #1a1c1e;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 36px;
        color: #ECEFF1;
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
        line-height: 1.6;
    }
    .politica-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 18px;
        margin-bottom: 20px;
    }
    .politica-header h1 {
        color: #E67E22;
        font-size: 1.4rem;
        margin: 0 0 6px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .politica-subtitle {
        color: #9aa0a6;
        margin: 0;
        font-size: 0.92rem;
    }
    .politica-aviso {
        background: rgba(230, 126, 34, 0.1);
        border: 1px solid rgba(230, 126, 34, 0.35);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.85rem;
        color: #ffcf9e;
        margin-bottom: 24px;
    }
    .politica-aviso code {
        background: rgba(0, 0, 0, 0.35);
        padding: 1px 6px;
        border-radius: 4px;
        color: #ffb066;
    }
    .politica-card h2 {
        color: #ECEFF1;
        font-size: 1.05rem;
        margin: 26px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .politica-card p,
    .politica-card li {
        color: #c9ccd1;
        font-size: 0.92rem;
    }
    .politica-card ul {
        margin: 0 0 12px;
        padding-left: 22px;
    }
    .politica-card li {
        margin-bottom: 6px;
    }
    .politica-fecha {
        color: #8a8d92;
        font-size: 0.8rem;
        margin-top: 30px;
        font-style: italic;
    }
    .politica-footer {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .btn-form-secundario {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.08);
        color: #ECEFF1;
        border: 1px solid rgba(255, 255, 255, 0.12);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: background 0.2s;
    }
    .btn-form-secundario:hover {
        background: rgba(255, 255, 255, 0.14);
        text-decoration: none;
    }

    @media (max-width: 600px) {
        .politica-card {
            padding: 24px;
        }
    }
</style>
