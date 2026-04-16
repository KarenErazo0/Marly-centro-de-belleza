HU3 corregida:

1. La selección múltiple de servicios ahora se hace directamente en la pantalla principal.
2. La ruta /cliente/citas/agendar/servicios ahora redirige al inicio en #servicios para evitar una segunda pantalla duplicada.
3. El error de trabajadores ocurre cuando no se han ejecutado las migraciones nuevas de HU3.

Ejecuta en tu proyecto:

php artisan migrate
php artisan db:seed

Si tu base de datos ya tiene datos de prueba y quieres recrearla completa:

php artisan migrate:fresh --seed

Tablas nuevas necesarias para HU3:
- trabajadores
- trabajador_servicio
- citas
- cita_servicio


Actualización adicional:
- ahora la selección de profesionales se realiza por área de servicio
- si eliges varios servicios de áreas distintas, debes elegir un profesional por cada área
- se agregó la tabla cita_detalle para guardar el profesional asignado a cada servicio
- ejecuta php artisan migrate o php artisan migrate:fresh --seed para crear la nueva tabla
