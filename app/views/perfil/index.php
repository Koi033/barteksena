<?php /* app/views/perfil/index.php — Formulario de perfil del usuario autenticado */ ?>

<?php 
  // Leemos la foto con prioridad desde la sesión, o de la base de datos
  $nombreFoto = $_SESSION['usuario_foto'] ?? ($usuario['foto'] ?? '');
  $tieneFoto  = !empty($nombreFoto);
  $fotoPerfil = $tieneFoto ? rtrim(BASE_URL, '/') . '/public/uploads/' . $nombreFoto : '';
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

  :root {
    --bg-dark: #121212;
    --card-bg: #1e1e1e;
    --card-inner: #282828;
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
  .profile-wrapper {
    max-width: 950px;
    margin: 2rem auto;
    padding: 2.5rem;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    position: relative;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
    overflow: hidden;
  }

  /* Línea Naranja Neón Superior */
  .profile-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--orange-main), #ffb700);
    box-shadow: 0 0 15px var(--orange-glow);
  }

  /* Header */
  .profile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 2rem;
    margin-bottom: 2rem;
    border-bottom: 1px solid var(--border-color);
    flex-wrap: wrap;
    gap: 1.5rem;
  }

  .header-info-group {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  /* AVATAR CIRCULAR CON BORDE NEÓN */
  .avatar-upload-label {
    position: relative;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2a2a2a 0%, #151515 100%);
    border: 3px solid var(--orange-main);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 25px var(--orange-glow);
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .avatar-upload-label:hover {
    transform: scale(1.05);
    border-color: #ffb700;
    box-shadow: 0 0 35px rgba(255, 123, 0, 0.7);
  }

  .avatar-preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
  }

  /* SVG Avatar elegante por defecto */
  .avatar-svg-default {
    width: 58px;
    height: 58px;
    filter: drop-shadow(0 0 6px var(--orange-glow));
    transition: transform 0.3s ease;
  }

  /* Franja inferior flotante para la cámara */
  .avatar-camera-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 30%;
    background: rgba(0, 0, 0, 0.75);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    backdrop-filter: blur(2px);
    transition: background 0.3s ease;
  }

  .avatar-upload-label:hover .avatar-camera-overlay {
    background: rgba(255, 123, 0, 0.9);
  }

  .title-area h1 {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-white);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .title-area p {
    color: var(--text-muted);
    font-size: 0.88rem;
    margin-top: 0.2rem;
  }

  .orange-badge {
    background: rgba(255, 123, 0, 0.1);
    border: 1px solid var(--orange-main);
    color: var(--orange-main);
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .header-badges {
    display: flex;
    gap: 0.8rem;
  }

  /* Secciones y Campos */
  .section-title-orange {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--orange-main);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }

  .custom-field {
    display: flex;
    flex-direction: column;
  }

  .custom-field label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 0.5rem;
  }

  .custom-field input {
    background: var(--card-inner);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 0.85rem 1rem;
    color: var(--text-white);
    font-size: 0.95rem;
    font-weight: 600;
    outline: none;
    transition: all 0.25s ease;
  }

  .custom-field input:focus {
    border-color: var(--orange-main);
    box-shadow: 0 0 12px var(--orange-glow);
  }

  .custom-field input[readonly] {
    opacity: 0.4;
    cursor: not-allowed;
    border-style: dashed;
  }

  .actions-wrapper {
    display: flex;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
  }

  .btn-orange {
    background: var(--orange-main);
    color: #ffffff;
    font-family: inherit;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.9rem 2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    box-shadow: 0 4px 15px var(--orange-glow);
    transition: all 0.25s ease;
  }

  .btn-orange:hover {
    background: var(--orange-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 123, 0, 0.5);
  }

  .btn-dark {
    background: var(--card-inner);
    color: var(--text-white);
    font-family: inherit;
    font-weight: 600;
    font-size: 0.95rem;
    padding: 0.9rem 1.8rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    transition: all 0.25s ease;
  }

  .btn-dark:hover {
    background: #333333;
    border-color: #444444;
    transform: translateY(-2px);
  }
</style>

<div class="profile-wrapper">

    <form method="POST" action="<?= BASE_URL ?>/perfil/actualizar" enctype="multipart/form-data" novalidate>

        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($tokenCSRF, ENT_QUOTES, 'UTF-8') ?>">

        <!-- CABECERA CON AVATAR CIRCULAR -->
        <div class="profile-header">
            <div class="header-info-group">
                
                <!-- CIRCULO INTERACTIVO CON ILUSTRACIÓN VECTORIAL / FOTO -->
                <label for="inputFotoPerfil" class="avatar-upload-label" title="Haz clic para subir tu foto">
                    
                    <img id="imgPreview" 
                         src="<?= $fotoPerfil ?>" 
                         class="avatar-preview-img" 
                         style="<?= $tieneFoto ? 'display: block;' : 'display: none;' ?>" 
                         alt="Foto de Perfil">

                    <svg id="defaultAvatarIcon" 
                         class="avatar-svg-default" 
                         style="<?= $tieneFoto ? 'display: none;' : 'display: block;' ?>" 
                         viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="38" r="22" stroke="#FF7B00" stroke-width="4.5" fill="rgba(255,123,0,0.1)"/>
                        <path d="M15 88C15 68 31 56 50 56C69 56 85 68 85 88" stroke="#FF7B00" stroke-width="4.5" stroke-linecap="round" fill="none"/>
                        <circle cx="50" cy="38" r="8" fill="#FF7B00"/>
                    </svg>

                    <div class="avatar-camera-overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </label>

                <!-- INPUT DE ARCHIVO OCULTO -->
                <input type="file" id="inputFotoPerfil" name="foto_perfil" accept="image/*" style="display: none;">

                <div class="title-area">
                    <h1>Mi Perfil</h1>
                    <p>Toca el círculo para subir tu foto de perfil.</p>
                </div>
            </div>

            <div class="header-badges">
                <span class="orange-badge"><i class="fas fa-user-shield"></i> <?= htmlspecialchars($usuario['rol'] ?? 'Rol', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="orange-badge"><i class="fas fa-at"></i> <?= htmlspecialchars($usuario['usuario'] ?? 'usuario', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <!-- SECCIÓN 1: DATOS GENERALES -->
        <div class="section-title-orange">
            <i class="fas fa-id-card"></i> Datos Generales
        </div>

        <div class="fields-grid">
            <div class="custom-field">
                <label for="pNombre">Nombre Completo *</label>
                <input type="text" id="pNombre" name="nombre" maxlength="100" required
                       value="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="custom-field">
                <label for="pEmail">Correo Electrónico *</label>
                <input type="email" id="pEmail" name="email" maxlength="150" required
                       value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="custom-field">
                <label for="pTel">Teléfono</label>
                <input type="tel" id="pTel" name="telefono" maxlength="20"
                       value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="custom-field">
                <label>Usuario</label>
                <input type="text" value="<?= htmlspecialchars($usuario['usuario'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       readonly>
            </div>

            <div class="custom-field">
                <label>Rol</label>
                <input type="text" value="<?= htmlspecialchars($usuario['rol'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       readonly style="text-transform:capitalize;">
            </div>
        </div>

        <!-- SECCIÓN 2: CAMBIAR CONTRASEÑA -->
        <div class="section-title-orange" style="margin-top: 1rem;">
            <i class="fas fa-key"></i> Cambiar Contraseña <span style="color:var(--text-muted); font-size:0.75rem; font-weight:normal; text-transform:none;">(opcional)</span>
        </div>

        <div class="fields-grid">
            <div class="custom-field">
                <label for="pPassAct">Contraseña Actual</label>
                <input type="password" id="pPassAct" name="pass_actual"
                       autocomplete="current-password" maxlength="128"
                       placeholder="Requerida para cambiar la contraseña">
            </div>

            <div class="custom-field">
                <label for="pPassNew">Nueva Contraseña</label>
                <input type="password" id="pPassNew" name="pass_nueva"
                       autocomplete="new-password" minlength="8" maxlength="128"
                       placeholder="Mínimo 8 caracteres">
            </div>

            <div class="custom-field">
                <label for="pPassConf">Confirmar Nueva Contraseña</label>
                <input type="password" id="pPassConf" name="pass_confirmar"
                       autocomplete="new-password" minlength="8" maxlength="128"
                       placeholder="Repite la nueva contraseña">
            </div>
        </div>

        <!-- BOTONES -->
        <div class="actions-wrapper">
            <button type="submit" class="btn-orange">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <a href="<?= BASE_URL ?>/dashboard" class="btn-dark">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </form>
</div>

<!-- VISTA PREVIA INSTANTÁNEA AL SELECCIONAR FOTO -->
<script>
  document.getElementById('inputFotoPerfil').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(evt) {
        const img = document.getElementById('imgPreview');
        const defaultIcon = document.getElementById('defaultAvatarIcon');
        
        img.src = evt.target.result;
        img.style.display = 'block';
        if (defaultIcon) defaultIcon.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  });
</script>