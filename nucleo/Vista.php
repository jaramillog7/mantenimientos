<?php

declare(strict_types=1);

namespace Nucleo;

final class Vista
{
    public static function renderizar(string $ruta, array $datos = []): void
    {
        $archivo = RUTA_BASE . '/aplicacion/Vistas/' . trim($ruta, '/') . '.php';
        if (!is_file($archivo)) {
            RespuestaHttp::enviar('Vista no encontrada', 500);
            return;
        }

        extract($datos, EXTR_SKIP);
        require $archivo;
    }
}

