# Sistema de Mantenimientos TI

Este proyecto usa una arquitectura MVC ligera en PHP (sin framework) y esta preparado para trabajar en XAMPP.

## Punto de entrada unico

- `index.php`

El navegador entra por `index.php`, se carga el arranque y luego se despachan rutas.

## Flujo de ejecucion

1. `index.php` define `RUTA_BASE`.
2. Carga `arranque/inicializador.php`.
3. Se registra autoload (con mapa de compatibilidad de clases a archivos renombrados).
4. Se crea el enrutador (`Nucleo\Enrutador`).
5. Se cargan rutas desde `rutas/rutas_app.php`.
6. Se despacha la ruta HTTP al controlador correspondiente.

## Estructura real del proyecto

### Raiz

- `index.php`: front controller unico.
- `.env.example`: plantilla publica para configuracion local.
- `.env`: configuracion sensible (BD y entorno), no se versiona.
- `.htaccess`: reescritura de URLs amigables.
- `README.md`: documentacion.

### `arranque/`

- `inicializador.php`: inicializa helpers, autoload, `.env` y sesion.

### `nucleo/`

- `Autenticacion.php`: sesion autenticada del usuario sistema.
- `CargadorEntorno.php`: lectura de `.env`.
- `Conexionbd.php`: clase `ConexionBaseDatos` (PDO MySQL).
- `Configuracion.php`: acceso a variables de configuracion.
- `Csrf.php`: token y validacion CSRF.
- `Enrutador.php`: registro/despacho de rutas.
- `Funciones.php`: helpers globales (`vista`, `urlRuta`, `urlRecurso`, flash, csrf token).
- `RespuestaHttp.php`: redirecciones y respuestas HTTP.
- `Sesion.php`: utilidades de sesion.
- `Vista.php`: renderizado de vistas.

### `rutas/`

- `rutas_app.php`: rutas principales de la aplicacion.
- `compatibilidad_rutas.php`: puente de compatibilidad que incluye `rutas_app.php`.

### `aplicacion/Controladores/`

- `Login.php`: clase `ControladorInicio`.
- `Autenticacion.php`: clase `ControladorAutenticacion`.
- `Panel.php`: clase `ControladorPanel`.

### `aplicacion/Datos/`

- `ConsUsuarios.php`: clase `RepositorioUsuarios`.
- `LogiUsuarios.php`: clase `RepositorioUsuariosSistema`.
- `Operaciones.php`: clase `RepositorioMantenimientos`.

### `aplicacion/Servicios/`

- `CalculadorFec.php`: clase `ServicioCalculadorMantenimiento`.
- `ValidacionContra.php`: clase `ServicioAutenticacion`.

### `aplicacion/Vistas/`

- `AccesoSistema/formulario_login.php`: vista de login.
- `InicioSistema/pantalla_inicio.php`: vista de inicio.
- `PanelMantenimientos/orden_panel.php`: layout principal del panel.
- `PanelMantenimientos/Componentes/`
  - `sidebar.php`
  - `encabezado.php`
  - `alertas.php`
  - `tarjeta.php`
- `PanelMantenimientos/Modulos/`
  - `modulo_proximos.php`
  - `modulo_programados.php`
  - `modulo_realizados.php`
  - `modulo_lista_usuarios.php`
  - `modulo_configuracion.php`

### `publico/assets/`

- `css/inicio.css`
- `css/login.css`
- `css/panel.css`
- `js/inicio.js`

### `archivos/`

- `modelo_mantenimientos.sql`
- `inserciones_usuarios.sql`: dataset demo sin datos reales.
- `inserciones_mantenimientos.sql`: dataset demo sin datos reales.
- `README_PUBLICO.md`: nota sobre privacidad y fuentes excluidas.

### `sql/`

- `001_usuarios_sistema.sql`
- `003_areas_desde_cronograma.sql`

## Rutas HTTP actuales

Definidas en `rutas/rutas_app.php`:

- `GET /` -> `ControladorInicio::index`
- `GET /inicio` -> `ControladorInicio::index`
- `GET /login` -> `ControladorAutenticacion::formularioLogin`
- `POST /login` -> `ControladorAutenticacion::procesarLogin`
- `GET /logout` -> `ControladorAutenticacion::logout`
- `GET /panel` -> `ControladorPanel::index`
- `POST /panel/proximos/programar` -> `ControladorPanel::marcarProximoComoProgramado`
- `POST /panel/programados/hecho` -> `ControladorPanel::marcarProgramadoComoHecho`
- `POST /panel/reversiones/revertir` -> `ControladorPanel::revertirUltimaAccion`

## Seguridad del repositorio publico

- El repositorio no incluye `.env`.
- Los seeds publicados usan datos demo anonimizados.
- Los archivos operativos internos como cronogramas fuente y listados reales se excluyen del control de versiones.

## Nota importante sobre nombres

Renombraste archivos fisicos a nombres mas claros para tu equipo. Para no romper el sistema, el autoload de `arranque/inicializador.php` incluye un mapa explicito entre:

- Nombre de clase (namespace) esperado por el codigo
- Archivo fisico real donde esta esa clase

Asi se mantiene compatibilidad sin forzarte a renombrar nuevamente.
