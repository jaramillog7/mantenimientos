<?php

declare(strict_types=1);

namespace Nucleo;

final class CargadorEntorno
{
    public static function cargar(string $rutaArchivo): void
    {
        if (!is_file($rutaArchivo) || !is_readable($rutaArchivo)) {
            return;
        }

        $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lineas === false) {
            return;
        }

        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if ($linea === '' || str_starts_with($linea, '#')) {
                continue;
            }

            $posicionSeparador = strpos($linea, '=');
            if ($posicionSeparador === false) {
                continue;
            }

            $clave = trim(substr($linea, 0, $posicionSeparador));
            $valor = trim(substr($linea, $posicionSeparador + 1));

            if ($clave === '') {
                continue;
            }

            $valor = trim($valor, "\"'");

            $_ENV[$clave] = $valor;
            $_SERVER[$clave] = $valor;
            putenv($clave . '=' . $valor);
        }
    }
}

