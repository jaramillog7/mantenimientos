<?php

declare(strict_types=1);

function vista(string $ruta, array $datos = []): void
{
    Nucleo\Vista::renderizar($ruta, $datos);
}

function rutaBaseWeb(): string
{
    $nombreScript = $_SERVER['SCRIPT_NAME'] ?? '';
    $directorio = str_replace('\\', '/', dirname($nombreScript));

    if ($directorio === '/' || $directorio === '.') {
        return '';
    }

    return rtrim($directorio, '/');
}

function urlRecurso(string $rutaRelativa): string
{
    $base = rutaBaseWeb();
    return $base . '/' . ltrim($rutaRelativa, '/');
}

function urlRuta(string $rutaRelativa = '/'): string
{
    $base = rutaBaseWeb();
    $rutaLimpia = '/' . ltrim($rutaRelativa, '/');
    return $base . $rutaLimpia;
}

function csrf_token(): string
{
    return Nucleo\Csrf::token();
}

function flash_guardar(string $clave, string $mensaje): void
{
    Nucleo\Sesion::guardar('flash_' . $clave, $mensaje);
}

function flash_obtener(string $clave): ?string
{
    $claveFlash = 'flash_' . $clave;
    $mensaje = Nucleo\Sesion::obtener($claveFlash);
    Nucleo\Sesion::eliminar($claveFlash);
    return is_string($mensaje) ? $mensaje : null;
}
