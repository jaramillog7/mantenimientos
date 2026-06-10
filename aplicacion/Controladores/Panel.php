<?php

declare(strict_types=1);

namespace Aplicacion\Controladores;

use Aplicacion\Repositorios\RepositorioMantenimientos;
use Aplicacion\Repositorios\RepositorioUsuarios;
use Aplicacion\Servicios\ServicioCalculadorMantenimiento;
use Nucleo\Autenticacion;
use Nucleo\ConexionBaseDatos;
use Nucleo\Csrf;
use Nucleo\RespuestaHttp;
use Throwable;

final class ControladorPanel
{
    private const APARTADOS_VALIDOS = [
        'reportes',
        'proximos',
        'programados',
        'realizados',
        'lista_usuarios',
        'configuracion',
    ];

    public function index(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }

        $estadoConexion = 'Pendiente de validar.';
        $apartado = isset($_GET['apartado']) ? (string) $_GET['apartado'] : 'proximos';
        if ($apartado === 'calculadora' || $apartado === 'resumen' || $apartado === 'notificaciones') {
            $apartado = 'reportes';
        }
        if ($apartado === 'reversiones') {
            $apartado = 'configuracion';
        }
        if (!in_array($apartado, self::APARTADOS_VALIDOS, true)) {
            $apartado = 'proximos';
        }

        $codigoActivoConsulta = isset($_GET['codigo']) ? (int) $_GET['codigo'] : 845;
        $areaIdFiltro = isset($_GET['area_id']) ? (int) $_GET['area_id'] : 0;
        $busquedaUsuarios = isset($_GET['q_usuarios']) ? trim((string) $_GET['q_usuarios']) : '';
        $editarUsuarioId = isset($_GET['editar_usuario_id']) ? (int) $_GET['editar_usuario_id'] : 0;
        $busquedaProximos = isset($_GET['q_proximos']) ? trim((string) $_GET['q_proximos']) : '';
        $busquedaRealizados = isset($_GET['q_realizados']) ? trim((string) $_GET['q_realizados']) : '';

        $paginaProximos = isset($_GET['proximos_pagina']) ? max(1, (int) $_GET['proximos_pagina']) : 1;
        $porPaginaProximos = isset($_GET['proximos_por_pagina']) ? (int) $_GET['proximos_por_pagina'] : 10;

        $paginaProgramados = isset($_GET['programados_pagina']) ? max(1, (int) $_GET['programados_pagina']) : 1;
        $porPaginaProgramados = isset($_GET['programados_por_pagina']) ? (int) $_GET['programados_por_pagina'] : 10;
        $paginaRealizados = isset($_GET['realizados_pagina']) ? max(1, (int) $_GET['realizados_pagina']) : 1;
        $porPaginaRealizados = isset($_GET['realizados_por_pagina']) ? (int) $_GET['realizados_por_pagina'] : 10;

        $opcionesPorPagina = [10, 25, 50];
        if (!in_array($porPaginaProximos, $opcionesPorPagina, true)) {
            $porPaginaProximos = 10;
        }
        if (!in_array($porPaginaProgramados, $opcionesPorPagina, true)) {
            $porPaginaProgramados = 10;
        }
        if (!in_array($porPaginaRealizados, $opcionesPorPagina, true)) {
            $porPaginaRealizados = 10;
        }

        $resultadoCalculo = null;
        $listaUsuarios = [];
        $listaAreas = [];
        $usuarioEdicion = null;
        $proximos = [];
        $programados = [];
        $totalProximos = 0;
        $totalPaginasProximos = 1;
        $totalProgramados = 0;
        $totalPaginasProgramados = 1;
        $realizados = [];
        $totalRealizados = 0;
        $totalPaginasRealizados = 1;
        $actividadReciente = [];
        $tecnicos = [];
        $resumenSemestral = [
            'fase' => 1,
            'fase_nombre' => 'Primer semestre',
            'inicio' => '',
            'fin' => '',
            'total_usuarios' => 0,
            'total_base_fase' => 0,
            'total_no_exigibles' => 0,
            'total_realizados' => 0,
            'total_programados' => 0,
            'total_pendientes' => 0,
            'indice_cumplimiento' => 0,
        ];

        $resumen = [
            'total_usuarios' => 0,
            'total_mantenimientos' => 0,
            'total_pendientes' => 0,
            'total_programados' => 0,
            'total_realizados' => 0,
        ];

        try {
            $conexion = ConexionBaseDatos::obtenerConexion();
            $consulta = $conexion->query('SELECT DATABASE() AS base_datos_actual');
            $resultado = $consulta->fetch();
            $baseDatosActual = $resultado['base_datos_actual'] ?? 'desconocida';
            $estadoConexion = 'Conexion correcta a la base de datos: ' . $baseDatosActual . '.';

            $repoMantenimientos = new RepositorioMantenimientos();
            $repoUsuarios = new RepositorioUsuarios();
            $repoMantenimientos->normalizarPendientesDuplicados();
            $tecnicos = $repoMantenimientos->listarTecnicosActivos();

            $listaAreas = $repoUsuarios->listarAreasActivas();
            $listaUsuarios = $repoUsuarios->listarTodos(
                $apartado === 'lista_usuarios' ? $areaIdFiltro : null,
                $apartado === 'lista_usuarios' ? $busquedaUsuarios : ''
            );
            if ($apartado === 'lista_usuarios' && $editarUsuarioId > 0) {
                $usuarioEdicion = $repoUsuarios->obtenerPorId($editarUsuarioId);
            }

            $resumenConsulta = $conexion->query(
                "SELECT
                    (SELECT COUNT(*) FROM usuarios) AS total_usuarios,
                    (SELECT COUNT(*) FROM mantenimientos) AS total_mantenimientos,
                    (SELECT COUNT(DISTINCT usuario_id) FROM mantenimientos WHERE estado = 'pendiente') AS total_pendientes,
                    (SELECT COUNT(*) FROM mantenimientos WHERE estado = 'programado') AS total_programados,
                    (SELECT COUNT(*) FROM mantenimientos WHERE estado = 'realizado' AND observaciones LIKE '%Hecho desde modulo Programados.%') AS total_realizados"
            );
            $resumenResultado = $resumenConsulta->fetch();
            if (is_array($resumenResultado)) {
                $resumen = [
                    'total_usuarios' => (int) ($resumenResultado['total_usuarios'] ?? 0),
                    'total_mantenimientos' => (int) ($resumenResultado['total_mantenimientos'] ?? 0),
                    'total_pendientes' => (int) ($resumenResultado['total_pendientes'] ?? 0),
                    'total_programados' => (int) ($resumenResultado['total_programados'] ?? 0),
                    'total_realizados' => (int) ($resumenResultado['total_realizados'] ?? 0),
                ];
            }

            if ($apartado === 'proximos') {
                $totalProximos = $repoMantenimientos->contarPorEstado('pendiente', $busquedaProximos);
                $totalPaginasProximos = max(1, (int) ceil($totalProximos / $porPaginaProximos));
                if ($paginaProximos > $totalPaginasProximos) {
                    $paginaProximos = $totalPaginasProximos;
                }
                $offset = ($paginaProximos - 1) * $porPaginaProximos;
                $proximos = $repoMantenimientos->listarPorEstado('pendiente', $porPaginaProximos, $offset, $busquedaProximos);
            }

            if ($apartado === 'programados') {
                $totalProgramados = $repoMantenimientos->contarPorEstado('programado');
                $totalPaginasProgramados = max(1, (int) ceil($totalProgramados / $porPaginaProgramados));
                if ($paginaProgramados > $totalPaginasProgramados) {
                    $paginaProgramados = $totalPaginasProgramados;
                }
                $offset = ($paginaProgramados - 1) * $porPaginaProgramados;
                $programados = $repoMantenimientos->listarPorEstado('programado', $porPaginaProgramados, $offset);
            }

            if ($apartado === 'realizados') {
                $totalRealizados = $repoMantenimientos->contarPorEstado('realizado', $busquedaRealizados);
                $totalPaginasRealizados = max(1, (int) ceil($totalRealizados / $porPaginaRealizados));
                if ($paginaRealizados > $totalPaginasRealizados) {
                    $paginaRealizados = $totalPaginasRealizados;
                }
                $offset = ($paginaRealizados - 1) * $porPaginaRealizados;
                $realizados = $repoMantenimientos->listarPorEstado('realizado', $porPaginaRealizados, $offset, $busquedaRealizados);
            }

            if ($apartado === 'reportes') {
                $resumenSemestral = $repoMantenimientos->obtenerResumenSemestralActual();
                $actividadProgramados = $repoMantenimientos->listarPorEstado('programado', 3, 0);
                $actividadRealizados = $repoMantenimientos->listarPorEstado('realizado', 3, 0);

                foreach ($actividadProgramados as $fila) {
                    $actividadReciente[] = [
                        'tipo' => 'Programado',
                        'estado' => 'programado',
                        'nombre' => (string) ($fila['nombre'] ?? ''),
                        'area_nombre' => (string) ($fila['area_nombre'] ?? 'SIN AREA'),
                        'fecha' => (string) ($fila['proxima_fecha_programada'] ?? ''),
                    ];
                }

                foreach ($actividadRealizados as $fila) {
                    $actividadReciente[] = [
                        'tipo' => 'Realizado',
                        'estado' => 'realizado',
                        'nombre' => (string) ($fila['nombre'] ?? ''),
                        'area_nombre' => (string) ($fila['area_nombre'] ?? 'SIN AREA'),
                        'fecha' => (string) (($fila['fecha_ejecucion'] ?? '') ?: ($fila['proxima_fecha_programada'] ?? '')),
                    ];
                }

                usort($actividadReciente, static function (array $a, array $b): int {
                    return strcmp((string) ($b['fecha'] ?? ''), (string) ($a['fecha'] ?? ''));
                });
                $actividadReciente = array_slice($actividadReciente, 0, 5);
            }

            if ($apartado === 'proximos') {
                $servicioCalculador = new ServicioCalculadorMantenimiento($repoMantenimientos);
                try {
                    $resultadoCalculo = $servicioCalculador->calcularProximoPorCodigoActivo($codigoActivoConsulta);
                } catch (Throwable $errorCalculo) {
                    $resultadoCalculo = [
                        'usuario_id' => null,
                        'nombre' => null,
                        'codigo_activo' => $codigoActivoConsulta,
                        'ultima_fecha_programada' => null,
                        'proxima_fecha_programada' => null,
                        'fecha_recordatorio' => null,
                        'fecha_hoy' => (new \DateTimeImmutable('today'))->format('Y-m-d'),
                        'dias_para_mantenimiento' => null,
                        'estado_mantenimiento' => 'sin_datos',
                        'mensaje' => 'No se pudo calcular: verifica el codigo activo.',
                    ];
                }
            }
        } catch (Throwable $error) {
            $estadoConexion = 'No se pudo validar la conexion de base de datos. Revisa configuracion y datos.';
            $resultadoCalculo = [
                'usuario_id' => null,
                'nombre' => null,
                'codigo_activo' => $codigoActivoConsulta,
                'ultima_fecha_programada' => null,
                'proxima_fecha_programada' => null,
                'fecha_recordatorio' => null,
                'fecha_hoy' => (new \DateTimeImmutable('today'))->format('Y-m-d'),
                'dias_para_mantenimiento' => null,
                'estado_mantenimiento' => 'sin_datos',
                'mensaje' => 'No se pudo calcular el proximo mantenimiento.',
            ];
        }

        vista('PanelMantenimientos/orden_panel', [
            'titulo' => 'Panel de Mantenimientos TI',
            'estadoConexion' => $estadoConexion,
            'resultadoCalculo' => $resultadoCalculo,
            'codigoActivoConsulta' => $codigoActivoConsulta,
            'usuarioSesion' => Autenticacion::usuario(),
            'listaUsuarios' => $listaUsuarios,
            'listaAreas' => $listaAreas,
            'usuarioEdicion' => $usuarioEdicion,
            'areaIdFiltro' => $areaIdFiltro,
            'editarUsuarioId' => $editarUsuarioId,
            'busquedaUsuarios' => $busquedaUsuarios,
            'busquedaProximos' => $busquedaProximos,
            'busquedaRealizados' => $busquedaRealizados,
            'apartado' => $apartado,
            'resumen' => $resumen,
            'proximos' => $proximos,
            'programados' => $programados,
            'realizados' => $realizados,
            'paginaProximos' => $paginaProximos,
            'porPaginaProximos' => $porPaginaProximos,
            'totalPaginasProximos' => $totalPaginasProximos,
            'totalProximos' => $totalProximos,
            'paginaProgramados' => $paginaProgramados,
            'porPaginaProgramados' => $porPaginaProgramados,
            'totalPaginasProgramados' => $totalPaginasProgramados,
            'totalProgramados' => $totalProgramados,
            'paginaRealizados' => $paginaRealizados,
            'porPaginaRealizados' => $porPaginaRealizados,
            'totalPaginasRealizados' => $totalPaginasRealizados,
            'totalRealizados' => $totalRealizados,
            'actividadReciente' => $actividadReciente,
            'tecnicos' => $tecnicos,
            'resumenSemestral' => $resumenSemestral,
            'opcionesPorPagina' => $opcionesPorPagina,
            'flashOk' => flash_obtener('ok'),
            'flashError' => flash_obtener('error'),
        ]);
    }

    public function marcarProximoComoProgramado(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=proximos'));
        }

        $mantenimientoId = isset($_POST['mantenimiento_id']) ? (int) $_POST['mantenimiento_id'] : 0;
        $pagina = isset($_POST['proximos_pagina']) ? max(1, (int) $_POST['proximos_pagina']) : 1;
        $porPagina = isset($_POST['proximos_por_pagina']) ? (int) $_POST['proximos_por_pagina'] : 10;
        $qProximos = isset($_POST['q_proximos']) ? trim((string) $_POST['q_proximos']) : '';
        $fechaProgramada = isset($_POST['fecha_programada']) ? (string) $_POST['fecha_programada'] : '';

        if (!$this->esFechaLaboralValida($fechaProgramada)) {
            flash_guardar('error', 'Debes seleccionar una fecha valida de lunes a viernes para programar.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=proximos&proximos_pagina=' . $pagina . '&proximos_por_pagina=' . $porPagina . '&q_proximos=' . urlencode($qProximos)) . '#tabla-proximos');
        }

        $repo = new RepositorioMantenimientos();
        $repo->normalizarPendientesDuplicados();
        $ok = $mantenimientoId > 0 ? $repo->marcarComoProgramado($mantenimientoId, $fechaProgramada) : false;

        if ($ok) {
            flash_guardar('ok', 'Mantenimiento programado correctamente.');
        } else {
            flash_guardar('error', 'No fue posible programar el mantenimiento.');
        }

        RespuestaHttp::redirigir(urlRuta('/panel?apartado=proximos&proximos_pagina=' . $pagina . '&proximos_por_pagina=' . $porPagina . '&q_proximos=' . urlencode($qProximos)) . '#tabla-proximos');
    }

    public function marcarProgramadoComoHecho(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=programados'));
        }

        $mantenimientoId = isset($_POST['mantenimiento_id']) ? (int) $_POST['mantenimiento_id'] : 0;
        $pagina = isset($_POST['programados_pagina']) ? max(1, (int) $_POST['programados_pagina']) : 1;
        $porPagina = isset($_POST['programados_por_pagina']) ? (int) $_POST['programados_por_pagina'] : 10;
        $fechaEjecucion = trim((string) ($_POST['fecha_ejecucion'] ?? ''));
        $tecnicoId = isset($_POST['tecnico_id']) ? (int) $_POST['tecnico_id'] : 0;
        $observaciones = trim((string) ($_POST['observaciones'] ?? ''));

        if (!$this->esFechaValida($fechaEjecucion)) {
            flash_guardar('error', 'Debes seleccionar una fecha valida para marcar el mantenimiento como hecho.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=programados&programados_pagina=' . $pagina . '&programados_por_pagina=' . $porPagina) . '#tabla-programados');
        }

        $fechaEjecutada = \DateTimeImmutable::createFromFormat('!Y-m-d', $fechaEjecucion);
        $hoy = new \DateTimeImmutable('today');
        if ($fechaEjecutada === false || $fechaEjecutada > $hoy) {
            flash_guardar('error', 'La fecha de ejecucion no puede ser posterior a hoy.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=programados&programados_pagina=' . $pagina . '&programados_por_pagina=' . $porPagina) . '#tabla-programados');
        }

        $repo = new RepositorioMantenimientos();
        $tecnicoResponsable = $repo->obtenerNombreTecnicoPorId($tecnicoId);
        if ($tecnicoResponsable === null) {
            flash_guardar('error', 'Debes seleccionar un tecnico o responsable valido.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=programados&programados_pagina=' . $pagina . '&programados_por_pagina=' . $porPagina) . '#tabla-programados');
        }
        $repo->normalizarPendientesDuplicados();
        $ok = $mantenimientoId > 0
            ? $repo->marcarProgramadoComoHechoYGenerarSiguiente($mantenimientoId, $fechaEjecucion, $tecnicoResponsable, $observaciones)
            : false;

        if ($ok) {
            flash_guardar('ok', 'Mantenimiento marcado como hecho. Se registro la ejecucion en ' . $fechaEjecucion . ' y se genero el siguiente mantenimiento a 6 meses.');
        } else {
            flash_guardar('error', 'No fue posible marcar como hecho.');
        }

        RespuestaHttp::redirigir(urlRuta('/panel?apartado=programados&programados_pagina=' . $pagina . '&programados_por_pagina=' . $porPagina) . '#tabla-programados');
    }

    public function crearTecnico(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }

        $nombreTecnico = trim((string) ($_POST['nombre_tecnico'] ?? ''));
        $repo = new RepositorioMantenimientos();
        $resultado = $repo->registrarTecnico($nombreTecnico);

        flash_guardar(
            ($resultado['ok'] ?? false) ? 'ok' : 'error',
            (string) ($resultado['mensaje'] ?? 'No fue posible registrar el tecnico.')
        );

        RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
    }

    private function esFechaLaboralValida(string $fecha): bool
    {
        if (!$this->esFechaValida($fecha)) {
            return false;
        }

        $fechaObj = \DateTimeImmutable::createFromFormat('Y-m-d', $fecha);
        $diaSemana = (int) $fechaObj->format('N');
        return $diaSemana >= 1 && $diaSemana <= 5;
    }

    private function esFechaValida(string $fecha): bool
    {
        if ($fecha === '') {
            return false;
        }

        $fechaObj = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
        return $fechaObj !== false && $fechaObj->format('Y-m-d') === $fecha;
    }

    public function revertirUltimaAccion(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }

        $busquedaUsuario = isset($_POST['busqueda_usuario']) ? trim((string) $_POST['busqueda_usuario']) : '';
        $cantidadReversiones = isset($_POST['cantidad_reversiones']) ? (int) $_POST['cantidad_reversiones'] : 1;
        if ($cantidadReversiones < 1 || $cantidadReversiones > 10) {
            flash_guardar('error', 'La cantidad de reversiones debe estar entre 1 y 10.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }

        $repoUsuarios = new RepositorioUsuarios();
        $resolucion = $repoUsuarios->resolverCodigoActivoPorBusqueda($busquedaUsuario);
        if (($resolucion['ok'] ?? false) !== true) {
            flash_guardar('error', (string) ($resolucion['mensaje'] ?? 'No se pudo identificar el usuario.'));
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }
        $codigoActivo = (int) ($resolucion['codigo_activo'] ?? 0);
        if ($codigoActivo <= 0) {
            flash_guardar('error', 'No se encontro un codigo activo valido para el usuario.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }

        $repo = new RepositorioMantenimientos();
        $resultado = $repo->revertirAccionesPorCodigoActivo($codigoActivo, $cantidadReversiones);

        if (($resultado['ok'] ?? false) === true) {
            flash_guardar('ok', (string) ($resultado['mensaje'] ?? 'Reversion realizada.'));
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=proximos&q_proximos=' . urlencode((string) $codigoActivo) . '&proximos_pagina=1&proximos_por_pagina=10') . '#tabla-proximos');
        } else {
            flash_guardar('error', (string) ($resultado['mensaje'] ?? 'No fue posible revertir la accion.'));
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=configuracion'));
        }
    }

    public function actualizarUsuario(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=lista_usuarios'));
        }

        $usuarioId = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $codigoActivo = isset($_POST['codigo_activo']) ? (int) $_POST['codigo_activo'] : 0;
        $serialEquipo = trim((string) ($_POST['serial_equipo'] ?? ''));
        $areaId = isset($_POST['area_id']) ? (int) $_POST['area_id'] : 0;
        $filtroArea = isset($_POST['filtro_area_id']) ? (int) $_POST['filtro_area_id'] : 0;
        $busqueda = trim((string) ($_POST['filtro_q_usuarios'] ?? ''));

        if ($usuarioId <= 0 || $nombre === '' || $codigoActivo <= 0 || $serialEquipo === '') {
            flash_guardar('error', 'Completa todos los campos requeridos para editar el usuario.');
            RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, $usuarioId));
        }

        $repoUsuarios = new RepositorioUsuarios();
        $ok = $repoUsuarios->actualizarUsuario($usuarioId, $nombre, $codigoActivo, $serialEquipo, $areaId > 0 ? $areaId : null);
        flash_guardar($ok ? 'ok' : 'error', $ok ? 'Usuario actualizado correctamente.' : 'No se pudo actualizar el usuario.');

        RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, 0));
    }

    public function cambiarEstadoUsuario(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=lista_usuarios'));
        }

        $usuarioId = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
        $nuevoEstado = isset($_POST['nuevo_estado']) ? (int) $_POST['nuevo_estado'] : 0;
        $filtroArea = isset($_POST['filtro_area_id']) ? (int) $_POST['filtro_area_id'] : 0;
        $busqueda = trim((string) ($_POST['filtro_q_usuarios'] ?? ''));

        if ($usuarioId <= 0 || ($nuevoEstado !== 0 && $nuevoEstado !== 1)) {
            flash_guardar('error', 'Solicitud invalida para cambio de estado.');
            RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, 0));
        }

        $repoUsuarios = new RepositorioUsuarios();
        $ok = $repoUsuarios->cambiarEstadoUsuario($usuarioId, $nuevoEstado === 1);
        flash_guardar($ok ? 'ok' : 'error', $ok ? 'Estado de usuario actualizado.' : 'No se pudo cambiar el estado del usuario.');

        RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, 0));
    }

    public function eliminarUsuario(): void
    {
        if (!Autenticacion::estaAutenticado()) {
            RespuestaHttp::redirigir(urlRuta('/login'));
        }
        if (!Csrf::validar($_POST['_token'] ?? null)) {
            flash_guardar('error', 'Sesion expirada. Intenta nuevamente.');
            RespuestaHttp::redirigir(urlRuta('/panel?apartado=lista_usuarios'));
        }

        $usuarioId = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
        $filtroArea = isset($_POST['filtro_area_id']) ? (int) $_POST['filtro_area_id'] : 0;
        $busqueda = trim((string) ($_POST['filtro_q_usuarios'] ?? ''));

        if ($usuarioId <= 0) {
            flash_guardar('error', 'Solicitud invalida para eliminar usuario.');
            RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, 0));
        }

        $repoUsuarios = new RepositorioUsuarios();
        $resultado = $repoUsuarios->eliminarUsuarioSeguro($usuarioId);
        flash_guardar(($resultado['ok'] ?? false) ? 'ok' : 'error', (string) ($resultado['mensaje'] ?? 'No se pudo completar la operacion.'));

        RespuestaHttp::redirigir($this->urlListaUsuarios($filtroArea, $busqueda, 0));
    }

    private function urlListaUsuarios(int $areaId, string $busqueda, int $editarUsuarioId = 0): string
    {
        $url = '/panel?apartado=lista_usuarios&area_id=' . max(0, $areaId) . '&q_usuarios=' . urlencode($busqueda);
        if ($editarUsuarioId > 0) {
            $url .= '&editar_usuario_id=' . $editarUsuarioId;
        }

        return urlRuta($url);
    }
}
