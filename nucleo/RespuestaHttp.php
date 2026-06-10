<?php

declare(strict_types=1);

namespace Nucleo;

final class RespuestaHttp
{
    public static function enviar(string $contenido, int $codigoEstado = 200): void
    {
        http_response_code($codigoEstado);
        echo $contenido;
    }

    public static function redirigir(string $url, int $codigoEstado = 302): void
    {
        header('Location: ' . $url, true, $codigoEstado);
        exit;
    }
}
