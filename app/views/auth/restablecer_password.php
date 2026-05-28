<section class="form-section">
    <div class="login-card">
        <div class="form-content">
            <div class="reset-card">
                <div class="icon-wrap">🔒</div>

                <h1>Nueva contraseña</h1>
                <p class="recuperar-subtitle">Elige una contraseña segura para tu cuenta.</p>

                <form method="POST" action="<?= BASE_URL ?>/restablecer" novalidate id="formReset">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($tokenCSRF) ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($tokenReset) ?>">

                    <div class="requirements">
                        <p>La contraseña debe tener:</p>
                        <div class="req-item" id="req-len">Mínimo 8 caracteres</div>
                        <div class="req-item" id="req-may">Al menos una mayúscula</div>
                        <div class="req-item" id="req-num">Al menos un número</div>
                        <div class="req-item" id="req-esp">Al menos un carácter especial</div>
                    </div>

                    <div class="field">
                        <label for="contrasena">Nueva contraseña</label>
                        <div class="input-wrap">
                            <input type="password" id="contrasena" name="contrasena"
                                   placeholder="••••••••" autocomplete="new-password" required>
                            <button type="button" class="toggle-pass" onclick="toggleVer('contrasena', this)">👁</button>
                        </div>
                        <div class="strength-bar">
                            <span id="s1"></span><span id="s2"></span>
                            <span id="s3"></span><span id="s4"></span>
                        </div>
                        <div class="strength-label" id="strength-text"></div>
                    </div>

                    <div class="field">
                        <label for="contrasena_confirm">Confirmar contraseña</label>
                        <div class="input-wrap">
                            <input type="password" id="contrasena_confirm" name="contrasena_confirm"
                                   placeholder="••••••••" autocomplete="new-password" required>
                            <button type="button" class="toggle-pass" onclick="toggleVer('contrasena_confirm', this)">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-form" id="btnGuardar">Guardar contraseña</button>
                </form>

                <a href="<?= BASE_URL ?>/login" class="back-link"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver al <span>inicio de sesión</span></a>
            </div>
        </div>
    </div>
</section>

<script>
  function toggleVer(id, btn) {
    const input = document.getElementById(id);
    const mostrar = input.type === 'password';
    input.type = mostrar ? 'text' : 'password';
    btn.textContent = mostrar ? '🙈' : '👁';
  }

  const pass  = document.getElementById('contrasena');
  const bars  = [document.getElementById('s1'), document.getElementById('s2'),
                 document.getElementById('s3'), document.getElementById('s4')];
  const label = document.getElementById('strength-text');

  const reqs = {
    len: { el: document.getElementById('req-len'), fn: v => v.length >= 8 },
    may: { el: document.getElementById('req-may'), fn: v => /[A-Z]/.test(v) },
    num: { el: document.getElementById('req-num'), fn: v => /[0-9]/.test(v) },
    esp: { el: document.getElementById('req-esp'), fn: v => /[\W_]/.test(v) },
  };

  const colors  = ['', '#e94560', '#f5a623', '#27ae60', '#2ecc71'];
  const labels_ = ['', 'Muy débil', 'Débil', 'Buena', 'Fuerte'];

  pass.addEventListener('input', () => {
    const v = pass.value;
    let score = 0;

    for (const key in reqs) {
      const ok = reqs[key].fn(v);
      reqs[key].el.classList.toggle('ok', ok);
      if (ok) score++;
    }

    bars.forEach((b, i) => {
      b.style.background = i < score ? colors[score] : 'rgba(255,255,255,.1)';
    });
    label.textContent = v.length ? labels_[score] : '';
    label.style.color  = colors[score] || '#aaa';
  });
</script>
