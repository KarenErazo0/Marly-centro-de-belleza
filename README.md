# Marly Centro de Belleza - Sprint 1

Proyecto monolítico en Laravel 12 enfocado únicamente en:

- Visualización del catálogo de servicios.
- Registro de cliente.
- Inicio de sesión de cliente.
- Panel del cliente.
- Modificación de datos de la cuenta.
- Eliminación de cuenta.
- Cierre de sesión.

## Estructura modular en español

- `app/Http/Controllers/Inicio`
- `app/Http/Controllers/Cliente/Autenticacion`
- `app/Http/Controllers/Cliente/Cuenta`
- `app/Http/Middleware/VerificarSesionCliente.php`
- `resources/views/plantillas`
- `resources/views/inicio`
- `resources/views/cliente/autenticacion`
- `resources/views/cliente/cuenta`
- `resources/css/base`
- `resources/css/componentes`
- `resources/css/paginas`

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL

## Configuración

1. Copia el archivo de entorno:

```bash
cp .env.example .env
```

2. Configura tu conexión MySQL en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marly_centro_belleza
DB_USERNAME=root
DB_PASSWORD=
```

3. Genera la clave:

```bash
php artisan key:generate
```

4. Ejecuta las migraciones y el seeder:

```bash
php artisan migrate:fresh --seed
```

5. Instala dependencias front y genera assets:

```bash
npm install
npm run build
```

6. Inicia el proyecto:

```bash
php artisan serve
```

## Credenciales de prueba

- Correo: `cliente@marly.com`
- Contraseña: `cliente12345`

## Rutas

- `/` Inicio
- `/cliente/registro` Registro
- `/cliente/ingresar` Inicio de sesión
- `/cliente/cuenta` Panel y CRUD de cuenta
