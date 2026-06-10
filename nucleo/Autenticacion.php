<?php

declare(strict_types=1);

namespace Nucleo;

final class Autenticacion
{
    private const CLAVE_USUARIO = 'usuario_autenticado';

    public static function iniciarSesion(array $usuario): void
    {
        Sesion::regenerarId();
        Sesion::guardar(self::CLAVE_USUARIO, [
            'id' => (int) $usuario['id'],
            'nombre' => (string) $usuario['nombre'],
            'correo' => (string) $usuario['correo'],
            'rol' => (string) $usuario['rol'],
        ]);
    }

    public static function cerrarSesion(): void
    {
        Sesion::eliminar(self::CLAVE_USUARIO);
        Sesion::regenerarId();
    }

    public static function estaAutenticado(): bool
    {
        $usuario = Sesion::obtener(self::CLAVE_USUARIO);
        return is_array($usuario) && isset($usuario['id']);
    }

    public static function usuario(): ?array
    {
        $usuario = Sesion::obtener(self::CLAVE_USUARIO);
        return is_array($usuario) ? $usuario : null;
    }
}

