<?php

declare(strict_types=1);

namespace Aplicacion\Repositorios;

use Nucleo\ConexionBaseDatos;
use PDO;

final class RepositorioUsuarios
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = ConexionBaseDatos::obtenerConexion();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarTodos(?int $areaId = null, string $busqueda = ''): array
    {
        $sqlBase = <<<SQL
        SELECT
            u.id,
            u.nombre,
            u.codigo_activo,
            u.serial_equipo,
            u.area_id,
            u.estado_usuario,
            COALESCE(a.nombre, 'SIN AREA') AS area_nombre
        FROM usuarios u
        LEFT JOIN areas a ON a.id = u.area_id
        SQL;

        $condiciones = [];
        if ($areaId !== null && $areaId > 0) {
            $condiciones[] = 'u.area_id = :area_id';
        }

        $busqueda = trim($busqueda);
        $esCodigo = $busqueda !== '' && ctype_digit($busqueda);
        if ($busqueda !== '') {
            if ($esCodigo) {
                $condiciones[] = 'u.codigo_activo = :codigo_activo_busqueda';
            } else {
                $condiciones[] = 'u.nombre LIKE :nombre_busqueda';
            }
        }

        if ($condiciones !== []) {
            $sqlBase .= "\nWHERE " . implode(' AND ', $condiciones);
        }

        $sqlBase .= <<<SQL

        ORDER BY area_nombre ASC, u.nombre ASC
        SQL;

        $sentencia = $this->conexion->prepare($sqlBase);
        if ($areaId !== null && $areaId > 0) {
            $sentencia->bindValue(':area_id', $areaId, PDO::PARAM_INT);
        }
        if ($busqueda !== '') {
            if ($esCodigo) {
                $sentencia->bindValue(':codigo_activo_busqueda', (int) $busqueda, PDO::PARAM_INT);
            } else {
                $sentencia->bindValue(':nombre_busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
            }
        }
        $sentencia->execute();
        $filas = $sentencia->fetchAll();

        return is_array($filas) ? $filas : [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarAreasActivas(): array
    {
        $sql = <<<SQL
        SELECT id, nombre
        FROM areas
        WHERE estado = 1
        ORDER BY nombre ASC
        SQL;

        $sentencia = $this->conexion->query($sql);
        $filas = $sentencia->fetchAll();
        return is_array($filas) ? $filas : [];
    }

    public function resolverCodigoActivoPorBusqueda(string $busqueda): array
    {
        $busqueda = trim($busqueda);
        if ($busqueda === '') {
            return ['ok' => false, 'codigo_activo' => null, 'mensaje' => 'Ingresa un codigo activo o nombre de usuario.'];
        }

        if (ctype_digit($busqueda)) {
            return ['ok' => true, 'codigo_activo' => (int) $busqueda, 'mensaje' => ''];
        }

        $sql = <<<SQL
        SELECT codigo_activo
        FROM usuarios
        WHERE nombre LIKE :nombre_busqueda
        ORDER BY nombre ASC
        LIMIT 3
        SQL;
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':nombre_busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
        $sentencia->execute();
        $filas = $sentencia->fetchAll();
        if (!is_array($filas) || count($filas) === 0) {
            return ['ok' => false, 'codigo_activo' => null, 'mensaje' => 'No se encontro usuario con ese nombre.'];
        }
        if (count($filas) > 1) {
            return ['ok' => false, 'codigo_activo' => null, 'mensaje' => 'Hay varios usuarios con ese nombre. Usa codigo activo para mayor precision.'];
        }

        return [
            'ok' => true,
            'codigo_activo' => (int) ($filas[0]['codigo_activo'] ?? 0),
            'mensaje' => '',
        ];
    }

    public function obtenerPorId(int $usuarioId): ?array
    {
        $sql = <<<SQL
        SELECT id, nombre, codigo_activo, serial_equipo, area_id, estado_usuario
        FROM usuarios
        WHERE id = :id
        LIMIT 1
        SQL;
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $sentencia->execute();
        $fila = $sentencia->fetch();

        return is_array($fila) ? $fila : null;
    }

    public function actualizarUsuario(int $usuarioId, string $nombre, int $codigoActivo, string $serialEquipo, ?int $areaId): bool
    {
        $sql = <<<SQL
        UPDATE usuarios
        SET
            nombre = :nombre,
            codigo_activo = :codigo_activo,
            serial_equipo = :serial_equipo,
            area_id = :area_id,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
        LIMIT 1
        SQL;
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $sentencia->bindValue(':codigo_activo', $codigoActivo, PDO::PARAM_INT);
        $sentencia->bindValue(':serial_equipo', $serialEquipo, PDO::PARAM_STR);
        if ($areaId === null || $areaId <= 0) {
            $sentencia->bindValue(':area_id', null, PDO::PARAM_NULL);
        } else {
            $sentencia->bindValue(':area_id', $areaId, PDO::PARAM_INT);
        }
        $sentencia->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $sentencia->execute();

        return $sentencia->rowCount() > 0;
    }

    public function cambiarEstadoUsuario(int $usuarioId, bool $activo): bool
    {
        $sql = <<<SQL
        UPDATE usuarios
        SET estado_usuario = :estado, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
        LIMIT 1
        SQL;
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':estado', $activo ? 1 : 0, PDO::PARAM_INT);
        $sentencia->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $sentencia->execute();

        return $sentencia->rowCount() > 0;
    }

    public function eliminarUsuarioSeguro(int $usuarioId): array
    {
        $sqlCuenta = 'SELECT COUNT(*) AS total FROM mantenimientos WHERE usuario_id = :usuario_id';
        $stCuenta = $this->conexion->prepare($sqlCuenta);
        $stCuenta->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stCuenta->execute();
        $fila = $stCuenta->fetch();
        $totalMantenimientos = (int) ($fila['total'] ?? 0);

        if ($totalMantenimientos > 0) {
            return [
                'ok' => false,
                'mensaje' => 'No se puede eliminar este usuario porque tiene mantenimientos asociados. Usa Desactivar.',
            ];
        }

        $sqlDelete = 'DELETE FROM usuarios WHERE id = :id LIMIT 1';
        $stDelete = $this->conexion->prepare($sqlDelete);
        $stDelete->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stDelete->execute();

        if ($stDelete->rowCount() <= 0) {
            return ['ok' => false, 'mensaje' => 'No se encontro el usuario para eliminar.'];
        }

        return ['ok' => true, 'mensaje' => 'Usuario eliminado correctamente.'];
    }
}
