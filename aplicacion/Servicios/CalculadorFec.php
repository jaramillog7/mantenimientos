<?php

declare(strict_types=1);

namespace Aplicacion\Servicios;

use Aplicacion\Repositorios\RepositorioMantenimientos;
use DateInterval;
use DateTimeImmutable;
use RuntimeException;

final class ServicioCalculadorMantenimiento
{
    private RepositorioMantenimientos $repositorioMantenimientos;

    public function __construct(RepositorioMantenimientos $repositorioMantenimientos)
    {
        $this->repositorioMantenimientos = $repositorioMantenimientos;
    }

    public function calcularProximoPorCodigoActivo(int $codigoActivo): array
    {
        $registro = $this->repositorioMantenimientos->obtenerUltimoMantenimientoPorCodigoActivo($codigoActivo);
        if ($registro === null) {
            throw new RuntimeException('No existe un usuario con el codigo activo indicado.');
        }

        $ultimaFecha = $registro['ultima_fecha_programada'];
        if ($ultimaFecha === null) {
            return [
                'usuario_id' => $registro['usuario_id'],
                'nombre' => $registro['nombre'],
                'codigo_activo' => $registro['codigo_activo'],
                'ultima_fecha_programada' => null,
                'proxima_fecha_programada' => null,
                'fecha_recordatorio' => null,
                'mensaje' => 'El usuario no tiene mantenimientos cargados todavia.',
            ];
        }

        $fechaBase = DateTimeImmutable::createFromFormat('Y-m-d', $ultimaFecha);
        if ($fechaBase === false) {
            throw new RuntimeException('La fecha de mantenimiento almacenada no es valida.');
        }

        $proximaFecha = $fechaBase->add(new DateInterval('P6M'));
        $proximaFecha = $this->moverASiguienteDiaHabil($proximaFecha);
        $fechaRecordatorio = $proximaFecha->sub(new DateInterval('P7D'));
        $fechaHoy = new DateTimeImmutable('today');
        $diasParaMantenimiento = (int) $fechaHoy->diff($proximaFecha)->format('%r%a');

        $estadoMantenimiento = 'en_tiempo';
        if ($diasParaMantenimiento < 0) {
            $estadoMantenimiento = 'vencido';
        } elseif ($diasParaMantenimiento === 0) {
            $estadoMantenimiento = 'hoy';
        } elseif ($diasParaMantenimiento <= 7) {
            $estadoMantenimiento = 'proximo';
        }

        return [
            'usuario_id' => $registro['usuario_id'],
            'nombre' => $registro['nombre'],
            'codigo_activo' => $registro['codigo_activo'],
            'ultima_fecha_programada' => $fechaBase->format('Y-m-d'),
            'proxima_fecha_programada' => $proximaFecha->format('Y-m-d'),
            'fecha_recordatorio' => $fechaRecordatorio->format('Y-m-d'),
            'fecha_hoy' => $fechaHoy->format('Y-m-d'),
            'dias_para_mantenimiento' => $diasParaMantenimiento,
            'estado_mantenimiento' => $estadoMantenimiento,
            'mensaje' => 'Calculo realizado con regla de 6 meses exactos.',
        ];
    }

    private function moverASiguienteDiaHabil(DateTimeImmutable $fecha): DateTimeImmutable
    {
        $fechaAjustada = $fecha;
        while ((int) $fechaAjustada->format('N') >= 6) {
            $fechaAjustada = $fechaAjustada->add(new DateInterval('P1D'));
        }

        return $fechaAjustada;
    }
}
