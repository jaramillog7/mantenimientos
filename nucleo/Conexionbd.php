<?php

declare(strict_types=1);

namespace Nucleo;

use PDO;
use PDOException;
use RuntimeException;

final class ConexionBaseDatos
{
    private static ?PDO $conexion = null;

    public static function obtenerConexion(): PDO
    {
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        $host = Configuracion::obtener('DB_HOST', '127.0.0.1');
        $puerto = Configuracion::obtener('DB_PUERTO', '3306');
        $baseDatos = Configuracion::obtener('DB_NOMBRE', 'mantenimientos');
        $usuario = Configuracion::obtener('DB_USUARIO', 'root');
        $contrasena = Configuracion::obtener('DB_PASSWORD', '');
        $charset = Configuracion::obtener('DB_CHARSET', 'utf8mb4');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $puerto,
            $baseDatos,
            $charset
        );

        try {
            self::$conexion = new PDO($dsn, $usuario, $contrasena, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'No fue posible conectarse a la base de datos. Verifica el archivo .env.',
                0,
                $e
            );
        }

        return self::$conexion;
    }
}

