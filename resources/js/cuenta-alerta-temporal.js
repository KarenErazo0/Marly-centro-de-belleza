document.addEventListener('DOMContentLoaded', () => {
    const alerta = document.querySelector('[data-alerta-temporal]');

    if (!alerta) return;

    setTimeout(() => {
        alerta.classList.add('marly-alerta-ocultando');

        setTimeout(() => {
            alerta.remove();
        }, 450);
    }, 4500);
});