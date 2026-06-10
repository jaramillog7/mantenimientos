<section class="hero-panel hero-panel-<?= htmlspecialchars((string) $datosApartado['acento'], ENT_QUOTES, 'UTF-8') ?>">
  <div class="hero-principal tarjeta">
    <div class="hero-etiquetas">
      <span class="hero-badge hero-badge-modulo">
        <span><?= htmlspecialchars((string) $datosApartado['etiqueta'], ENT_QUOTES, 'UTF-8') ?></span>
      </span>
      <span class="hero-badge hero-badge-bd">
        <span><?= htmlspecialchars((string) $estadoConexion, ENT_QUOTES, 'UTF-8') ?></span>
      </span>
    </div>

    <div class="hero-cuerpo">
      <div class="hero-texto">
        <h1><?= htmlspecialchars($datosApartado['titulo'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($datosApartado['descripcion'], ENT_QUOTES, 'UTF-8') ?></p>
      </div>
      <div class="hero-destacado">
        <span class="hero-destacado-etiqueta"><?= $apartadoActivo === 'reportes' ? 'Enfoque del reporte' : 'Estado del entorno' ?></span>
        <strong><?= htmlspecialchars((string) ucfirst((string) ($datosApartado['etiqueta'] ?? 'Operativo')), ENT_QUOTES, 'UTF-8') ?></strong>
        <small><?= $apartadoActivo === 'reportes'
          ? 'El conteo usa solo mantenimientos que ya deben cumplirse en el semestre actual.'
          : 'Vista priorizada para equipos, agenda y seguimiento continuo.' ?></small>
      </div>
    </div>
  </div>

  <div class="hero-metricas">
    <?php foreach ($metricasPanel as $metrica) : ?>
      <article class="metrica-premium metrica-premium-<?= htmlspecialchars((string) $metrica['tono'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="metrica-texto">
          <span><?= htmlspecialchars((string) $metrica['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
          <strong><?= htmlspecialchars((string) $metrica['valor'], ENT_QUOTES, 'UTF-8') ?></strong>
          <small><?= htmlspecialchars((string) $metrica['meta'], ENT_QUOTES, 'UTF-8') ?></small>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
