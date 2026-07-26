'use strict';

(function () {
    const patterns = {
        nombre: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/,
        apellido: /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,40}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/i,
        telefono: /^[+]?([0-9\s()-]{7,20})$/,
        usuario: /^(?=.{4,60}$)[a-zA-Z0-9._-]+$/,
        password: /^(?=.*[A-Za-z])(?=.*\d).{8,128}$/,
        loginUsuario: /^(?=.{4,60}$)[a-zA-Z0-9._-]+$/
    };

    function setError(input, message) {
        const container = input.closest('.campo-grupo');
        input.classList.add('input-error');

        let errorElement = container?.querySelector('.field-error');
        if (!errorElement) {
            errorElement = document.createElement('small');
            errorElement.className = 'field-error';
            container?.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    function clearError(input) {
        const container = input.closest('.campo-grupo');
        input.classList.remove('input-error');

        const errorElement = container?.querySelector('.field-error');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    function validateRequired(input, message) {
        if (!input.value.trim()) {
            setError(input, message);
            return false;
        }
        clearError(input);
        return true;
    }

    function validatePattern(input, pattern, message) {
        if (!pattern.test(input.value.trim())) {
            setError(input, message);
            return false;
        }
        clearError(input);
        return true;
    }

    function initLoginForm() {
        const form = document.getElementById('formLogin');
        if (!form) return;

        const usuario = document.getElementById('loginUsuario');
        const password = document.getElementById('loginPass');

        [usuario, password].forEach((input) => {
            if (!input) return;
            input.addEventListener('input', () => clearError(input));
        });

        form.addEventListener('submit', (event) => {
            let isValid = true;

            if (!validateRequired(usuario, 'El usuario es obligatorio.')) {
                isValid = false;
            } else if (!validatePattern(usuario, patterns.loginUsuario, 'El usuario solo permite letras, números, puntos, guiones y guiones bajos.')) {
                isValid = false;
            }

            if (!validateRequired(password, 'La contraseña es obligatoria.')) {
                isValid = false;
            } else if (!validatePattern(password, patterns.password, 'La contraseña debe tener mínimo 8 caracteres y contener letras y números.')) {
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
                usuario?.focus();
            }
        });
    }

    function initRegisterForm() {
        const form = document.getElementById('formRegistro');
        if (!form) return;

        const nombre = document.getElementById('rNombre');
        const apellido = document.getElementById('rApellido');
        const email = document.getElementById('rEmail');
        const telefono = document.getElementById('rTelefono');
        const usuario = document.getElementById('rUsuario');
        const password = document.getElementById('rPass');
        const passwordConfirm = document.getElementById('rPassConf');

        [nombre, apellido, email, telefono, usuario, password, passwordConfirm].forEach((input) => {
            if (!input) return;
            input.addEventListener('input', () => clearError(input));
        });

        form.addEventListener('submit', (event) => {
            let isValid = true;

            if (!validateRequired(nombre, 'El nombre es obligatorio.')) {
                isValid = false;
            } else if (!validatePattern(nombre, patterns.nombre, 'El nombre solo puede contener letras y espacios.')) {
                isValid = false;
            }

            if (!validateRequired(apellido, 'El apellido es obligatorio.')) {
                isValid = false;
            } else if (!validatePattern(apellido, patterns.apellido, 'El apellido solo puede contener letras y espacios.')) {
                isValid = false;
            }

            if (!validateRequired(email, 'El correo electrónico es obligatorio.')) {
                isValid = false;
            } else if (!validatePattern(email, patterns.email, 'Ingresa un correo electrónico válido.')) {
                isValid = false;
            }

            if (telefono && telefono.value.trim()) {
                if (!validatePattern(telefono, patterns.telefono, 'El teléfono solo puede contener números, espacios, guiones y paréntesis.')) {
                    isValid = false;
                }
            }

            if (!validateRequired(usuario, 'El nombre de usuario es obligatorio.')) {
                isValid = false;
            } else if (!validatePattern(usuario, patterns.usuario, 'El usuario solo permite letras, números, puntos, guiones y guiones bajos.')) {
                isValid = false;
            }

            if (!validateRequired(password, 'La contraseña es obligatoria.')) {
                isValid = false;
            } else if (!validatePattern(password, patterns.password, 'La contraseña debe tener mínimo 8 caracteres y contener letras y números.')) {
                isValid = false;
            }

            if (!validateRequired(passwordConfirm, 'Debes confirmar la contraseña.')) {
                isValid = false;
            } else if (passwordConfirm.value.trim() !== password.value.trim()) {
                setError(passwordConfirm, 'Las contraseñas no coinciden.');
                isValid = false;
            } else {
                clearError(passwordConfirm);
            }

            if (!isValid) {
                event.preventDefault();
                nombre?.focus();
            }
        });
    }

    initLoginForm();
    initRegisterForm();
})();
