import './bootstrap';
import './cuenta-alerta-temporal';
import './login-password-toggle';

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('marly-theme');
    const initialTheme = savedTheme || 'light';

    const applyTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);

        if (themeToggle) {
            themeToggle.setAttribute(
                'aria-label',
                theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'
            );

            themeToggle.classList.toggle('theme-dark-active', theme === 'dark');
        }
    };

    applyTheme(initialTheme);

    themeToggle?.addEventListener('click', () => {
        const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('marly-theme', nextTheme);
        applyTheme(nextTheme);
    });

    const carousel = document.getElementById('carrusel-sobre-ventana');

    if (carousel) {
        const slides = Array.from(carousel.querySelectorAll('.slide-sobre'));
        const prev = document.querySelector('[data-carousel-prev]');
        const next = document.querySelector('[data-carousel-next]');
        let index = 0;

        const goToSlide = (newIndex) => {
            if (!slides.length) return;
            index = (newIndex + slides.length) % slides.length;
            carousel.style.transform = `translateX(-${index * 100}%)`;
        };

        prev?.addEventListener('click', () => goToSlide(index - 1));
        next?.addEventListener('click', () => goToSlide(index + 1));
        setInterval(() => goToSlide(index + 1), 4200);
    }

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

    const btnEditarCuenta = document.getElementById('btn-editar-cuenta');
    const panelEdicionCuenta = document.getElementById('panel-edicion-cuenta');

    if (btnEditarCuenta && panelEdicionCuenta) {
        const tituloBotonEditar = btnEditarCuenta.querySelector('strong');
        const descripcionBotonEditar = btnEditarCuenta.querySelector('small');

        btnEditarCuenta.addEventListener('click', () => {
            const estaVisible = panelEdicionCuenta.classList.toggle('visible');

            btnEditarCuenta.classList.toggle('marly-cuenta-opcion-activa', estaVisible);

            if (tituloBotonEditar) {
                tituloBotonEditar.textContent = estaVisible
                    ? 'Ocultar datos personales'
                    : 'Editar datos personales';
            }

            if (descripcionBotonEditar) {
                descripcionBotonEditar.textContent = estaVisible
                    ? 'Cerrar formulario de edición'
                    : 'Actualiza tu información personal y de contacto';
            }
        });
    }

    const fechaSelector = document.getElementById('fecha_selector');
    const horaSelector = document.getElementById('hora_selector');
    const resumenHoraHorario = document.getElementById('resumen-hora-horario');

    if (fechaSelector) {
        fechaSelector.addEventListener('change', () => {
            if (!fechaSelector.value) return;

            const fecha = new Date(`${fechaSelector.value}T00:00:00`);

            if (fecha.getDay() === 0) {
                alert('Los domingos no hay atención en el salón. Por favor selecciona otra fecha.');
                fechaSelector.value = '';
                return;
            }

            const base = fechaSelector.dataset.urlBase;

            if (base) {
                window.location.href = `${base}?fecha=${fechaSelector.value}`;
            }
        });
    }

    if (horaSelector && resumenHoraHorario) {
        horaSelector.addEventListener('change', () => {
            const opcion = horaSelector.options[horaSelector.selectedIndex];
            resumenHoraHorario.textContent = opcion && opcion.value ? opcion.textContent.trim() : 'Pendiente';
        });
    }

    const formConfirmarReserva = document.getElementById('form-confirmar-reserva');
    const modalConfirmacion = document.getElementById('modal-confirmacion-cita');
    const btnConfirmarModal = document.getElementById('btn-modal-confirmar-cita');
    const cancelarModal = document.querySelectorAll('[data-modal-cancelar]');
    let envioConfirmado = false;

    if (formConfirmarReserva && modalConfirmacion && btnConfirmarModal) {
        formConfirmarReserva.addEventListener('submit', (event) => {
            if (envioConfirmado) return;

            event.preventDefault();
            modalConfirmacion.classList.add('visible');
            modalConfirmacion.setAttribute('aria-hidden', 'false');
        });

        cancelarModal.forEach((elemento) => {
            elemento.addEventListener('click', () => {
                modalConfirmacion.classList.remove('visible');
                modalConfirmacion.setAttribute('aria-hidden', 'true');
            });
        });

        btnConfirmarModal.addEventListener('click', () => {
            envioConfirmado = true;
            modalConfirmacion.classList.remove('visible');
            formConfirmarReserva.submit();
        });
    }

    const btnCalendarioAdmin = document.getElementById('btn-ver-calendario-admin');
    const panelCalendarioAdmin = document.getElementById('calendario-admin-panel');
    const listadoAdminPanel = document.getElementById('listado-admin-panel');

    if (btnCalendarioAdmin && panelCalendarioAdmin) {
        const actualizarVistaAdmin = () => {
            const activo = panelCalendarioAdmin.classList.contains('visible');

            btnCalendarioAdmin.textContent = activo ? 'Ver listado de citas' : 'Ver citas en calendario';
            listadoAdminPanel?.classList.toggle('oculto', activo);
        };

        if (window.location.hash === '#calendario-admin-panel') {
            panelCalendarioAdmin.classList.add('visible');
        }

        actualizarVistaAdmin();

        btnCalendarioAdmin.addEventListener('click', () => {
            panelCalendarioAdmin.classList.toggle('visible');
            actualizarVistaAdmin();
        });
    }

    const fechaEditarCita = document.querySelector('.tarjeta-editar-cita input[type="date"][data-url-base]');

    if (fechaEditarCita) {
        fechaEditarCita.addEventListener('change', () => {
            if (!fechaEditarCita.value) return;

            const fecha = new Date(`${fechaEditarCita.value}T00:00:00`);

            if (fecha.getDay() === 0) {
                alert('Los domingos no hay atención en el salón. Por favor selecciona otra fecha.');
                fechaEditarCita.value = '';
                return;
            }

            window.location.href = `${fechaEditarCita.dataset.urlBase}?fecha=${fechaEditarCita.value}`;
        });
    }

    document.querySelectorAll('.boton-menu-cita:not(.marly-info-cita)').forEach((boton) => {
        boton.addEventListener('click', (event) => {
            event.stopPropagation();

            const menu = boton.closest('.menu-cita-cliente');

            document.querySelectorAll('.menu-cita-cliente.abierto').forEach((abierto) => {
                if (abierto !== menu) abierto.classList.remove('abierto');
            });

            menu?.classList.toggle('abierto');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.menu-cita-cliente.abierto').forEach((menu) => {
            menu.classList.remove('abierto');
        });
    });

    const crearModalConfirmacionGlobal = () => {
        let modal = document.getElementById('modal-confirmacion-global');

        if (modal) return modal;

        modal = document.createElement('div');
        modal.className = 'modal-confirmacion-cita';
        modal.id = 'modal-confirmacion-global';
        modal.setAttribute('aria-hidden', 'true');

        modal.innerHTML = `
            <div class="modal-confirmacion-backdrop" data-global-modal-cancelar></div>
            <div class="modal-confirmacion-card" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-global">
                <div class="modal-icono-alerta">!</div>
                <h2 id="titulo-modal-global">Confirmar acción</h2>
                <p id="mensaje-modal-global">¿Deseas continuar?</p>
                <small id="detalle-modal-global">Esta acción actualizará la información del sistema.</small>
                <div class="acciones-modal-confirmacion">
                    <button type="button" class="boton boton-secundario" data-global-modal-cancelar>Volver</button>
                    <button type="button" class="boton boton-primario" id="btn-global-confirmar">Confirmar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        return modal;
    };

    const modalGlobal = crearModalConfirmacionGlobal();
    const tituloModalGlobal = modalGlobal.querySelector('#titulo-modal-global');
    const mensajeModalGlobal = modalGlobal.querySelector('#mensaje-modal-global');
    const detalleModalGlobal = modalGlobal.querySelector('#detalle-modal-global');
    const btnGlobalConfirmar = modalGlobal.querySelector('#btn-global-confirmar');

    let formularioPendiente = null;
    let botonPendiente = null;

    const cerrarModalGlobal = () => {
        modalGlobal.classList.remove('visible');
        modalGlobal.setAttribute('aria-hidden', 'true');

        formularioPendiente = null;
        botonPendiente = null;

        if (btnGlobalConfirmar) {
            btnGlobalConfirmar.textContent = 'Confirmar';
            btnGlobalConfirmar.removeAttribute('disabled');
        }
    };

    modalGlobal.querySelectorAll('[data-global-modal-cancelar]').forEach((elemento) => {
        elemento.addEventListener('click', cerrarModalGlobal);
    });

    document.querySelectorAll('button[data-confirm-title], button[data-confirm-message]').forEach((boton) => {
        const formulario = boton.closest('form');

        if (!formulario || formulario.id === 'form-confirmar-reserva') return;

        formulario.addEventListener('submit', (event) => {
            if (formulario.dataset.confirmado === 'true') return;

            event.preventDefault();

            formularioPendiente = formulario;
            botonPendiente = boton;

            if (tituloModalGlobal) {
                tituloModalGlobal.textContent = boton.dataset.confirmTitle || 'Confirmar acción';
            }

            if (mensajeModalGlobal) {
                mensajeModalGlobal.textContent = boton.dataset.confirmMessage || '¿Deseas continuar?';
            }

            if (detalleModalGlobal) {
                detalleModalGlobal.textContent = boton.dataset.confirmDetail || 'Esta acción actualizará la información del sistema.';
            }

            if (btnGlobalConfirmar) {
                btnGlobalConfirmar.textContent = boton.dataset.confirmAction || 'Confirmar';
                btnGlobalConfirmar.removeAttribute('disabled');
            }

            modalGlobal.classList.add('visible');
            modalGlobal.setAttribute('aria-hidden', 'false');
        });
    });

    btnGlobalConfirmar?.addEventListener('click', () => {
        if (!formularioPendiente) {
            cerrarModalGlobal();
            return;
        }

        formularioPendiente.dataset.confirmado = 'true';
        botonPendiente?.setAttribute('disabled', 'disabled');

        modalGlobal.classList.remove('visible');
        modalGlobal.setAttribute('aria-hidden', 'true');

        formularioPendiente.submit();
    });

    const mostrarNotificacionMarly = (titulo, mensaje, tipo = 'info') => {
        const contenedorPagina =
            document.querySelector('.contenedor-mis-citas') ||
            document.querySelector('.contenedor-cuenta') ||
            document.querySelector('.contenedor') ||
            document.querySelector('main') ||
            document.body;

        const notificacionAnterior = contenedorPagina.querySelector('.marly-notificacion-temporal');

        if (notificacionAnterior) {
            notificacionAnterior.remove();
        }

        const notificacion = document.createElement('div');
        notificacion.className = `marly-notificacion-temporal marly-notificacion-${tipo}`;

        notificacion.innerHTML = `
            <div class="marly-notificacion-icono">
                ${tipo === 'success' ? '✓' : '!'}
            </div>

            <div class="marly-notificacion-contenido">
                <strong>${titulo}</strong>
                <p>${mensaje}</p>
            </div>

            <div class="marly-notificacion-decoracion">✦</div>
        `;

        contenedorPagina.prepend(notificacion);

        setTimeout(() => {
            notificacion.classList.add('saliendo');

            setTimeout(() => {
                notificacion.remove();
            }, 450);
        }, 5500);
    };

    document.querySelectorAll('.marly-info-cita').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const titulo = button.getAttribute('data-info-title') || 'Cita no disponible';
            const mensaje = button.getAttribute('data-info-message') || 'Esta cita no se puede modificar ni eliminar.';

            mostrarNotificacionMarly(titulo, mensaje, 'info');
        });
    });

    const mensajesSistema = document.querySelectorAll(
        '.alerta, .alerta-exito, .alerta-error, .admin-alert'
    );

    mensajesSistema.forEach((mensaje) => {
        const texto = mensaje.innerText.trim().length;

        let duracion = 5500;

        const esError =
            mensaje.classList.contains('alerta-error') ||
            mensaje.classList.contains('error') ||
            (mensaje.classList.contains('admin-alert') && mensaje.classList.contains('error'));

        if (esError) {
            duracion = 8000;
        }

        if (texto > 120) {
            duracion += 2000;
        }

        setTimeout(() => {
            mensaje.style.transition = 'opacity 0.45s ease, transform 0.45s ease';
            mensaje.style.opacity = '0';
            mensaje.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                mensaje.remove();
            }, 500);
        }, duracion);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const adminMenu = document.querySelector('[data-admin-menu]');
    const adminMenuButton = document.querySelector('[data-admin-menu-button]');

    adminMenuButton?.addEventListener('click', (event) => {
        event.stopPropagation();
        adminMenu?.classList.toggle('abierto');
    });

    document.addEventListener('click', (event) => {
        if (adminMenu && !adminMenu.contains(event.target)) {
            adminMenu.classList.remove('abierto');
        }
    });

    const abrirModal = (id) => {
        const modal = document.getElementById(id);

        if (!modal) return;

        modal.classList.add('visible');
        modal.setAttribute('aria-hidden', 'false');

        const firstInput = modal.querySelector('input, select, textarea, button[type="submit"]');

        setTimeout(() => firstInput?.focus(), 80);
    };

    const cerrarModal = (modal) => {
        modal?.classList.remove('visible');
        modal?.setAttribute('aria-hidden', 'true');
    };

    document.querySelectorAll('[data-open-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const serviceId = button.getAttribute('data-service-id');

            if (serviceId) {
                const selector = document.getElementById('select-crear-trabajador-servicio');

                if (selector) selector.value = serviceId;
            }

            abrirModal(button.getAttribute('data-open-modal'));
        });
    });

    document.querySelectorAll('[data-close-modal], .admin-modal-backdrop').forEach((button) => {
        button.addEventListener('click', () => cerrarModal(button.closest('.admin-modal')));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.admin-modal.visible').forEach(cerrarModal);
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const userMenu = document.querySelector('[data-user-menu]');
    const userMenuButton = document.querySelector('[data-user-menu-button]');

    userMenuButton?.addEventListener('click', (event) => {
        event.stopPropagation();
        userMenu?.classList.toggle('abierto');
    });

    document.addEventListener('click', (event) => {
        if (userMenu && !userMenu.contains(event.target)) {
            userMenu.classList.remove('abierto');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.marly-toggle-password').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-password-target');
            const input = document.getElementById(targetId);

            if (!input) return;

            input.type = input.type === 'password' ? 'text' : 'password';

            button.classList.toggle('activo', input.type === 'text');
        });
    });
});