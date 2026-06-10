<?php

declare(strict_types=1);

require RUTA_BASE . '/nucleo/Funciones.php';

spl_autoload_register(static function (string $clase): void {
    $mapaClases = [
        // Controladores
        'Aplicacion\\Controladores\\ControladorAutenticacion' => RUTA_BASE . '/aplicacion/Controladores/Autenticacion.php',
        'Aplicacion\\Controladores\\ControladorInicio' => RUTA_BASE . '/aplicacion/Controladores/Login.php',
        'Aplicacion\\Controladores\\ControladorPanel' => RUTA_BASE . '/aplicacion/Controladores/Panel.php',
        // Datos / Repositorios
        'Aplicacion\\Repositorios\\RepositorioUsuarios' => RUTA_BASE . '/aplicacion/Datos/ConsUsuarios.php',
        'Aplicacion\\Repositorios\\RepositorioUsuariosSistema' => RUTA_BASE . '/aplicacion/Datos/LogiUsuarios.php',
        'Aplicacion\\Repositorios\\RepositorioMantenimientos' => RUTA_BASE . '/aplicacion/Datos/Operaciones.php',
        // Servicios
        'Aplicacion\\Servicios\\ServicioCalculadorMantenimiento' => RUTA_BASE . '/aplicacion/Servicios/CalculadorFec.php',
        'Aplicacion\\Servicios\\ServicioAutenticacion' => RUTA_BASE . '/aplicacion/Servicios/ValidacionContra.php',
        // Nucleo
        'Nucleo\\ConexionBaseDatos' => RUTA_BASE . '/nucleo/Conexionbd.php',
        'Nucleo\\Funciones' => RUTA_BASE . '/nucleo/Funciones.php',
    ];

    if (isset($mapaClases[$clase]) && is_file($mapaClases[$clase])) {
        require $mapaClases[$clase];
        return;
    }

    $prefijos = [
        'Aplicacion\\' => RUTA_BASE . '/aplicacion/',
        'Nucleo\\' => RUTA_BASE . '/nucleo/',
    ];

    foreach ($prefijos as $prefijo => $directorioBase) {
        $longitud = strlen($prefijo);
        if (strncmp($prefijo, $clase, $longitud) !== 0) {
            continue;
        }

        $claseRelativa = substr($clase, $longitud);
        $archivo = $directorioBase . str_replace('\\', '/', $claseRelativa) . '.php';

        if (is_file($archivo)) {
            require $archivo;
        }
    }
});

\Nucleo\CargadorEntorno::cargar(RUTA_BASE . '/.env');
\Nucleo\Sesion::iniciar();
