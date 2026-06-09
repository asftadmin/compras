# Control de Proveedores

Aplicacion web PHP para el control de proveedores de ASFALTART S.A.S. El sistema centraliza usuarios, roles, empleados, proveedores, lineas, evaluaciones de proveedores y reportes de ordenes de compra, con integracion a Siesa Cloud Connekta y envio de evaluaciones por correo.

## Tecnologias

- PHP con PDO para PostgreSQL.
- Apache/XAMPP como entorno local sugerido.
- JavaScript, jQuery, Bootstrap y AdminLTE para la interfaz.
- DataTables para listados y exportacion.
- SweetAlert/SweetAlert2 para mensajes y confirmaciones.
- Chart.js para graficos.
- PHPMailer para envio de correos.
- FPDF para generacion de reportes PDF.
- API Siesa Cloud Connekta para consultas externas.

## Flujo general

1. `index.php` muestra el login y procesa el inicio de sesion.
2. `config/conexion.php` inicia la sesion, crea la conexion PDO a PostgreSQL y define la ruta base.
3. `models/Usuario.php` valida credenciales, carga datos del empleado, roles y permisos.
4. Las vistas de `view/` reutilizan cabecera, barra superior, menu lateral, footer y scripts comunes.
5. Los archivos `.js` de cada modulo llaman por AJAX a `controller/*.php?op=...`.
6. Los controladores validan la operacion solicitada y delegan consultas/actualizaciones a `models/`.
7. Los modelos ejecutan SQL sobre las tablas locales.
8. Algunos modulos consultan Siesa Cloud mediante `controller/curl.php`.
9. Las evaluaciones pueden generar PDF en `view/PDF/ReporteEvaluacion.php` y enviarse por correo con PHPMailer.

## Modulos principales

- **Usuarios**: CRUD de usuarios, cambio de clave y asociacion con empleados.
- **Roles**: CRUD de roles. La vista tambien referencia gestion de permisos por menu.
- **Menu y permisos**: `models/Menu.php` construye el menu dinamico segun roles y permisos activos.
- **Empleados App**: CRUD de empleados registrados localmente.
- **Empleados Siesa**: consulta empleados desde Siesa y permite guardar empleados nuevos en la base local.
- **Proveedores App**: CRUD de proveedores locales, estado, NIT, codigo Siesa y linea asociada.
- **Proveedores Siesa**: consulta proveedores desde Siesa.
- **Linea**: CRUD de lineas o categorias usadas para agrupar proveedores.
- **Evaluaciones**: crea evaluaciones por rango de fechas, consulta resultados en Siesa, guarda detalle, lista envios y genera reportes PDF.
- **Ordenes de compra**: consulta ordenes y compras por linea/proveedor desde Siesa, con tabla y grafico.
- **Perfil y logout**: gestion basica de perfil y cierre de sesion.

## Requisitos

- PHP compatible con las dependencias instaladas.
- Apache o servidor web equivalente.
- PostgreSQL con la base de datos del proyecto.
- Extensiones PHP recomendadas: `pdo_pgsql`, `pgsql`, `curl`, `mbstring` y `openssl`.
- Composer.
- Acceso de red a Siesa Cloud Connekta y al SMTP configurado.
- Permisos de escritura para `public/temp` cuando se generen PDFs temporales.

## Instalacion local

1. Clonar o copiar el proyecto en `C:\xampp\htdocs\compras`.
2. Instalar dependencias PHP:

```bash
composer install
```

3. Verificar que Apache y PostgreSQL esten activos.
4. Configurar la conexion de base de datos en `config/conexion.php`.
5. Configurar la URL base en `Conectar::ruta()` y en `config/config.js`.
6. Configurar credenciales/tokens de Siesa en `controller/curl.php`.
7. Configurar SMTP en `controller/Evaluacion.php`.
8. Abrir la aplicacion en:

```text
http://localhost/compras/
```

## Base de datos

Las tablas principales inferidas desde el codigo son:

- `usuarios`
- `usuario_roles`
- `roles`
- `permiso`
- `menu`
- `empleados`
- `proveedores`
- `linea`
- `evaluacion`
- `evaluacion_detalle`

El repositorio no incluye un script SQL de instalacion visible en la raiz, por lo que la estructura de base de datos debe restaurarse desde el respaldo o ambiente existente.

## Integraciones externas

- **Siesa Cloud Connekta**: se consulta mediante `controller/curl.php`.
- **SMTP Office 365**: se usa para enviar correos de evaluacion desde `controller/Evaluacion.php`.
- **CDN externos**: algunas vistas cargan librerias desde Google Fonts, Font Awesome, Ionicons, Select2 y Chart.js.

## Reportes y archivos generados

- `view/PDF/ReporteEvaluacion.php` genera el PDF de evaluacion de proveedor usando FPDF.
- `public/ReporteEvaluaciones.html` funciona como plantilla HTML del correo.
- `public/temp` se usa para PDFs temporales antes de adjuntarlos a correos.

## Observaciones de mantenimiento

- Hay credenciales de base de datos, Siesa y SMTP escritas directamente en el codigo. Conviene moverlas a variables de entorno o a un archivo de configuracion no versionado.
- Las claves de usuario se validan con `MD5`. Para produccion se recomienda migrar a `password_hash()` y `password_verify()`.
- `view/Roles/crudRoles.js` referencia `controller/Menu.php`, pero ese archivo no aparece en el arbol actual del repositorio.
- No se encontraron pruebas automatizadas.
- `vendor/` esta ignorado en `.gitignore`, aunque existe una copia local en el arbol actual.

