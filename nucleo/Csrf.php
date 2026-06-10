<?php

declare(strict_types=1);

namespace Nucleo;

final class Csrf
{
    private const CLAVE_TOKEN = '_token_csrf';

    public static function token(): string
    {
        $token = Sesion::obtener(self::CLAVE_TOKEN);
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            Sesion::guardar(self::CLAVE_TOKEN, $token);
        }

        return $token;
    }

    public static function validar(?string $tokenFormulario): bool
    {
        if ($tokenFormulario === null || $tokenFormulario === '') {
            return false;
        }

        $tokenSesion = Sesion::obtener(self::CLAVE_TOKEN);
        if (!is_string($tokenSesion) || $tokenSesion === '') {
            return false;
        }

        return hash_equals($tokenSesion, $tokenFormulario);
    }
}

