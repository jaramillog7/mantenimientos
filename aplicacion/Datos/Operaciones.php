<?php

declare(strict_types=1);

namespace Aplicacion\Repositorios;

use Nucleo\ConexionBaseDatos;
use PDO;
use Throwable;

final class RepositorioMantenimientos
{
    private PDO $conexion;

    public function __construct()
    {
        $this->conexion = ConexionBaseDatos::obtenerConexion();
        $this->asegurarEstructuraTecnicos();
    }

    public function obtenerUltimoMantenimientoPorCodigoActivo(int $codigoActivo): ?array
    {
        $sql = <<<SQL
        SELECT
            u.id AS usuario_id,
            u.nombre,
            u.codigo_activo,
            MAX(COALESCE(m.fecha_ejecucion, m.fecha_programada)) AS ultima_fecha_programada
        FROM usuarios u
        LEFT JOIN mantenimientos m ON m.usuario_id = u.id
        WHERE u.codigo_activo = :codigo_activo
        GROUP BY u.id, u.nombre, u.codigo_activo
        LIMIT 1
        SQL;

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':codigo_activo', $codigoActivo, PDO::PARAM_INT);
        $sentencia->execute();

        $resultado = $sentencia->fetch();
        if ($resultado === false) {
            return null;
        }

        return [
            'usuario_id' => (int) $resultado['usuario_id'],
            'nombre' => (string) $resultado['nombre'],
            'codigo_activo' => (int) $resultado['codigo_activo'],
            'ultima_fecha_programada' => $resultado['ultima_fecha_programada'] !== null
                ? (string) $resultado['ultima_fecha_programada']
                : null,
        ];
    }

    public function contarPorEstado(string $estado, string $busqueda = ''): int
    {
        if ($estado === 'pendiente') {
            $sql = <<<SQL
            SELECT COUNT(DISTINCT u.id) AS total
            FROM usuarios u
            INNER JOIN mantenimientos m ON m.usuario_id = u.id
            WHERE m.estado = :estado
            SQL;

            $busqueda = trim($busqueda);
            $esCodigo = $busqueda !== '' && ctype_digit($busqueda);
            if ($busqueda !== '') {
                if ($esCodigo) {
                    $sql .= "\nAND u.codigo_activo = :codigo_activo_busqueda";
                } else {
                    $sql .= "\nAND u.nombre LIKE :nombre_busqueda";
                }
            }

            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindValue(':estado', $estado, PDO::PARAM_STR);
            if ($busqueda !== '') {
                if ($esCodigo) {
                    $sentencia->bindValue(':codigo_activo_busqueda', (int) $busqueda, PDO::PARAM_INT);
                } else {
                    $sentencia->bindValue(':nombre_busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
                }
            }
            $sentencia->execute();
            $fila = $sentencia->fetch();
            return (int) ($fila['total'] ?? 0);
        }

        $sql = <<<SQL
        SELECT COUNT(*) AS total
        FROM mantenimientos m
        INNER JOIN usuarios u ON u.id = m.usuario_id
        WHERE m.estado = :estado
        SQL;
        if ($estado === 'realizado') {
            $sql .= "\nAND m.observaciones LIKE '%Hecho desde modulo Programados.%'";
        }

        $busqueda = trim($busqueda);
        $esCodigo = $busqueda !== '' && ctype_digit($busqueda);
        if ($busqueda !== '') {
            if ($esCodigo) {
                $sql .= "\nAND u.codigo_activo = :codigo_activo_busqueda";
            } else {
                $sql .= "\nAND u.nombre LIKE :nombre_busqueda";
            }
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':estado', $estado, PDO::PARAM_STR);
        if ($busqueda !== '') {
            if ($esCodigo) {
                $sentencia->bindValue(':codigo_activo_busqueda', (int) $busqueda, PDO::PARAM_INT);
            } else {
                $sentencia->bindValue(':nombre_busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
            }
        }
        $sentencia->execute();
        $fila = $sentencia->fetch();
        return (int) ($fila['total'] ?? 0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarPorEstado(string $estado, int $limite, int $desplazamiento, string $busqueda = ''): array
    {
        $sql = <<<SQL
        SELECT
            m.id AS mantenimiento_id,
            u.id AS usuario_id,
            u.nombre,
            u.codigo_activo,
            COALESCE(a.nombre, 'SIN AREA') AS area_nombre,
            COALESCE(
                (
                    SELECT MAX(COALESCE(m2.fecha_ejecucion, m2.fecha_programada))
                    FROM mantenimientos m2
                    WHERE m2.usuario_id = u.id
                      AND m2.estado = 'realizado'
                ),
                (
                    SELECT MAX(COALESCE(m3.fecha_ejecucion, m3.fecha_programada))
                    FROM mantenimientos m3
                    WHERE m3.usuario_id = u.id
                      AND m3.id <> m.id
                      AND m3.fecha_programada <= m.fecha_programada
                ),
                m.fecha_programada
            ) AS ultima_fecha_programada,
            m.fecha_programada AS proxima_fecha_programada,
            m.fecha_ejecucion,
            m.observaciones,
            m.tecnico_responsable
        FROM usuarios u
        INNER JOIN mantenimientos m ON m.usuario_id = u.id
        LEFT JOIN areas a ON a.id = u.area_id
        WHERE m.estado = :estado
        SQL;
        if ($estado === 'pendiente') {
            $sql .= <<<SQL

        AND m.id = (
            SELECT mpend.id
            FROM mantenimientos mpend
            WHERE mpend.usuario_id = u.id
              AND mpend.estado = 'pendiente'
            ORDER BY mpend.fecha_programada ASC, mpend.id DESC
            LIMIT 1
        )
        SQL;
        }
        if ($estado === 'realizado') {
            $sql .= "\nAND m.observaciones LIKE '%Hecho desde modulo Programados.%'";
        }

        $busqueda = trim($busqueda);
        $esCodigo = $busqueda !== '' && ctype_digit($busqueda);
        if ($busqueda !== '') {
            if ($esCodigo) {
                $sql .= "\nAND u.codigo_activo = :codigo_activo_busqueda";
            } else {
                $sql .= "\nAND u.nombre LIKE :nombre_busqueda";
            }
        }

        if ($estado === 'realizado') {
            $sql .= "\nORDER BY COALESCE(m.fecha_ejecucion, m.fecha_programada) DESC, u.nombre ASC";
        } elseif ($estado === 'pendiente') {
            $sql .= "\nORDER BY u.nombre ASC";
        } else {
            $sql .= "\nORDER BY m.fecha_programada ASC, u.nombre ASC";
        }
        if ($estado !== 'pendiente') {
            $sql .= "\nLIMIT :limite OFFSET :desplazamiento";
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':estado', $estado, PDO::PARAM_STR);
        if ($busqueda !== '') {
            if ($esCodigo) {
                $sentencia->bindValue(':codigo_activo_busqueda', (int) $busqueda, PDO::PARAM_INT);
            } else {
                $sentencia->bindValue(':nombre_busqueda', '%' . $busqueda . '%', PDO::PARAM_STR);
            }
        }
        if ($estado !== 'pendiente') {
            $sentencia->bindValue(':limite', $limite, PDO::PARAM_INT);
            $sentencia->bindValue(':desplazamiento', $desplazamiento, PDO::PARAM_INT);
        }
        $sentencia->execute();
        $filas = $sentencia->fetchAll();
        if (!is_array($filas)) {
            return [];
        }

        foreach ($filas as &$fila) {
            $fila['observaciones'] = $this->limpiarObservacionesSistema((string) ($fila['observaciones'] ?? ''));
        }
        unset($fila);

        if ($estado === 'pendiente') {
            foreach ($filas as &$fila) {
                $ultima = (string) ($fila['ultima_fecha_programada'] ?? '');
                if ($ultima !== '') {
                    $fila['proxima_fecha_programada'] = $this->calcularProximaSemestralDesde($ultima);
                }
            }
            unset($fila);

            usort($filas, static function (array $a, array $b): int {
                $fechaA = (string) ($a['proxima_fecha_programada'] ?? '');
                $fechaB = (string) ($b['proxima_fecha_programada'] ?? '');
                if ($fechaA === $fechaB) {
                    return strcmp((string) ($a['nombre'] ?? ''), (string) ($b['nombre'] ?? ''));
                }
                return strcmp($fechaA, $fechaB);
            });

            $filas = array_slice($filas, $desplazamiento, $limite);
        }

        return $filas;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listarTecnicosActivos(): array
    {
        $sql = <<<SQL
        SELECT id, nombre
        FROM tecnicos
        WHERE estado = 1
        ORDER BY nombre ASC
        SQL;

        $sentencia = $this->conexion->query($sql);
        $filas = $sentencia->fetchAll();
        return is_array($filas) ? $filas : [];
    }

    public function registrarTecnico(string $nombre): array
    {
        $nombreLimpio = trim($nombre);
        if ($nombreLimpio === '') {
            return ['ok' => false, 'mensaje' => 'Debes ingresar el nombre del tecnico o responsable.'];
        }

        try {
            $sql = <<<SQL
            INSERT INTO tecnicos (nombre, estado)
            VALUES (:nombre, 1)
            SQL;
            $sentencia = $this->conexion->prepare($sql);
            $sentencia->bindValue(':nombre', $nombreLimpio, PDO::PARAM_STR);
            $sentencia->execute();

            return ['ok' => true, 'mensaje' => 'Tecnico registrado correctamente.'];
        } catch (Throwable $e) {
            return ['ok' => false, 'mensaje' => 'No fue posible registrar el tecnico. Verifica si ya existe con ese nombre.'];
        }
    }

    public function obtenerNombreTecnicoPorId(int $tecnicoId): ?string
    {
        $sql = <<<SQL
        SELECT nombre
        FROM tecnicos
        WHERE id = :id
          AND estado = 1
        LIMIT 1
        SQL;

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':id', $tecnicoId, PDO::PARAM_INT);
        $sentencia->execute();
        $fila = $sentencia->fetch();

        if (!is_array($fila) || trim((string) ($fila['nombre'] ?? '')) === '') {
            return null;
        }

        return trim((string) $fila['nombre']);
    }

    private function calcularProximaSemestralDesde(string $fechaBase): string
    {
        $fecha = \DateTimeImmutable::createFromFormat('Y-m-d', $fechaBase);
        if ($fecha === false || $fecha->format('Y-m-d') !== $fechaBase) {
            return $fechaBase;
        }

        $proxima = $fecha->add(new \DateInterval('P6M'));
        while ((int) $proxima->format('N') >= 6) {
            $proxima = $proxima->add(new \DateInterval('P1D'));
        }

        return $proxima->format('Y-m-d');
    }

    public function obtenerResumenSemestralActual(): array
    {
        $hoy = new \DateTimeImmutable('today');
        $anio = (int) $hoy->format('Y');
        $mes = (int) $hoy->format('n');
        $fase = $mes <= 6 ? 1 : 2;
        $inicio = $fase === 1 ? sprintf('%04d-01-01', $anio) : sprintf('%04d-07-01', $anio);
        $fin = $fase === 1 ? sprintf('%04d-06-30', $anio) : sprintf('%04d-12-31', $anio);

        $sqlTotales = <<<SQL
        SELECT COUNT(*) AS total
        FROM usuarios
        WHERE estado_usuario = 1
        SQL;
        $totalUsuarios = (int) (($this->conexion->query($sqlTotales)->fetch()['total'] ?? 0));

        $sqlBaseFase = <<<SQL
        SELECT COUNT(DISTINCT u.id) AS total
        FROM usuarios u
        WHERE u.estado_usuario = 1
          AND EXISTS (
              SELECT 1
              FROM mantenimientos m
              WHERE m.usuario_id = u.id
                AND COALESCE(m.fecha_ejecucion, m.fecha_programada) IS NOT NULL
                AND COALESCE(m.fecha_ejecucion, m.fecha_programada) <= :fin_base
          )
        SQL;
        $stBaseFase = $this->conexion->prepare($sqlBaseFase);
        $stBaseFase->bindValue(':fin_base', $fin, PDO::PARAM_STR);
        $stBaseFase->execute();
        $totalBaseFase = (int) (($stBaseFase->fetch()['total'] ?? 0));

        $sqlRealizados = <<<SQL
        SELECT COUNT(DISTINCT u.id) AS total
        FROM usuarios u
        INNER JOIN mantenimientos m ON m.usuario_id = u.id
        WHERE u.estado_usuario = 1
          AND m.estado = 'realizado'
          AND m.fecha_ejecucion IS NOT NULL
          AND m.fecha_ejecucion BETWEEN :inicio AND :fin
          AND EXISTS (
              SELECT 1
              FROM mantenimientos mh
              WHERE mh.usuario_id = u.id
                AND COALESCE(mh.fecha_ejecucion, mh.fecha_programada) IS NOT NULL
                AND COALESCE(mh.fecha_ejecucion, mh.fecha_programada) <= :fin_historial
          )
        SQL;
        $stRealizados = $this->conexion->prepare($sqlRealizados);
        $stRealizados->bindValue(':inicio', $inicio, PDO::PARAM_STR);
        $stRealizados->bindValue(':fin', $fin, PDO::PARAM_STR);
        $stRealizados->bindValue(':fin_historial', $fin, PDO::PARAM_STR);
        $stRealizados->execute();
        $totalRealizados = (int) (($stRealizados->fetch()['total'] ?? 0));

        $sqlProgramados = <<<SQL
        SELECT COUNT(DISTINCT u.id) AS total
        FROM usuarios u
        INNER JOIN mantenimientos m ON m.usuario_id = u.id
        WHERE u.estado_usuario = 1
          AND m.estado = 'programado'
          AND m.fecha_programada BETWEEN :inicio AND :fin
          AND EXISTS (
              SELECT 1
              FROM mantenimientos mh
              WHERE mh.usuario_id = u.id
                AND COALESCE(mh.fecha_ejecucion, mh.fecha_programada) IS NOT NULL
                AND COALESCE(mh.fecha_ejecucion, mh.fecha_programada) <= :fin_base_fase
          )
          AND u.id NOT IN (
              SELECT DISTINCT m2.usuario_id
              FROM mantenimientos m2
              WHERE m2.estado = 'realizado'
                AND m2.fecha_ejecucion IS NOT NULL
                AND m2.fecha_ejecucion BETWEEN :inicio_sub AND :fin_sub
          )
        SQL;
        $stProgramados = $this->conexion->prepare($sqlProgramados);
        $stProgramados->bindValue(':inicio', $inicio, PDO::PARAM_STR);
        $stProgramados->bindValue(':fin', $fin, PDO::PARAM_STR);
        $stProgramados->bindValue(':fin_base_fase', $fin, PDO::PARAM_STR);
        $stProgramados->bindValue(':inicio_sub', $inicio, PDO::PARAM_STR);
        $stProgramados->bindValue(':fin_sub', $fin, PDO::PARAM_STR);
        $stProgramados->execute();
        $totalProgramados = (int) (($stProgramados->fetch()['total'] ?? 0));

        $totalPendientes = max(0, $totalBaseFase - $totalRealizados);
        $indiceCumplimiento = $totalBaseFase > 0
            ? (int) round(($totalRealizados / $totalBaseFase) * 100)
            : 0;

        return [
            'fase' => $fase,
            'fase_nombre' => $fase === 1 ? 'Primer semestre' : 'Segundo semestre',
            'inicio' => $inicio,
            'fin' => $fin,
            'total_usuarios' => $totalUsuarios,
            'total_base_fase' => $totalBaseFase,
            'total_no_exigibles' => max(0, $totalUsuarios - $totalBaseFase),
            'total_realizados' => $totalRealizados,
            'total_programados' => $totalProgramados,
            'total_pendientes' => $totalPendientes,
            'indice_cumplimiento' => $indiceCumplimiento,
        ];
    }

    public function normalizarPendientesDuplicados(): int
    {
        $sqlUsuarios = <<<SQL
        SELECT usuario_id
        FROM mantenimientos
        WHERE estado = 'pendiente'
        GROUP BY usuario_id
        HAVING COUNT(*) > 1
        SQL;
        $stUsuarios = $this->conexion->query($sqlUsuarios);
        $usuarios = $stUsuarios->fetchAll(PDO::FETCH_COLUMN);
        if (!is_array($usuarios) || $usuarios === []) {
            return 0;
        }

        $this->conexion->beginTransaction();
        try {
            $cancelados = 0;
            $sqlPendientes = <<<SQL
            SELECT id
            FROM mantenimientos
            WHERE usuario_id = :usuario_id
              AND estado = 'pendiente'
            ORDER BY fecha_programada ASC, id ASC
            SQL;
            $sqlCancelar = <<<SQL
            UPDATE mantenimientos
            SET
                estado = 'cancelado',
                observaciones = CONCAT(COALESCE(observaciones, ''), ' | Cancelado automaticamente por normalizacion de pendientes duplicados.'),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            LIMIT 1
            SQL;

            $stPendientes = $this->conexion->prepare($sqlPendientes);
            $stCancelar = $this->conexion->prepare($sqlCancelar);

            foreach ($usuarios as $usuarioId) {
                $stPendientes->bindValue(':usuario_id', (int) $usuarioId, PDO::PARAM_INT);
                $stPendientes->execute();
                $ids = $stPendientes->fetchAll(PDO::FETCH_COLUMN);
                if (!is_array($ids) || count($ids) <= 1) {
                    continue;
                }

                array_shift($ids);
                foreach ($ids as $idCancelar) {
                    $stCancelar->bindValue(':id', (int) $idCancelar, PDO::PARAM_INT);
                    $stCancelar->execute();
                    $cancelados += $stCancelar->rowCount();
                }
            }

            $this->conexion->commit();
            return $cancelados;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return 0;
        }
    }

    public function marcarComoProgramado(int $mantenimientoId, string $fechaProgramada): bool
    {
        $sql = <<<SQL
        UPDATE mantenimientos
        SET
            estado = 'programado',
            fecha_programada = :fecha_programada,
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Programado desde modulo Proximos.'),
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id AND estado = 'pendiente'
        SQL;
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->bindValue(':fecha_programada', $fechaProgramada, PDO::PARAM_STR);
        $sentencia->bindValue(':id', $mantenimientoId, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->rowCount() > 0;
    }

    public function marcarProgramadoComoHechoYGenerarSiguiente(int $mantenimientoId, string $fechaEjecucion, string $tecnicoResponsable, string $observaciones): bool
    {
        $this->conexion->beginTransaction();

        try {
            $sqlBuscar = <<<SQL
            SELECT id, usuario_id, estado
            FROM mantenimientos
            WHERE id = :id
            LIMIT 1
            SQL;
            $stBuscar = $this->conexion->prepare($sqlBuscar);
            $stBuscar->bindValue(':id', $mantenimientoId, PDO::PARAM_INT);
            $stBuscar->execute();
            $registro = $stBuscar->fetch();

            if (!is_array($registro) || (string) ($registro['estado'] ?? '') !== 'programado') {
                $this->conexion->rollBack();
                return false;
            }
            $usuarioId = (int) $registro['usuario_id'];

            $sqlUpdate = <<<SQL
            UPDATE mantenimientos
            SET
                estado = 'realizado',
                fecha_ejecucion = :fecha_ejecucion,
                tecnico_responsable = :tecnico_responsable,
                observaciones = :observaciones
            WHERE id = :id
            SQL;
            $stUpdate = $this->conexion->prepare($sqlUpdate);
            $stUpdate->bindValue(':fecha_ejecucion', $fechaEjecucion, PDO::PARAM_STR);
            $stUpdate->bindValue(':tecnico_responsable', trim($tecnicoResponsable), PDO::PARAM_STR);
            $observacionFinal = $this->construirObservacionRealizado($observaciones);
            if ($observacionFinal === null) {
                $stUpdate->bindValue(':observaciones', null, PDO::PARAM_NULL);
            } else {
                $stUpdate->bindValue(':observaciones', $observacionFinal, PDO::PARAM_STR);
            }
            $stUpdate->bindValue(':id', $mantenimientoId, PDO::PARAM_INT);
            $stUpdate->execute();

            $fechaBase = new \DateTimeImmutable($fechaEjecucion);
            $siguiente = $fechaBase->add(new \DateInterval('P6M'));
            while ((int) $siguiente->format('N') >= 6) {
                $siguiente = $siguiente->add(new \DateInterval('P1D'));
            }

            $sqlInsert = <<<SQL
            INSERT INTO mantenimientos
                (usuario_id, fecha_programada, hora_programada, estado, fecha_ejecucion, observaciones)
            VALUES
                (:usuario_id, :fecha_programada, NULL, 'pendiente', NULL, :observaciones)
            SQL;
            $stInsert = $this->conexion->prepare($sqlInsert);
            $stInsert->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stInsert->bindValue(':fecha_programada', $siguiente->format('Y-m-d'), PDO::PARAM_STR);
            $stInsert->bindValue(':observaciones', 'Generado automatico despues de marcar Hecho.', PDO::PARAM_STR);
            $stInsert->execute();

            $this->conexion->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function revertirAccionesPorCodigoActivo(int $codigoActivo, int $cantidad): array
    {
        if ($cantidad < 1) {
            $cantidad = 1;
        }
        if ($cantidad > 10) {
            $cantidad = 10;
        }

        $this->conexion->beginTransaction();

        try {
            $usuarioId = $this->obtenerUsuarioIdPorCodigoActivo($codigoActivo);
            if ($usuarioId === null) {
                $this->conexion->rollBack();
                return ['ok' => false, 'mensaje' => 'No existe un usuario con ese codigo activo.'];
            }

            $reversionesAplicadas = 0;
            for ($i = 0; $i < $cantidad; $i++) {
                $accionRevertida = $this->revertirUnaAccionInterna($usuarioId);
                if ($accionRevertida === null) {
                    break;
                }
                $reversionesAplicadas++;
            }

            if ($reversionesAplicadas === 0) {
                $this->conexion->rollBack();
                return ['ok' => false, 'mensaje' => 'No hay acciones reversibles para este usuario.'];
            }

            $this->conexion->commit();

            if ($reversionesAplicadas < $cantidad) {
                return [
                    'ok' => true,
                    'mensaje' => 'Se revirtieron ' . $reversionesAplicadas . ' accion(es). No habia mas acciones reversibles.',
                ];
            }

            return ['ok' => true, 'mensaje' => 'Se revirtieron ' . $reversionesAplicadas . ' accion(es) correctamente.'];
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return ['ok' => false, 'mensaje' => 'Error inesperado al revertir la accion.'];
        }
    }

    private function obtenerUsuarioIdPorCodigoActivo(int $codigoActivo): ?int
    {
        $sql = <<<SQL
        SELECT id
        FROM usuarios
        WHERE codigo_activo = :codigo_activo
        LIMIT 1
        SQL;
        $st = $this->conexion->prepare($sql);
        $st->bindValue(':codigo_activo', $codigoActivo, PDO::PARAM_INT);
        $st->execute();
        $fila = $st->fetch();
        if (!is_array($fila)) {
            return null;
        }
        return (int) $fila['id'];
    }

    private function revertirUnaAccionInterna(int $usuarioId): ?string
    {
        $ultimoProgramado = $this->buscarUltimoProgramadoReversible($usuarioId);
        $ultimoRealizado = $this->buscarUltimoRealizadoReversible($usuarioId);

        $tsProgramado = is_array($ultimoProgramado) ? strtotime((string) $ultimoProgramado['updated_at']) : false;
        $tsRealizado = is_array($ultimoRealizado) ? strtotime((string) $ultimoRealizado['updated_at']) : false;

        if ($tsProgramado === false && $tsRealizado === false) {
            return null;
        }

        if ($tsRealizado !== false && ($tsProgramado === false || $tsRealizado >= $tsProgramado)) {
            $pendienteAuto = $this->buscarPendienteAutomatico($usuarioId);
            if (!is_array($pendienteAuto)) {
                return null;
            }

            $sqlBorrarPendiente = "DELETE FROM mantenimientos WHERE id = :id LIMIT 1";
            $stBorrar = $this->conexion->prepare($sqlBorrarPendiente);
            $stBorrar->bindValue(':id', (int) $pendienteAuto['id'], PDO::PARAM_INT);
            $stBorrar->execute();

            $sqlRevertirHecho = <<<SQL
            UPDATE mantenimientos
            SET
                estado = 'programado',
                fecha_ejecucion = NULL,
                observaciones = CONCAT(COALESCE(observaciones, ''), ' | Revertido desde modulo Reversiones (HECHO).'),
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            LIMIT 1
            SQL;
            $stRevertir = $this->conexion->prepare($sqlRevertirHecho);
            $stRevertir->bindValue(':id', (int) $ultimoRealizado['id'], PDO::PARAM_INT);
            $stRevertir->execute();
            if ($stRevertir->rowCount() === 0) {
                return null;
            }

            return 'HECHO';
        }

        $sqlRevertirProgramado = <<<SQL
        UPDATE mantenimientos
        SET
            estado = 'pendiente',
            observaciones = CONCAT(COALESCE(observaciones, ''), ' | Revertido desde modulo Reversiones (PROGRAMAR).'),
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
        LIMIT 1
        SQL;
        $stRevertir = $this->conexion->prepare($sqlRevertirProgramado);
        $stRevertir->bindValue(':id', (int) $ultimoProgramado['id'], PDO::PARAM_INT);
        $stRevertir->execute();
        if ($stRevertir->rowCount() === 0) {
            return null;
        }

        return 'PROGRAMAR';
    }

    private function buscarUltimoProgramadoReversible(int $usuarioId): ?array
    {
        $sql = <<<SQL
        SELECT id, updated_at
        FROM mantenimientos
        WHERE usuario_id = :usuario_id
          AND estado = 'programado'
          AND (
              observaciones LIKE '%Programado desde modulo Proximos.%'
              OR observaciones LIKE '%Hecho desde modulo Programados.%'
              OR observaciones LIKE '%Revertido desde modulo Reversiones (HECHO).%'
          )
        ORDER BY updated_at DESC, id DESC
        LIMIT 1
        SQL;
        $st = $this->conexion->prepare($sql);
        $st->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $st->execute();
        $fila = $st->fetch();
        return is_array($fila) ? $fila : null;
    }

    private function buscarUltimoRealizadoReversible(int $usuarioId): ?array
    {
        $sql = <<<SQL
        SELECT id, updated_at
        FROM mantenimientos
        WHERE usuario_id = :usuario_id
          AND estado = 'realizado'
          AND observaciones LIKE '%Hecho desde modulo Programados.%'
        ORDER BY updated_at DESC, id DESC
        LIMIT 1
        SQL;
        $st = $this->conexion->prepare($sql);
        $st->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $st->execute();
        $fila = $st->fetch();
        return is_array($fila) ? $fila : null;
    }

    private function buscarPendienteAutomatico(int $usuarioId): ?array
    {
        $sql = <<<SQL
        SELECT id
        FROM mantenimientos
        WHERE usuario_id = :usuario_id
          AND estado = 'pendiente'
          AND observaciones LIKE 'Generado automatico despues de marcar Hecho.%'
        ORDER BY created_at DESC, id DESC
        LIMIT 1
        SQL;
        $st = $this->conexion->prepare($sql);
        $st->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $st->execute();
        $fila = $st->fetch();
        return is_array($fila) ? $fila : null;
    }

    private function asegurarEstructuraTecnicos(): void
    {
        $sqlTecnicos = <<<SQL
        CREATE TABLE IF NOT EXISTS tecnicos (
          id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          nombre VARCHAR(150) NOT NULL,
          estado TINYINT(1) NOT NULL DEFAULT 1,
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          UNIQUE KEY uq_tecnicos_nombre (nombre),
          KEY idx_tecnicos_estado (estado)
        ) ENGINE=InnoDB
        SQL;
        $this->conexion->exec($sqlTecnicos);

        $stColumna = $this->conexion->query("SHOW COLUMNS FROM mantenimientos LIKE 'tecnico_responsable'");
        $columna = $stColumna->fetch();
        if ($columna === false) {
            $this->conexion->exec("ALTER TABLE mantenimientos ADD COLUMN tecnico_responsable VARCHAR(150) NULL AFTER fecha_ejecucion");
        }
    }

    private function construirObservacionRealizado(string $observaciones): ?string
    {
        $observacionLimpia = trim($observaciones);
        if ($observacionLimpia === '') {
            return 'Hecho desde modulo Programados.';
        }

        return $observacionLimpia . ' | Hecho desde modulo Programados.';
    }

    private function limpiarObservacionesSistema(string $observaciones): string
    {
        $texto = trim($observaciones);
        if ($texto === '') {
            return '';
        }

        $marcas = [
            '| Programado desde modulo Proximos.',
            '| Hecho desde modulo Programados.',
            '| Revertido desde modulo Reversiones (HECHO).',
            '| Revertido desde modulo Reversiones (PROGRAMAR).',
            '| Cancelado automaticamente por normalizacion de pendientes duplicados.',
            'Programado desde modulo Proximos.',
            'Hecho desde modulo Programados.',
            'Revertido desde modulo Reversiones (HECHO).',
            'Revertido desde modulo Reversiones (PROGRAMAR).',
            'Cancelado automaticamente por normalizacion de pendientes duplicados.',
            'Generado automatico despues de marcar Hecho.',
        ];

        $texto = str_replace($marcas, '', $texto);
        $texto = preg_replace('/\s*\|\s*\|+/', ' | ', $texto) ?? $texto;
        $texto = trim($texto, " |\t\n\r\0\x0B");

        return $texto;
    }
}
