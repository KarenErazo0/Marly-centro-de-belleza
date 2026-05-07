document.addEventListener('DOMContentLoaded', () => {
    const botonOjito = document.querySelector('.marly-login-eye');
    const inputPassword = document.querySelector('#contrasena');

    if (!botonOjito || !inputPassword) return;

    botonOjito.addEventListener('click', () => {
        const esPassword = inputPassword.type === 'password';

        inputPassword.type = esPassword ? 'text' : 'password';

        botonOjito.setAttribute(
            'aria-label',
            esPassword ? 'Ocultar contraseña' : 'Mostrar contraseña'
        );
    });
});