<?php

declare(strict_types=1);

namespace Aplicacion\Controladores;

use Aplicacion\Repositorios\RepositorioUsuariosSistema;
use Aplicacion\Servicios\ServicioAutenticacion;
use Nucleo\Autenticacion;
use Nucleo\Csrf;
use Nucleo\RespuestaHttp;

final class ControladorAutenticacion
{
    public function formularioLogin(): void
    {
        if (Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/panel'));
        }

        vista('AccesoSistema/formulario_login', [
            'titulo' => 'Ingreso al Sistema',
            'error' => flash_obtener('error_login'),
        ]);
    }

    public function procesarLogin(): void
    {
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error_login', 'Sesion expirada. Vuelve a intentarlo.');
            RespuestaHttp::redirigir(urlRuta('/login'));
        }

        $correo = (string) ($_POST['correo'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $servicioAutenticacion = new ServicioAutenticacion(new RepositorioUsuariosSistema());
        $autenticado = $servicioAutenticacion->autenticar($correo, $password);

        if (!$autenticado) {
            flash_guardar('error_login', 'Credenciales invalidas o usuario inactivo.');
            RespuestaHttp::redirigir(urlRuta('/login'));
        }

        RespuestaHttp::redirigir(urlRuta('/panel'));
    }

    public function logout(): void
    {
        Autenticacion::cerrarSesion();
        RespuestaHttp::redirigir(urlRuta('/login'));
    }
}
