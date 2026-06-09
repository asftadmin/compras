# Estructura del repositorio

Este documento resume la organizacion del proyecto y la responsabilidad de cada carpeta. El arbol incluye codigo propio y carpetas relevantes; se omiten detalles profundos de librerias vendorizadas para mantenerlo legible.

## Arbol principal

```text
compras/
|-- index.php
|-- index.js
|-- composer.json
|-- composer.lock
|-- .gitignore
|-- config/
|   |-- conexion.php
|   `-- config.js
|-- controller/
|   |-- curl.php
|   |-- Empleado.php
|   |-- Evaluacion.php
|   |-- Linea.php
|   |-- Proveedores.php
|   |-- Rol.php
|   `-- Usuario.php
|-- models/
|   |-- Empleado.php
|   |-- Evaluacion.php
|   |-- Linea.php
|   |-- Menu.php
|   |-- Proveedores.php
|   |-- Rol.php
|   `-- Usuario.php
|-- view/
|   |-- Empleados/
|   |-- Evaluaciones/
|   |-- inicio/
|   |-- Linea/
|   |-- Logout/
|   |-- MainFooter/
|   |-- MainHead/
|   |-- MainJS/
|   |-- MainMenu/
|   |-- MainNav/
|   |-- MntPerfil/
|   |-- Ordenes/
|   |-- PDF/
|   |-- Proveedores/
|   |-- Roles/
|   `-- Usuarios/
|-- public/
|   |-- css/
|   |-- img/
|   |-- dist/
|   |-- plugins/
|   |-- inicio/
|   |-- build/
|   |-- ReporteEvaluaciones.html
|   `-- RecuperarContrasena.html
|-- docs/
|   |-- fpdf.php
|   |-- font/
|   |-- makefont/
|   |-- tutorial/
|   `-- doc/
|-- include/
|   |-- composer.json
|   `-- composer.lock
`-- vendor/
```

## Raiz

- `index.php`: entrada publica de la aplicacion y formulario de login.
- `index.js`: script cargado desde el login.
- `composer.json`: dependencia principal del proyecto, actualmente PHPMailer.
- `composer.lock`: versiones bloqueadas de Composer.
- `.gitignore`: excluye `vendor/`.

## Configuracion

- `config/conexion.php`: inicia sesion, define la clase `Conectar`, crea la conexion PDO a PostgreSQL y expone `Conectar::ruta()`.
- `config/config.js`: define `BASE_URL` para el frontend.

## Controladores

Los controladores reciben operaciones por `$_GET['op']`, leen datos por `POST` o `GET`, llaman modelos y devuelven JSON, HTML parcial o datos en formato DataTables.

- `controller/Usuario.php`: guardar, listar, editar, eliminar usuarios y cambiar claves.
- `controller/Rol.php`: CRUD de roles y combos.
- `controller/Empleado.php`: CRUD local, consulta a Siesa y guardado de empleados importados.
- `controller/Proveedores.php`: CRUD local, consulta a Siesa, ordenes de compra y compras por linea.
- `controller/Linea.php`: CRUD y combos de lineas.
- `controller/Evaluacion.php`: creacion/listado de evaluaciones, consulta Siesa, guardado de detalle, listado de envios y envio de correos.
- `controller/curl.php`: cliente cURL para Siesa Cloud Connekta.

## Modelos

Los modelos extienden `Conectar` y encapsulan SQL de cada entidad.

- `models/Usuario.php`: login, sesiones, roles, permisos, CRUD de usuarios y claves.
- `models/Rol.php`: CRUD y listados de roles.
- `models/Empleado.php`: CRUD de empleados y guardado desde Siesa.
- `models/Proveedores.php`: CRUD de proveedores y consultas por linea/codigo Siesa.
- `models/Linea.php`: CRUD de lineas y relacion linea-proveedor.
- `models/Evaluacion.php`: evaluaciones, detalle, promedios, envios y estados.
- `models/Menu.php`: menu dinamico por roles y permisos.

## Vistas

Las vistas PHP validan sesion, cargan layout comun y enlazan el JavaScript del modulo.

- `view/MainHead/head.php`: estilos y metadatos comunes.
- `view/MainNav/nav.php`: barra superior con datos del usuario.
- `view/MainMenu/menu.php`: menu lateral dinamico por roles.
- `view/MainFooter/footer.php`: pie comun.
- `view/MainJS/JS.php`: scripts comunes.
- `view/inicio/inicio.php`: pantalla principal.
- `view/Usuarios/`: mantenimiento de usuarios y modal de clave.
- `view/Roles/`: mantenimiento de roles y vista de permisos.
- `view/Empleados/`: empleados locales y consulta/importacion desde Siesa.
- `view/Proveedores/`: proveedores locales y consulta/importacion desde Siesa.
- `view/Linea/`: mantenimiento de lineas.
- `view/Evaluaciones/`: evaluaciones, detalle, seleccion de proveedores, envios y acciones de correo/PDF.
- `view/Ordenes/`: reportes de ordenes y compras por linea.
- `view/PDF/ReporteEvaluacion.php`: generador de PDF.
- `view/MntPerfil/`: perfil de usuario.
- `view/Logout/logout.php`: cierre de sesion.

## Assets publicos

- `public/css/`: hojas de estilo propias y de plantillas.
- `public/img/`: logos, iconos e imagenes de la aplicacion.
- `public/plugins/`: librerias frontend como Bootstrap, DataTables, SweetAlert, Select2, Chart.js, AdminLTE y otras.
- `public/dist/` y `public/build/`: recursos de plantilla/AdminLTE.
- `public/inicio/`: otra copia de recursos de plantilla usados por el dashboard.
- `public/ReporteEvaluaciones.html`: plantilla para correo de evaluacion.
- `public/RecuperarContrasena.html`: plantilla/pagina HTML relacionada con recuperacion de clave.

## Documentacion y librerias locales

- `docs/fpdf.php`: libreria FPDF usada por los reportes.
- `docs/font/`, `docs/makefont/`, `docs/tutorial/` y `docs/doc/`: archivos de documentacion y recursos de FPDF.
- `vendor/`: dependencias instaladas por Composer.
- `include/`: contiene otro `composer.json` y `composer.lock` con PHPMailer; parece un paquete secundario o legado.

## Convenciones detectadas

- Cada modulo suele tener una vista principal `.php`, un modal/fragmento CRUD `.php` y un archivo `.js`.
- Las tablas frontend se alimentan con DataTables y respuestas JSON tipo `aaData`.
- Los controladores usan un `switch` sobre `op`.
- Las rutas relativas de vistas apuntan a controladores con `../../controller/...`.
- El menu depende de las tablas `menu` y `permiso`, y de los roles guardados en `$_SESSION["user_roles"]`.

## Conteo aproximado

- Codigo propio en `config`, `controller`, `models` y `view`: 43 archivos PHP y 12 archivos JS.
- El repositorio completo contiene muchas mas entradas por librerias frontend, FPDF, AdminLTE, assets y dependencias.

## Observaciones

- `controller/Menu.php` no existe en el arbol actual, aunque `view/Roles/crudRoles.js` lo llama para listar y cambiar permisos.
- No se encontro un archivo SQL de esquema o migraciones.
- No se encontraron pruebas automatizadas.
- El nombre de este archivo conserva la escritura solicitada: `structre.md`.

