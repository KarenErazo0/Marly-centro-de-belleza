import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const formServicios = document.getElementById('form-seleccion-servicios');

    if (formServicios) {
        const checkboxes = Array.from(formServicios.querySelectorAll('.input-servicio-home'));
        const contador = document.getElementById('contador-servicios-home');

        const actualizarServicios = () => {
            let total = 0;

            checkboxes.forEach((checkbox) => {
                const card = checkbox.closest('.tarjeta-servicio-home');
                const texto = card?.querySelector('.texto-boton-servicio');

                if (checkbox.checked) {
                    total += 1;
                    card?.classList.add('seleccionado');
                    if (texto) texto.textContent = 'Seleccionado ✓';
                } else {
                    card?.classList.remove('seleccionado');
                    if (texto) texto.textContent = 'Seleccionar servicio';
                }
            });

            if (contador) {
                contador.textContent = `${total} servicio${total === 1 ? '' : 's'} seleccionado${total === 1 ? '' : 's'}`;
            }
        };

        checkboxes.forEach((checkbox) => checkbox.addEventListener('change', actualizarServicios));
        actualizarServicios();
    }

    const formProfesionales = document.getElementById('form-seleccion-profesionales');
    if (formProfesionales) {
        const radios = Array.from(formProfesionales.querySelectorAll('.tarjeta-trabajador-opcion input[type="radio"]'));

        const actualizarProfesionales = () => {
            const grupos = {};
            radios.forEach((radio) => {
                const label = radio.closest('.tarjeta-trabajador-opcion');
                const groupName = radio.getAttribute('name');
                if (!grupos[groupName]) grupos[groupName] = [];
                grupos[groupName].push({ radio, label });
            });

            Object.values(grupos).forEach((items) => {
                items.forEach(({ radio, label }) => {
                    const texto = label?.querySelector('.texto-profesional');
                    if (radio.checked) {
                        label?.classList.add('seleccionado');
                        if (texto) texto.textContent = 'Seleccionado ✓';
                    } else {
                        label?.classList.remove('seleccionado');
                        if (texto) texto.textContent = 'Seleccionar';
                    }
                });
            });
        };

        radios.forEach((radio) => radio.addEventListener('change', actualizarProfesionales));
        actualizarProfesionales();
    }

    const formHorario = document.getElementById('form-seleccion-horario');
    if (formHorario) {
        const radiosHora = Array.from(formHorario.querySelectorAll('.item-hora-opcion input[type="radio"]'));

        const actualizarHoras = () => {
            radiosHora.forEach((radio) => {
                const label = radio.closest('.item-hora-opcion');
                label?.classList.toggle('seleccionado', radio.checked);
            });
        };

        radiosHora.forEach((radio) => radio.addEventListener('change', actualizarHoras));
        actualizarHoras();
    }
});
