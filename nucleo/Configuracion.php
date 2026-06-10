<?php

declare(strict_types=1);

namespace Nucleo;

final class Configuracion
{
    public static function obtener(string $clave, ?string $porDefecto = null): ?string
    {
        $valorEnv = $_ENV[$clave] ?? $_SERVER[$clave] ?? getenv($clave);
        if ($valorEnv === false || $valorEnv === null || $valorEnv === '') {
            return $porDefecto;
        }

        return (string) $valorEnv;
    }
}

