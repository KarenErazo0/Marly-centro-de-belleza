@php
    $tabActivo = $tab ?? 'personal';
    $tabVisual = $tabActivo;
    $adminEmail = session('admin_correo', 'admin@marly.com');
    $heroImagen = $configuracion->hero_imagen ?: 'default-service.jpg';
    $heroRuta = str_starts_with($heroImagen, 'http')
        ? $heroImagen
        : (str_starts_with($heroImagen, 'hero-admin-')
            ? asset('images/site/' . $heroImagen)
            : asset('images/services/' . $heroImagen));
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel admin | Marly Centro de Belleza</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/css/paginas/admin.css', 'resources/js/app.js'])
</head>

<body class="admin-body admin-layout-sidebar">
    <aside class="admin-sidebar">
        <a class="admin-sidebar-brand" href="{{ route('admin.dashboard', ['tab' => 'servicios']) }}">
            <img src="{{ asset('images/logo-marly.png') }}" alt="Logo Marly Centro de Belleza">
            <span>Marly Centro<br>de Belleza</span>
        </a>

        <nav class="admin-sidebar-nav" aria-label="Navegación administrativa">
            <a class="admin-sidebar-link {{ $tabVisual === 'servicios' ? 'active' : '' }}"
                href="{{ route('admin.dashboard', ['tab' => 'servicios']) }}">
                <svg viewBox="0 0 24 24">
                    <path d="M6 7V6a3 3 0 0 1 6 0v1m-7 0h14l-1 13H6L5 7Zm9 0V6a3 3 0 0 1 6 0v1" />
                </svg>
                <span>Gestión de servicios</span>
            </a>

            <a class="admin-sidebar-link {{ $tabVisual === 'personal' ? 'active' : '' }}"
                href="{{ route('admin.dashboard', ['tab' => 'personal']) }}">
                <svg viewBox="0 0 24 24">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 9a7 7 0 0 1 14 0" />
                </svg>
                <span>Gestión de personal</span>
            </a>

            <a class="admin-sidebar-link {{ $tabVisual === 'citas' ? 'active' : '' }}"
                href="{{ route('admin.dashboard', ['tab' => 'citas']) }}">
                <svg viewBox="0 0 24 24">
                    <path d="M7 3v4M17 3v4M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z" />
                </svg>
                <span>Gestión de citas</span>
            </a>

            <a class="admin-sidebar-link {{ $tabVisual === 'configuracion' ? 'active' : '' }}"
                href="{{ route('admin.dashboard', ['tab' => 'configuracion']) }}">
                <svg viewBox="0 0 24 24">
                    <path d="M4 5h16v14H4zM8 9h8M8 13h5M17 17h.01" />
                </svg>
                <span>Página principal</span>
            </a>
        </nav>

        <div class="admin-sidebar-bottom">
            <a class="admin-sidebar-link" href="{{ route('admin.dashboard', ['tab' => 'configuracion']) }}">
                <svg viewBox="0 0 24 24">
                    <path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" />
                    <path
                        d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21a2 2 0 0 1-4 0v-.09A1.7 1.7 0 0 0 8.6 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H3a2 2 0 0 1 0-4h.09A1.7 1.7 0 0 0 4.6 8.6a1.7 1.7 0 0 0-.34-1.88l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V3a2 2 0 0 1 4 0v.09A1.7 1.7 0 0 0 15.4 4.6a1.7 1.7 0 0 0 1.88-.34l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.4 9c.2.37.55.6 1 .6H21a2 2 0 0 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1.4Z" />
                </svg>
                <span>Configuración</span>
            </a>

            <a class="admin-sidebar-link" href="#">
                <svg viewBox="0 0 24 24">
                    <path d="M12 18h.01M9.1 9a3 3 0 1 1 5.8 1c-.45 1.14-1.6 1.62-2.25 2.35-.45.5-.65.95-.65 1.65" />
                    <circle cx="12" cy="12" r="10" />
                </svg>
                <span>Ayuda</span>
            </a>
        </div>
    </aside>

    <header class="admin-sidebar-topbar">
        <div></div>

        <div class="admin-sidebar-actions">
            <button type="button" class="theme-toggle admin-theme-toggle" id="theme-toggle"
                aria-label="Cambiar a modo oscuro" title="Cambiar tema">
                <span class="theme-toggle-track" aria-hidden="true">
                    <span class="theme-toggle-thumb">
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path
                                d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41">
                            </path>
                        </svg>
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3 6.7 6.7 0 0 0 21 12.8Z"></path>
                        </svg>
                    </span>
                </span>
            </button>

            <div class="admin-user admin-user-sidebar" data-admin-menu>
                <button type="button" class="admin-user-button" data-admin-menu-button
                    aria-label="Cuenta administradora">
                    <span class="admin-user-inicial">
                        {{ strtoupper(substr($adminEmail, 0, 1)) }}
                    </span>
                    <span class="admin-user-name">{{ $adminEmail }}</span>
                    <svg class="admin-user-chevron" viewBox="0 0 24 24">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>

                <div class="admin-user-dropdown">
                    <strong>{{ $adminEmail }}</strong>
                    <span>Panel admin</span>
                    <form action="{{ route('cliente.salir') }}" method="POST">
                        @csrf
                        <button type="submit" data-confirm-title="Cerrar sesión"
                            data-confirm-message="¿Quieres cerrar la sesión de administración?"
                            data-confirm-detail="Volverás a la página de inicio." data-confirm-action="Cerrar sesión">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-shell">
        @if (session('success') || session('error') || $errors->any())
            <section class="admin-alert-zone">
                @if (session('success'))
                    <div class="admin-alert success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="admin-alert error">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="admin-alert error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        @if ($tabVisual === 'personal')
            <section class="admin-section-head">
                <div>
                    <h1>Gestión de personal</h1>
                    <p>Administra a los profesionales por área de servicio.</p>
                </div>
                <div class="admin-head-actions">
                    <button type="button" class="admin-btn admin-btn-light"
                        data-open-modal="modal-crear-seccion"><span>+</span>Nueva sección</button>
                    <button type="button" class="admin-btn admin-btn-gold"
                        data-open-modal="modal-crear-trabajador"><span>♙</span>Agregar trabajador</button>
                </div>
            </section>

            <section class="admin-personal-stack">
                @forelse($servicios as $servicio)
                    <article class="admin-worker-section">
                        <div class="worker-section-header">
                            <div>
                                <h2>{{ $servicio->nombre_servicio }}</h2>
                                <p>{{ $servicio->trabajadores->count() }}
                                    trabajador{{ $servicio->trabajadores->count() === 1 ? '' : 'es' }}</p>
                            </div>
                            <div class="worker-section-actions">
                                <button type="button" class="admin-mini-btn"
                                    data-open-modal="modal-editar-seccion-{{ $servicio->id_servicio }}">✎ Editar
                                    sección</button>
                                <form action="{{ route('admin.personal.secciones.eliminar', $servicio) }}"
                                    method="POST" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-mini-btn danger"
                                        data-confirm-title="Eliminar sección"
                                        data-confirm-message="¿Eliminar la sección {{ $servicio->nombre_servicio }}?"
                                        data-confirm-detail="Se eliminará esta sección administrativa. Si tiene registros asociados, el sistema protegerá el historial."
                                        data-confirm-action="Eliminar sección"><span class="trash-icon"
                                            aria-hidden="true"><svg viewBox="0 0 24 24">
                                                <path d="M4 7h16M10 11v6M14 11v6M6 7l1 14h10l1-14M9 7V4h6v3" />
                                            </svg></span><span>Eliminar sección</span></button>
                                </form>
                                <button type="button" class="admin-collapse"
                                    aria-label="Contraer sección">⌃</button>
                            </div>
                        </div>
                        <div class="worker-grid">
                            @foreach ($servicio->trabajadores as $trabajador)
                                <article class="worker-card">
                                    @php
                                        $fotoTrabajador = $trabajador->foto ?: 'default-service.jpg';
                                    @endphp
                                    <img src="{{ str_starts_with($fotoTrabajador, 'http') ? $fotoTrabajador : asset('images/services/' . $fotoTrabajador) }}"
                                        alt="{{ $trabajador->nombre_completo }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                                    <h3>{{ $trabajador->nombre_completo }}</h3>
                                    <div class="worker-card-actions">
                                        <button type="button" class="admin-mini-btn compact"
                                            data-open-modal="modal-editar-trabajador-{{ $trabajador->id_trabajador }}">✎
                                            Editar</button>
                                        <form
                                            action="{{ route('admin.personal.trabajadores.eliminar', $trabajador) }}"
                                            method="POST" class="inline-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-mini-btn compact danger"
                                                data-confirm-title="Eliminar trabajador"
                                                data-confirm-message="¿Eliminar a {{ $trabajador->nombre_completo }}?"
                                                data-confirm-detail="Su foto y datos se eliminarán del módulo de personal si no existen citas asociadas."
                                                data-confirm-action="Eliminar"><span class="trash-icon"
                                                    aria-hidden="true"><svg viewBox="0 0 24 24">
                                                        <path d="M4 7h16M10 11v6M14 11v6M6 7l1 14h10l1-14M9 7V4h6v3" />
                                                    </svg></span><span>Eliminar</span></button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach

                            <button type="button" class="add-worker-card" data-open-modal="modal-crear-trabajador"
                                data-service-id="{{ $servicio->id_servicio }}">
                                <span>+</span>
                                <strong>Agregar trabajador</strong>
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="admin-empty-wide">No hay secciones registradas. Crea una nueva sección para organizar
                        el personal.</div>
                @endforelse

                <button type="button" class="new-section-bottom" data-open-modal="modal-crear-seccion">
                    <span>+</span>
                    <strong>Nueva sección</strong>
                    <small>Crea una nueva área o servicio para organizar a tu personal.</small>
                </button>
            </section>
        @endif

        @if ($tabVisual === 'servicios')
            <section class="admin-section-head service-head">
                <div>
                    <h1>Gestión de servicios</h1>
                    <p>Administra los servicios que ofrece tu centro de belleza.</p>
                </div>
                <button type="button" class="admin-btn admin-btn-gold"
                    data-open-modal="modal-crear-servicio"><span>+</span>Agregar nuevo servicio</button>
            </section>

            <section class="admin-service-grid">
                @forelse($servicios as $servicio)
                    <article class="admin-service-card">
                        <div class="admin-service-image">
                            @php
                                $imagenServicio = $servicio->imagen ?: 'default-service.jpg';
                            @endphp
                            <img src="{{ str_starts_with($imagenServicio, 'http') ? $imagenServicio : asset('images/services/' . $imagenServicio) }}"
                                alt="{{ $servicio->nombre_servicio }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                            <span
                                class="status-badge {{ $servicio->estado === 'activo' ? 'active' : 'inactive' }}"><i></i>{{ $servicio->estado === 'activo' ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                        <div class="service-card-body">
                            <div class="service-meta">
                                <strong>${{ number_format($servicio->precio, 0, ',', '.') }}</strong><span>{{ $servicio->duracion_minutos }}
                                    min</span>
                            </div>
                            <h2>{{ $servicio->nombre_servicio }}</h2>
                            <p>{{ $servicio->descripcion }}</p>
                            <div class="service-actions-row">
                                <form action="{{ route('admin.servicios.estado', $servicio) }}" method="POST"
                                    class="inline-form service-toggle-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="service-switch {{ $servicio->estado === 'activo' ? 'on' : '' }}"
                                        data-confirm-title="Cambiar estado"
                                        data-confirm-message="¿Quieres {{ $servicio->estado === 'activo' ? 'desactivar' : 'activar' }} este servicio?"
                                        data-confirm-detail="Al desactivarlo, dejará de verse en la página del cliente."
                                        data-confirm-action="Guardar estado"><span></span></button>
                                    <em>{{ $servicio->estado === 'activo' ? 'Activo' : 'Inactivo' }}</em>
                                </form>
                                <button type="button" class="admin-mini-btn edit-service"
                                    data-open-modal="modal-editar-servicio-{{ $servicio->id_servicio }}">✎
                                    Editar</button>
                                <form action="{{ route('admin.servicios.eliminar', $servicio) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-mini-btn danger"
                                        data-confirm-title="Eliminar servicio"
                                        data-confirm-message="¿Eliminar {{ $servicio->nombre_servicio }}?"
                                        data-confirm-detail="Se eliminarán sus datos y foto del catálogo si no existen registros asociados."
                                        data-confirm-action="Eliminar"><span class="trash-icon"
                                            aria-hidden="true"><svg viewBox="0 0 24 24">
                                                <path d="M4 7h16M10 11v6M14 11v6M6 7l1 14h10l1-14M9 7V4h6v3" />
                                            </svg></span><span>Eliminar</span></button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="admin-empty-wide">No hay servicios registrados.</div>
                @endforelse
            </section>

            <div class="admin-pagination-note">Mostrando 1 a {{ $servicios->count() }} de {{ $servicios->count() }}
                servicios <span>‹</span><b>1</b><span>›</span></div>

        @endif

        @if ($tabVisual === 'configuracion')
            <section class="admin-section-head config-head">
                <div>
                    <h1>Configuración de página principal</h1>
                    <p>Actualiza la imagen principal, los datos de contacto y los enlaces visibles para los clientes.
                    </p>
                </div>
            </section>

            <section class="site-config-card">
                <div class="site-config-preview">
                    <img src="{{ $heroRuta }}" alt="Imagen principal actual"
                        onerror="this.onerror=null;this.src='{{ asset('images/services/default-service.jpg') }}';">
                    <div>
                        <span>Configuración de página principal</span>
                        <h2>Imagen principal, contacto y horarios</h2>
                        <p>Actualiza la información visible en la página de clientes sin entrar al código.</p>
                    </div>
                </div>
                <form action="{{ route('admin.configuracion.actualizar') }}" method="POST"
                    enctype="multipart/form-data" class="site-config-form">
                    @csrf
                    @method('PATCH')
                    <label>Imagen principal<input type="file" name="hero_imagen" accept="image/*"></label>
                    <label>Ubicación<input type="text" name="contacto_ubicacion"
                            value="{{ old('contacto_ubicacion', $configuracion->contacto_ubicacion) }}"></label>
                    <label>Teléfono<input type="text" name="contacto_telefono"
                            value="{{ old('contacto_telefono', $configuracion->contacto_telefono) }}"></label>
                    <label>Correo<input type="email" name="contacto_correo"
                            value="{{ old('contacto_correo', $configuracion->contacto_correo) }}"></label>
                    <label class="full">Horario
                        <textarea name="contacto_horario" rows="3">{{ old('contacto_horario', $configuracion->contacto_horario) }}</textarea>
                    </label>
                    <label>Instagram<input type="url" name="instagram_url"
                            value="{{ old('instagram_url', $configuracion->instagram_url) }}"></label>
                    <label>WhatsApp<input type="url" name="whatsapp_url"
                            value="{{ old('whatsapp_url', $configuracion->whatsapp_url) }}"></label>
                    <button type="submit" class="admin-btn admin-btn-gold"
                        data-confirm-title="Guardar página principal"
                        data-confirm-message="¿Guardar los cambios de imagen, contacto y horarios?"
                        data-confirm-detail="La información se actualizará en la página visible para clientes."
                        data-confirm-action="Guardar cambios">Guardar cambios</button>
                </form>
            </section>
        @endif

        @if ($tabVisual === 'citas')
            <section class="admin-citas-original">
                <div class="admin-hero">
                    <h1>Gestión de citas</h1>
                    <p>Gestiona las citas, registra asistencia y consulta reservas por nombre, teléfono o servicio.</p>
                </div>

                <form class="barra-busqueda-citas" action="{{ route('admin.dashboard') }}" method="GET">
                    <input type="hidden" name="tab" value="citas">
                    <input type="search" name="buscar" value="{{ $busqueda }}"
                        placeholder="Buscar por nombre, teléfono o servicio">
                    <button type="submit" class="boton boton-primario">Buscar</button>
                    @if ($busqueda)
                        <a href="{{ route('admin.dashboard', ['tab' => 'citas']) }}"
                            class="boton boton-secundario">Limpiar</a>
                    @endif
                </form>

                <article class="admin-card admin-seccion">
                    <div class="admin-card-header">
                        <div>
                            <h2>Citas del día de hoy</h2>
                            <p>{{ ucfirst($hoy->translatedFormat('l, d \d\e F \d\e Y')) }}</p>
                        </div>
                    </div>
                    @include('admin.partials.lista-citas', ['citas' => $citasHoy])
                </article>

                <article class="admin-card admin-seccion">
                    <div class="admin-card-header">
                        <div>
                            <h2 id="titulo-todas-citas">Todas las citas</h2>
                            <p>Organizadas por secciones según su fecha.</p>
                        </div>
                        <button type="button" class="boton boton-secundario" id="btn-ver-calendario-admin">Ver citas
                            en calendario</button>
                    </div>

                    <div class="calendario-panel" id="calendario-admin-panel">
                        <div class="calendario-top">
                            <a
                                href="{{ route('admin.dashboard', [
                                    'tab' => 'citas',
                                    'mes' => $mesAnterior,
                                    'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                    'buscar' => $busqueda,
                                ]) }}#calendario-admin-panel">
                                ← Mes anterior
                            </a>

                            <h3>{{ ucfirst($mesActual->translatedFormat('F Y')) }}</h3>

                            <a
                                href="{{ route('admin.dashboard', [
                                    'tab' => 'citas',
                                    'mes' => $mesSiguiente,
                                    'fecha' => $fechaSeleccionada->format('Y-m-d'),
                                    'buscar' => $busqueda,
                                ]) }}#calendario-admin-panel">
                                Mes siguiente →
                            </a>
                        </div>

                        <div class="calendario">
                            @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia)
                                <div class="dia-nombre">{{ $dia }}</div>
                            @endforeach

                            @php
                                $diasCalendario = [];
                                $cursor = $inicioCalendario->copy();

                                while ($cursor->lte($finCalendario)) {
                                    $diasCalendario[] = $cursor->copy();
                                    $cursor->addDay();
                                }
                            @endphp

                            @foreach ($diasCalendario as $cursor)
                                @php
                                    $fecha = $cursor->format('Y-m-d');
                                    $total = (int) ($citasPorDia[$fecha] ?? 0);
                                    $clases = 'dia-calendario';

                                    if (!$cursor->isSameMonth($mesActual)) {
                                        $clases .= ' fuera-mes';
                                    }
                                    if ($total > 0) {
                                        $clases .= ' con-citas';
                                    }
                                    if ($cursor->isSameDay($fechaSeleccionada)) {
                                        $clases .= ' seleccionado';
                                    }
                                    if ($cursor->isToday()) {
                                        $clases .= ' hoy';
                                    }
                                @endphp

                                <a class="{{ $clases }}"
                                    href="{{ route('admin.dashboard', ['tab' => 'citas', 'mes' => $mesActual->format('Y-m'), 'fecha' => $fecha, 'buscar' => $busqueda]) }}#calendario-admin-panel">
                                    <span class="numero-dia">{{ $cursor->format('j') }}</span>

                                    @if ($total > 0)
                                        <span class="contador-citas">{{ $total }}
                                            cita{{ $total === 1 ? '' : 's' }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <div class="admin-card" style="margin-top:1rem;">
                            <h3>Citas del {{ $fechaSeleccionada->translatedFormat('d \d\e F \d\e Y') }}</h3>
                            @include('admin.partials.lista-citas', ['citas' => $citasFechaSeleccionada])
                        </div>
                    </div>

                    <div id="listado-admin-panel" class="listado-admin-panel grupos-citas-tiempo">
                        @forelse($citasAgrupadas as $tituloGrupo => $citasGrupo)
                            <section class="grupo-tiempo-citas">
                                <div class="grupo-tiempo-header">
                                    <h2>{{ $tituloGrupo }}</h2>
                                    <span>{{ $citasGrupo->count() }}
                                        cita{{ $citasGrupo->count() === 1 ? '' : 's' }}</span>
                                </div>
                                @include('admin.partials.lista-citas', ['citas' => $citasGrupo])
                            </section>
                        @empty
                            <div class="sin-citas">No se encontraron citas con los criterios seleccionados.</div>
                        @endforelse
                    </div>
                </article>
            </section>
        @endif

    </main>

    <div class="admin-modal" id="modal-crear-seccion" aria-hidden="true">
        <div class="admin-modal-backdrop" data-close-modal></div>
        <form class="admin-modal-card small" action="{{ route('admin.personal.secciones.guardar') }}"
            method="POST">
            @csrf
            <button type="button" class="modal-close" data-close-modal>×</button>
            <h2>Nueva sección</h2>
            <p>Crea un área para organizar trabajadores.</p>
            <label>Nombre de la sección<input type="text" name="nombre_servicio"
                    placeholder="Ej: Depilación con cera" required></label>
            <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Crear sección"
                data-confirm-message="¿Crear esta nueva sección?"
                data-confirm-detail="La sección quedará disponible para asignar trabajadores."
                data-confirm-action="Crear sección">Guardar sección</button>
        </form>
    </div>

    <div class="admin-modal" id="modal-crear-trabajador" aria-hidden="true">
        <div class="admin-modal-backdrop" data-close-modal></div>
        <form class="admin-modal-card" action="{{ route('admin.personal.trabajadores.guardar') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <button type="button" class="modal-close" data-close-modal>×</button>
            <h2>Agregar trabajador</h2>
            <p>Registra nombre, foto y sección del profesional.</p>
            <label>Sección<select name="id_servicio" id="select-crear-trabajador-servicio" required>
                    @foreach ($servicios as $servicio)
                        <option value="{{ $servicio->id_servicio }}">{{ $servicio->nombre_servicio }}</option>
                    @endforeach
                </select>
            </label>
            <label>Nombre<input type="text" name="nombre_completo" placeholder="Nombre del trabajador"
                    required></label>
            <label>Foto<input type="file" name="foto" accept="image/*" required></label>
            <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Agregar trabajador"
                data-confirm-message="¿Guardar este trabajador?"
                data-confirm-detail="El trabajador quedará asociado a la sección seleccionada."
                data-confirm-action="Guardar trabajador">Guardar trabajador</button>
        </form>
    </div>

    <div class="admin-modal" id="modal-crear-servicio" aria-hidden="true">
        <div class="admin-modal-backdrop" data-close-modal></div>
        <form class="admin-modal-card" action="{{ route('admin.servicios.guardar') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <button type="button" class="modal-close" data-close-modal>×</button>
            <h2>Agregar nuevo servicio</h2>
            <p>Completa los datos que verá el cliente en el catálogo.</p>
            <label>Nombre del servicio<input type="text" name="nombre_servicio" required></label>
            <label>Precio estimado<input type="number" name="precio" min="0" step="100"
                    required></label>
            <label>Duración estimada<input type="number" name="duracion_minutos" min="1" required></label>
            <label>Foto<input type="file" name="imagen" accept="image/*" required></label>
            <label class="full">Descripción
                <textarea name="descripcion" rows="4" required></textarea>
            </label>
            <label class="check-line"><input type="checkbox" name="estado" value="1" checked> Servicio
                activo</label>
            <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Crear servicio"
                data-confirm-message="¿Agregar este servicio al catálogo?"
                data-confirm-detail="Si está activo, será visible para los clientes."
                data-confirm-action="Agregar servicio">Guardar servicio</button>
        </form>
    </div>

    @foreach ($servicios as $servicio)
        <div class="admin-modal" id="modal-editar-seccion-{{ $servicio->id_servicio }}" aria-hidden="true">
            <div class="admin-modal-backdrop" data-close-modal></div>
            <form class="admin-modal-card small"
                action="{{ route('admin.personal.secciones.actualizar', $servicio) }}" method="POST">
                @csrf @method('PUT')
                <button type="button" class="modal-close" data-close-modal>×</button>
                <h2>Editar sección</h2>
                <label>Nombre de la sección<input type="text" name="nombre_servicio"
                        value="{{ $servicio->nombre_servicio }}" required></label>
                <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Guardar sección"
                    data-confirm-message="¿Guardar los cambios de esta sección?"
                    data-confirm-detail="El nombre se actualizará en el módulo de personal."
                    data-confirm-action="Guardar">Guardar cambios</button>
            </form>
        </div>

        <div class="admin-modal" id="modal-editar-servicio-{{ $servicio->id_servicio }}" aria-hidden="true">
            <div class="admin-modal-backdrop" data-close-modal></div>
            <form class="admin-modal-card" action="{{ route('admin.servicios.actualizar', $servicio) }}"
                method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <button type="button" class="modal-close" data-close-modal>×</button>
                <h2>Editar servicio</h2>
                <label>Nombre<input type="text" name="nombre_servicio" value="{{ $servicio->nombre_servicio }}"
                        required></label>
                <label>Precio<input type="number" name="precio" min="0" step="100"
                        value="{{ $servicio->precio }}" required></label>
                <label>Duración<input type="number" name="duracion_minutos" min="1"
                        value="{{ $servicio->duracion_minutos }}" required></label>
                <label>Foto<input type="file" name="imagen" accept="image/*"></label>
                <label class="full">Descripción
                    <textarea name="descripcion" rows="4" required>{{ $servicio->descripcion }}</textarea>
                </label>
                <label class="check-line"><input type="checkbox" name="estado" value="1"
                        {{ $servicio->estado === 'activo' ? 'checked' : '' }}> Servicio activo</label>
                <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Guardar servicio"
                    data-confirm-message="¿Guardar los cambios de {{ $servicio->nombre_servicio }}?"
                    data-confirm-detail="Se actualizará nombre, descripción, precio, duración, foto o estado."
                    data-confirm-action="Guardar cambios">Guardar cambios</button>
            </form>
        </div>
    @endforeach

    @foreach ($trabajadores as $trabajador)
        <div class="admin-modal" id="modal-editar-trabajador-{{ $trabajador->id_trabajador }}" aria-hidden="true">
            <div class="admin-modal-backdrop" data-close-modal></div>
            <form class="admin-modal-card"
                action="{{ route('admin.personal.trabajadores.actualizar', $trabajador) }}" method="POST"
                enctype="multipart/form-data">
                @csrf @method('PUT')
                <button type="button" class="modal-close" data-close-modal>×</button>
                <h2>Editar trabajador</h2>
                <label>Sección<select name="id_servicio" required>
                        @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->id_servicio }}"
                                {{ $trabajador->servicios->contains('id_servicio', $servicio->id_servicio) ? 'selected' : '' }}>
                                {{ $servicio->nombre_servicio }}</option>
                        @endforeach
                    </select></label>
                <label>Nombre<input type="text" name="nombre_completo" value="{{ $trabajador->nombre_completo }}"
                        required></label>
                <label>Foto<input type="file" name="foto" accept="image/*"></label>
                <button type="submit" class="admin-btn admin-btn-gold" data-confirm-title="Guardar trabajador"
                    data-confirm-message="¿Guardar los cambios de {{ $trabajador->nombre_completo }}?"
                    data-confirm-detail="Se actualizará el nombre, foto o sección del trabajador."
                    data-confirm-action="Guardar cambios">Guardar cambios</button>
            </form>
        </div>
    @endforeach
</body>

</html>
