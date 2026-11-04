# Plan de mantenimiento y evolucion

Fecha: 2026-11-04.

Este plan parte del inventario de [structure.md](structure.md) y de la revision estatica del codigo. Las tareas pendientes son propuestas; su presencia aqui no significa que se hayan implementado ni probado contra PostgreSQL, Siesa o SMTP.

## Estado actual

- [x] Revisar la estructura y los principales flujos del repositorio.
- [x] Actualizar `structure.md` y conservar una referencia desde `structre.md`.
- [x] Documentar prioridades y criterios de aceptacion en `plan.md`.
- [ ] Ejecutar las mejoras y las validaciones descritas abajo.

La aplicacion ya incluye CRUD de usuarios, roles, empleados, proveedores y lineas; consulta Siesa; evaluaciones con PDF y correo; reportes de ordenes; y un dashboard con comparativos de compras de hasta tres anios y de dos evaluaciones.

## 1. Preparar un entorno reproducible — prioridad alta

- [ ] Registrar versiones utilizadas de PHP, PostgreSQL y extensiones, y verificar la instalacion con el `composer.lock` raiz.
- [ ] Obtener y documentar el esquema de base de datos sin datos personales ni credenciales; preparar un procedimiento de restauracion y datos de prueba.
- [ ] Documentar configuracion de URL base, Siesa y SMTP mediante ejemplos sin secretos.
- [ ] Establecer una lista de comprobaciones para login, permisos, CRUD, dashboard, evaluaciones, PDF y correo en un entorno de pruebas.

Criterio de aceptacion: otra persona puede instalar el proyecto y reproducir los flujos con datos de prueba siguiendo instrucciones documentadas.

## 2. Corregir contratos y permisos — prioridad alta

- [ ] Implementar las operaciones `listarxrol`, `habilitar` y `deshabilitar` de `controller/Menu.php`, referenciadas por `view/Roles/crudRoles.js`, junto con los metodos necesarios del modelo.
- [ ] Validar autorizacion en el servidor para consultas, modificaciones, reportes y envios; revisar su correspondencia con los permisos de sesion y el menu.
- [ ] Alinear el parametro de cambio de clave del perfil: `Perfil.js` envia `user_contrasena`, pero la operacion `Password` espera `user_password`.
- [ ] Devolver y comprobar resultados de exito/error antes de mostrar confirmaciones de cambio de clave.

Criterio de aceptacion: los permisos se pueden listar y modificar, las operaciones rechazan accesos sin autorizacion y el perfil permite cambiar la clave y comprobarla en un nuevo inicio de sesion.

## 3. Mejorar configuracion y autenticacion — prioridad alta

- [ ] Extraer las credenciales de PostgreSQL, Siesa y SMTP a configuracion externa no versionada, con una plantilla de variables requeridas.
- [ ] Planificar el reemplazo de las credenciales expuestas en el codigo y documentar el cambio de configuracion.
- [ ] Sustituir MD5 por `password_hash()` y `password_verify()`, con migracion compatible de usuarios existentes y revision del tipo/longitud de columna.
- [ ] Revisar la validacion del login, la regeneracion de sesion y el cierre de sesion.
- [ ] Agregar proteccion CSRF a operaciones que modifican datos y evitar devolver hashes de claves en las respuestas de usuario.

Criterio de aceptacion: la aplicacion funciona con configuracion externa, las cuentas existentes pueden migrar y las claves nuevas se almacenan con el mecanismo actualizado; las peticiones de escritura validan sesion, permiso y CSRF.

## 4. Fortalecer evaluaciones, PDF y correo — prioridad media

- [ ] Validar fechas, estructura del detalle, identificadores y datos obligatorios antes de importar evaluaciones.
- [ ] Revisar la regla de duplicados por NIT, tipo y rango de fechas; definir su alcance entre evaluaciones y reforzarla en la base de datos para evitar duplicados concurrentes.
- [ ] Evaluar transacciones para importaciones de multiples detalles y definir el comportamiento ante fallos parciales.
- [ ] Verificar generacion, adjunto y limpieza de PDFs temporales en `public/temp/`.
- [ ] Comprobar que el estado de envio cambie segun el resultado real de SMTP y que los fallos permitan reintentos controlados.

Criterio de aceptacion: repetir una importacion no duplica detalles segun la regla acordada; un fallo deja un estado consistente y el envio adjunta el PDF correcto sin marcar como enviados los correos fallidos.

## 5. Validar reportes y dashboard — prioridad media

- [ ] Verificar compras por linea, proveedor y hasta tres anios contra datos conocidos de Siesa.
- [ ] Verificar comparacion de dos evaluaciones, filtro de proveedores, promedios, diferencias y casos sin calificacion.
- [ ] Revisar respuestas ante errores, tiempos de espera o datos vacios de Siesa, incluidos mensajes y recuperacion de botones de consulta.
- [ ] Comprobar tablas y graficos con muchos proveedores, filtros cambiantes y distintas dimensiones de pantalla.

Criterio de aceptacion: tablas, indicadores y graficos coinciden con la fuente verificada; los casos vacios y fallidos muestran un estado comprensible y permiten una nueva consulta.

## 6. Consolidar dependencias y mantenimiento — prioridad baja

- [ ] Revisar el uso del manifiesto secundario `include/composer.json` antes de consolidarlo con el raiz; resolver la diferencia de PHPMailer `^6.8` frente a `^7.0`.
- [ ] Inventariar recursos usados en `public/` y `public/inicio/` antes de retirar duplicados.
- [ ] Incorporar comprobacion de sintaxis PHP y pruebas de regresion para permisos, cambio de clave, duplicados y calculos del dashboard.
- [ ] Actualizar README, estructura y este plan al completar cada etapa, con evidencia de validacion.

Criterio de aceptacion: las dependencias se instalan de manera reproducible, las rutas de recursos siguen funcionando y las comprobaciones cubren los flujos criticos.

## Orden de ejecucion y validacion

Preparar primero el entorno y los datos de prueba. Continuar con contratos/permisos y autenticacion, luego evaluaciones y dashboard, y finalmente consolidar dependencias. Antes de modificar esquema o claves, disponer de respaldo y procedimiento de recuperacion.

Por cada cambio, comprobar sintaxis de los archivos PHP afectados y ejecutar las verificaciones funcionales relacionadas. Usar destinatarios de prueba para SMTP y registrar los resultados de consultas externas. Marcar una tarea completada solo cuando cumpla su criterio de aceptacion.
