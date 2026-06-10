<?php

declare(strict_types=1);

namespace Nucleo;

final class Sesion
{
    public static function iniciar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function guardar(string $clave, mixed $valor): void
    {
        $_SESSION[$clave] = $valor;
    }

    public static function obtener(string $clave, mixed $porDefecto = null): mixed
    {
        return $_SESSION[$clave] ?? $porDefecto;
    }

    public static function eliminar(string $clave): void
    {
        unset($_SESSION[$clave]);
    }

    public static function destruir(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function regenerarId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

