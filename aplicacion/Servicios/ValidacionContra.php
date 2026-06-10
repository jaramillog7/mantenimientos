<?php

declare(strict_types=1);

namespace Aplicacion\Servicios;

use Aplicacion\Repositorios\RepositorioUsuariosSistema;
use Nucleo\Autenticacion;

final class ServicioAutenticacion
{
    private RepositorioUsuariosSistema $repositorioUsuariosSistema;

    public function __construct(RepositorioUsuariosSistema $repositorioUsuariosSistema)
    {
        $this->repositorioUsuariosSistema = $repositorioUsuariosSistema;
    }

    public function autenticar(string $correo, string $password): bool
    {
        $correoLimpio = trim(strtolower($correo));
        if ($correoLimpio === '' || $password === '') {
            return false;
        }

        $usuario = $this->repositorioUsuariosSistema->obtenerPorCorreo($correoLimpio);
        if ($usuario === null) {
            return false;
        }

        if ((int) $usuario['estado'] !== 1) {
            return false;
        }

        if (!password_verify($password, (string) $usuario['password_hash'])) {
            return false;
        }

        Autenticacion::iniciarSesion($usuario);
        $this->repositorioUsuariosSistema->actualizarUltimoAcceso((int) $usuario['id']);
        return true;
    }
}
