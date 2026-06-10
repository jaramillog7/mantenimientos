<aside class="sidebar" data-sidebar-panel>
  <div class="sidebar-topbar">
    <button class="sidebar-toggle sidebar-toggle-desktop" type="button" data-sidebar-toggle aria-label="Colapsar panel lateral">
      <?= $iconosPanel('menu') ?>
    </button>
    <button class="sidebar-toggle sidebar-toggle-mobile" type="button" data-sidebar-close aria-label="Cerrar menu lateral">
      <?= $iconosPanel('close') ?>
    </button>
  </div>

  <div class="sidebar-marca">
    <div class="sidebar-marca-cabecera">
      <div class="logo">MT</div>
    </div>
    <div class="sidebar-marca-texto">
      <h2>Mantenimientos TI</h2>
    </div>
  </div>

  <nav class="menu">
    <p class="menu-titulo">Modulos</p>
    <?php foreach ($definicionModulosPanel as $claveModulo => $datosModulo) : ?>
      <?php
      $conteoModulo = '';
      if ($claveModulo === 'reportes') {
          $conteoModulo = (string) (int) ($resumen['total_mantenimientos'] ?? 0);
      } elseif ($claveModulo === 'lista_usuarios') {
          $conteoModulo = (string) (int) ($resumen['total_usuarios'] ?? 0);
      } elseif ($claveModulo === 'proximos') {
          $conteoModulo = (string) (int) ($resumen['total_pendientes'] ?? 0);
      } elseif ($claveModulo === 'programados') {
          $conteoModulo = (string) (int) ($resumen['total_programados'] ?? 0);
      } elseif ($claveModulo === 'realizados') {
          $conteoModulo = (string) (int) ($resumen['total_realizados'] ?? 0);
      }
      ?>
      <a
        class="menu-item <?= $apartadoActivo === $claveModulo ? 'activo' : '' ?>"
        href="<?= htmlspecialchars(urlRuta('/panel?apartado=' . $claveModulo), ENT_QUOTES, 'UTF-8') ?>"
      >
        <span class="menu-icono"><?= $iconosPanel((string) $datosModulo['icono']) ?></span>
        <span class="menu-texto"><strong><?= htmlspecialchars((string) ($datosModulo['menu_titulo'] ?? $datosModulo['titulo']), ENT_QUOTES, 'UTF-8') ?></strong></span>
        <?php if ($conteoModulo !== '') : ?>
          <span class="menu-conteo"><?= htmlspecialchars($conteoModulo, ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>
</aside>
