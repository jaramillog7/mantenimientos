<?php

declare(strict_types=1);

$definicionModulosPanel = [
    'reportes' => [
        'titulo' => 'Reportes',
        'menu_titulo' => 'Dashboard',
        'descripcion' => 'Resumen del semestre actual con mantenimientos exigibles, completados, programados y pendientes reales.',
        'icono' => 'spark',
        'etiqueta' => 'Indicadores',
        'acento' => 'oscuro',
    ],
    'lista_usuarios' => [
        'titulo' => 'Lista de Usuarios',
        'menu_titulo' => 'Usuarios',
        'descripcion' => 'Catalogo completo de usuarios, equipos y datos operativos para gestion TI.',
        'icono' => 'usuarios',
        'etiqueta' => 'Base maestra',
        'acento' => 'azul',
    ],
    'proximos' => [
        'titulo' => 'Proximos',
        'menu_titulo' => 'Proximos',
        'descripcion' => 'Planeacion de mantenimientos pendientes con prioridad operativa.',
        'icono' => 'reloj',
        'etiqueta' => 'Planeacion',
        'acento' => 'cobalto',
    ],
    'programados' => [
        'titulo' => 'Programados',
        'menu_titulo' => 'Programados',
        'descripcion' => 'Mantenimientos confirmados listos para ejecucion y seguimiento.',
        'icono' => 'calendario',
        'etiqueta' => 'Agenda activa',
        'acento' => 'verde',
    ],
    'realizados' => [
        'titulo' => 'Realizados',
        'menu_titulo' => 'Realizados',
        'descripcion' => 'Historial validado de mantenimientos ejecutados desde el flujo del sistema.',
        'icono' => 'check',
        'etiqueta' => 'Historial',
        'acento' => 'oscuro',
    ],
    'configuracion' => [
        'titulo' => 'Configuracion',
        'menu_titulo' => 'Configuracion',
        'descripcion' => 'Herramientas operativas y controles de soporte administrativo.',
        'icono' => 'ajustes',
        'etiqueta' => 'Control',
        'acento' => 'vino',
    ],
];

$iconosPanel = static function (string $icono): string {
    $iconos = [
        'usuarios' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'reloj' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/></svg>',
        'calendario' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="m8.5 12.5 2.3 2.3 4.8-5.3"/></svg>',
        'ajustes' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8.8A3.2 3.2 0 1 0 12 15.2 3.2 3.2 0 0 0 12 8.8Z"/><path d="M19.4 15a1 1 0 0 0 .2 1.1l.1.1a1 1 0 0 1 0 1.4l-1.2 1.2a1 1 0 0 1-1.4 0l-.1-.1a1 1 0 0 0-1.1-.2 1 1 0 0 0-.6.9V20a1 1 0 0 1-1 1h-1.8a1 1 0 0 1-1-1v-.2a1 1 0 0 0-.7-.9 1 1 0 0 0-1 .2l-.1.1a1 1 0 0 1-1.4 0l-1.2-1.2a1 1 0 0 1 0-1.4l.1-.1a1 1 0 0 0 .2-1.1 1 1 0 0 0-.9-.6H4a1 1 0 0 1-1-1v-1.8a1 1 0 0 1 1-1h.2a1 1 0 0 0 .9-.7 1 1 0 0 0-.2-1l-.1-.1a1 1 0 0 1 0-1.4l1.2-1.2a1 1 0 0 1 1.4 0l.1.1a1 1 0 0 0 1 .2 1 1 0 0 0 .7-.9V4a1 1 0 0 1 1-1h1.8a1 1 0 0 1 1 1v.2a1 1 0 0 0 .6.9 1 1 0 0 0 1.1-.2l.1-.1a1 1 0 0 1 1.4 0l1.2 1.2a1 1 0 0 1 0 1.4l-.1.1a1 1 0 0 0-.2 1.1 1 1 0 0 0 .9.7H20a1 1 0 0 1 1 1v1.8a1 1 0 0 1-1 1h-.2a1 1 0 0 0-.9.6Z"/></svg>',
        'panel' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="7" height="7" rx="2"/><rect x="14" y="4" width="7" height="4" rx="2"/><rect x="14" y="11" width="7" height="9" rx="2"/><rect x="3" y="14" width="7" height="6" rx="2"/></svg>',
        'bd' => '<svg viewBox="0 0 24 24" aria-hidden="true"><ellipse cx="12" cy="5" rx="7" ry="3"/><path d="M5 5v6c0 1.7 3.1 3 7 3s7-1.3 7-3V5"/><path d="M5 11v6c0 1.7 3.1 3 7 3s7-1.3 7-3v-6"/></svg>',
        'menu' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
        'close' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>',
        'spark' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 1.9 4.8L19 9.7l-4.1 2.1L13 17l-1.9-5.2L7 9.7l5.1-1.9L12 3Z"/></svg>',
    ];

    return $iconos[$icono] ?? $iconos['panel'];
};

$apartadoActivo = $apartado ?? 'proximos';
$datosApartado = $definicionModulosPanel[$apartadoActivo] ?? $definicionModulosPanel['proximos'];
$fechaHoyPanel = new DateTimeImmutable('now');
$fechaHoyLegible = $fechaHoyPanel->format('d M Y');
$etiquetasMes = [
    'Jan' => 'Ene',
    'Feb' => 'Feb',
    'Mar' => 'Mar',
    'Apr' => 'Abr',
    'May' => 'May',
    'Jun' => 'Jun',
    'Jul' => 'Jul',
    'Aug' => 'Ago',
    'Sep' => 'Sep',
    'Oct' => 'Oct',
    'Nov' => 'Nov',
    'Dec' => 'Dic',
];
$fechaHoyLegible = strtr($fechaHoyLegible, $etiquetasMes);

$formatearFecha = static function (?string $fecha): string {
    if ($fecha === null) {
        return '';
    }
    $fechaLimpia = trim($fecha);
    if ($fechaLimpia === '') {
        return '';
    }
    $objetoFecha = DateTimeImmutable::createFromFormat('Y-m-d', $fechaLimpia);
    if ($objetoFecha !== false && $objetoFecha->format('Y-m-d') === $fechaLimpia) {
        return $objetoFecha->format('d/m/Y');
    }
    return $fechaLimpia;
};

$metricasPanel = $apartadoActivo === 'reportes'
    ? [
        [
            'titulo' => 'Faltan',
            'valor' => (string) (int) ($resumenSemestral['total_pendientes'] ?? 0),
            'meta' => 'Pendientes por ejecutar en la fase',
            'icono' => 'reloj',
            'tono' => 'ambar',
        ],
        [
            'titulo' => 'Programados',
            'valor' => (string) (int) ($resumenSemestral['total_programados'] ?? 0),
            'meta' => 'Ya tienen fecha en esta fase',
            'icono' => 'calendario',
            'tono' => 'cobalto',
        ],
        [
            'titulo' => 'Completados',
            'valor' => (string) (int) ($resumenSemestral['total_realizados'] ?? 0),
            'meta' => 'Ya se ejecutaron en esta fase',
            'icono' => 'check',
            'tono' => 'verde',
        ],
        [
            'titulo' => 'Mantenimientos',
            'valor' => (string) (int) ($resumenSemestral['total_base_fase'] ?? 0),
            'meta' => 'Obligatorios en la fase actual',
            'icono' => 'usuarios',
            'tono' => 'azul',
        ],
    ]
    : [
        [
            'titulo' => 'Pendientes',
            'valor' => (string) (int) ($resumen['total_pendientes'] ?? 0),
            'meta' => 'Por programar en agenda',
            'icono' => 'reloj',
            'tono' => 'ambar',
        ],
        [
            'titulo' => 'Programados',
            'valor' => (string) (int) ($resumen['total_programados'] ?? 0),
            'meta' => 'Listos para ejecucion',
            'icono' => 'calendario',
            'tono' => 'cobalto',
        ],
        [
            'titulo' => 'Realizados',
            'valor' => (string) (int) ($resumen['total_realizados'] ?? 0),
            'meta' => 'Cierres confirmados',
            'icono' => 'check',
            'tono' => 'verde',
        ],
        [
            'titulo' => 'Usuarios',
            'valor' => (string) (int) ($resumen['total_usuarios'] ?? 0),
            'meta' => 'Base operativa registrada',
            'icono' => 'usuarios',
            'tono' => 'azul',
        ],
    ];

$mapaArchivosModulo = [
    'reportes' => __DIR__ . '/Modulos/modulo_reportes.php',
    'proximos' => __DIR__ . '/Modulos/modulo_proximos.php',
    'programados' => __DIR__ . '/Modulos/modulo_programados.php',
    'realizados' => __DIR__ . '/Modulos/modulo_realizados.php',
    'lista_usuarios' => __DIR__ . '/Modulos/modulo_lista_usuarios.php',
    'configuracion' => __DIR__ . '/Modulos/modulo_configuracion.php',
];

$archivoModuloActivo = $mapaArchivosModulo[$apartadoActivo] ?? $mapaArchivosModulo['proximos'];
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo ?? 'Panel', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= htmlspecialchars(urlRecurso('publico/assets/css/panel.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
  <div class="panel-fondo-orb panel-fondo-orb-a" aria-hidden="true"></div>
  <div class="panel-fondo-orb panel-fondo-orb-b" aria-hidden="true"></div>
  <button class="panel-overlay" type="button" data-panel-overlay aria-label="Cerrar menu lateral"></button>

  <div class="layout" data-layout-panel>
    <?php require __DIR__ . '/Componentes/sidebar.php'; ?>

    <main class="contenido">
      <?php require __DIR__ . '/Componentes/Alertas.php'; ?>

      <section class="modulo-contenido">
        <?php if ($apartadoActivo === 'reportes') : ?>
          <?php require __DIR__ . '/Componentes/tarjeta.php'; ?>
        <?php endif; ?>
        <?php require $archivoModuloActivo; ?>
      </section>
    </main>
  </div>

  <script src="<?= htmlspecialchars(urlRecurso('publico/assets/js/panel.js'), ENT_QUOTES, 'UTF-8') ?>" defer></script>
</body>
</html>
