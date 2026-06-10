<?php

declare(strict_types=1);

namespace Aplicacion\Controladores;

use Nucleo\Autenticacion;
use Nucleo\RespuestaHttp;

final class ControladorInicio
{
    public function index(): void
    {
        if (Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/panel'));
        }

        RespuestaHttp::redirigir(urlRuta('/login'));
    }
}
