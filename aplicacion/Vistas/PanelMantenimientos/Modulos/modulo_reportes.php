<?php
$totalMantenimientos = (int) ($resumenSemestral['total_usuarios'] ?? 0);
$totalBaseFase = (int) ($resumenSemestral['total_base_fase'] ?? 0);
$totalNoExigibles = (int) ($resumenSemestral['total_no_exigibles'] ?? 0);
$totalPendientes = (int) ($resumenSemestral['total_pendientes'] ?? 0);
$totalProgramados = (int) ($resumenSemestral['total_programados'] ?? 0);
$totalRealizados = (int) ($resumenSemestral['total_realizados'] ?? 0);
$faseNombre = (string) ($resumenSemestral['fase_nombre'] ?? 'Semestre actual');
$fechaInicioFase = $formatearFecha((string) ($resumenSemestral['inicio'] ?? ''));
$fechaFinFase = $formatearFecha((string) ($resumenSemestral['fin'] ?? ''));
$basePorcentaje = max(1, $totalBaseFase);
$porcentajePendientes = (int) round(($totalPendientes / $basePorcentaje) * 100);
$porcentajeProgramados = (int) round(($totalProgramados / $basePorcentaje) * 100);
$porcentajeRealizados = (int) round(($totalRealizados / $basePorcentaje) * 100);
$indiceCumplimiento = (int) ($resumenSemestral['indice_cumplimiento'] ?? 0);
?>

<section class="modulo-bloque tarjeta tarjeta-reportes-premium" id="modulo-reportes">
  <div class="cabecera-modulo">
    <div>
      <p class="cabecera-modulo-eyebrow">Indicadores operativos</p>
      <h3>Reportes de mantenimientos</h3>
      <p class="cabecera-modulo-texto">Control del <?= htmlspecialchars($faseNombre, ENT_QUOTES, 'UTF-8') ?> desde <?= htmlspecialchars($fechaInicioFase, ENT_QUOTES, 'UTF-8') ?> hasta <?= htmlspecialchars($fechaFinFase, ENT_QUOTES, 'UTF-8') ?>. Aqui se cuentan solo los mantenimientos que ya deben cumplirse en esta fase.</p>
    </div>
    <div class="cabecera-modulo-aside">
      <span class="dato-superior">Mantenimientos de la fase</span>
      <strong><?= $totalBaseFase ?></strong>
      <small><?php if ($totalNoExigibles > 0) : ?><?= $totalNoExigibles ?> equipos aun no exigibles | <?php endif; ?><?= $totalMantenimientos ?> usuarios activos en total</small>
    </div>
  </div>

  <div class="reportes-panel-grid">
    <section class="tarjeta-subseccion reporte-distribucion">
      <div class="cabecera-subpanel">
        <div>
          <h4>Distribucion del estado</h4>
          <p class="texto-ayuda">De los <?= $totalBaseFase ?> mantenimientos exigibles en esta fase, <?= $totalRealizados ?> ya se cerraron, <?= $totalProgramados ?> siguen agendados y <?= $totalPendientes ?> aun faltan por ejecutar.</p>
        </div>
      </div>

      <div class="reporte-barra">
        <span class="reporte-barra-segmento reporte-barra-pendientes" style="width: <?= $porcentajePendientes ?>%"></span>
        <span class="reporte-barra-segmento reporte-barra-programados" style="width: <?= $porcentajeProgramados ?>%"></span>
        <span class="reporte-barra-segmento reporte-barra-realizados" style="width: <?= $porcentajeRealizados ?>%"></span>
      </div>

      <div class="reporte-leyenda">
        <div class="reporte-leyenda-item">
          <span class="reporte-punto reporte-punto-pendientes"></span>
          <strong>Faltan</strong>
          <span><?= $totalPendientes ?> por ejecutar</span>
        </div>
        <div class="reporte-leyenda-item">
          <span class="reporte-punto reporte-punto-programados"></span>
          <strong>Programados</strong>
          <span><?= $totalProgramados ?> con fecha</span>
        </div>
        <div class="reporte-leyenda-item">
          <span class="reporte-punto reporte-punto-realizados"></span>
          <strong>Completados</strong>
          <span><?= $totalRealizados ?> cerrados</span>
        </div>
      </div>
    </section>

    <section class="tarjeta-subseccion reporte-actividad">
      <div class="cabecera-subpanel">
        <div>
          <h4>Actividad reciente</h4>
          <p class="texto-ayuda">Ultimos movimientos registrados en agenda y ejecucion dentro del flujo de mantenimientos.</p>
        </div>
      </div>

      <?php if (!empty($actividadReciente)) : ?>
        <div class="actividad-reciente-lista">
          <?php foreach ($actividadReciente as $item) : ?>
            <?php $tipoActividad = (string) ($item['tipo'] ?? ''); ?>
            <article class="actividad-item">
              <div class="actividad-item-texto">
                <strong><?= htmlspecialchars((string) ($item['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
                <div class="actividad-item-meta">
                  <span class="actividad-item-area"><?= htmlspecialchars((string) ($item['area_nombre'] ?? 'SIN AREA'), ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="actividad-item-tipo actividad-item-tipo-<?= strtolower($tipoActividad) === 'realizado' ? 'realizado' : 'programado' ?>">
                    <?= htmlspecialchars($tipoActividad, ENT_QUOTES, 'UTF-8') ?>
                  </span>
                </div>
              </div>
              <div class="actividad-item-fecha">
                <span class="actividad-item-fecha-etiqueta">Fecha</span>
                <strong><?= htmlspecialchars($formatearFecha((string) ($item['fecha'] ?? '')), ENT_QUOTES, 'UTF-8') ?></strong>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="estado-vacio estado-vacio-reportes">
          <div>
            <strong>No hay actividad reciente para mostrar</strong>
            <p>La estructura queda preparada y empezara a llenarse automaticamente cuando existan movimientos en programados o realizados.</p>
          </div>
        </div>
      <?php endif; ?>
    </section>
  </div>
</section>
