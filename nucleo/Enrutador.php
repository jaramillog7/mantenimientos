<?php

declare(strict_types=1);

namespace Nucleo;

final class Enrutador
{
    /** @var array<string, array<string, callable>> */
    private array $rutas = [];

    public function get(string $ruta, callable $accion): void
    {
        $this->rutas['GET'][$this->normalizarRuta($ruta)] = $accion;
    }

    public function post(string $ruta, callable $accion): void
    {
        $this->rutas['POST'][$this->normalizarRuta($ruta)] = $accion;
    }

    public function despachar(string $metodo, string $uri): void
    {
        $ruta = $this->normalizarRuta((string) parse_url($uri, PHP_URL_PATH));
        $accion = $this->rutas[$metodo][$ruta] ?? null;

        if ($accion === null) {
            RespuestaHttp::enviar('Ruta no encontrada', 404);
            return;
        }

        call_user_func($accion);
    }

    private function normalizarRuta(string $ruta): string
    {
        if ($ruta === '') {
            return '/';
        }

        $rutaLimpia = '/' . trim($ruta, '/');
        return $rutaLimpia === '//' ? '/' : $rutaLimpia;
    }
}
