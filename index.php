<?php

declare(strict_types=1);

use Nucleo\Enrutador;

define('RUTA_BASE', __DIR__);

require RUTA_BASE . '/arranque/inicializador.php';

$enrutador = new Enrutador();

require RUTA_BASE . '/rutas/rutas_app.php';

$metodoHttp = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uriSolicitud = $_SERVER['REQUEST_URI'] ?? '/';
$rutaSolicitud = (string) parse_url($uriSolicitud, PHP_URL_PATH);

// Soporta proyectos en subcarpetas como /Mantenimientos sin romper rutas internas.
$prefijoProyecto = rtrim((string) dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
if ($prefijoProyecto !== '' && $prefijoProyecto !== '/' && str_starts_with($rutaSolicitud, $prefijoProyecto)) {
    $rutaSolicitud = substr($rutaSolicitud, strlen($prefijoProyecto));
}

$rutaSolicitud = $rutaSolicitud === '' ? '/' : $rutaSolicitud;

$enrutador->despachar($metodoHttp, $rutaSolicitud);
