<?php

declare(strict_types=1);

namespace Aplicacion\Repositorios;

use Nucleo\ConexionBaseDatos;
use PDO;

final class RepositorioUsuariosSistema
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = ConexionBaseDatos::obtenerConexion();
    }

    public function obtenerPorCorreo(string $correo): ?array
    {
        $sql = <<<SQL
        SELECT id, nombre, correo, password_hash, rol, estado
        FROM usuarios_sistema
        WHERE correo = :correo
        LIMIT 1
        SQL;

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':correo', $correo, PDO::PARAM_STR);
        $sentencia->execute();

        $resultado = $sentencia->fetch();
        return $resultado === false ? null : $resultado;
    }

    public function actualizarUltimoAcceso(int $idUsuarioSistema): void
    {
        $sql = 'UPDATE usuarios_sistema SET ultimo_acceso = NOW() WHERE id = :id';
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':id', $idUsuarioSistema, PDO::PARAM_INT);
        $sentencia->execute();
    }
}

