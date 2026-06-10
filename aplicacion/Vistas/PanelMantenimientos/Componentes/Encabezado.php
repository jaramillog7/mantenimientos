<header class="encabezado" data-encabezado-sticky>
  <div class="encabezado-izquierda">
    <button class="boton-toggle-movil" type="button" data-sidebar-open aria-label="Abrir menu lateral">
      <?= $iconosPanel('menu') ?>
    </button>

    <div class="encabezado-breadcrumb">
      <span class="breadcrumb-home">
        <span class="breadcrumb-icono"><?= $iconosPanel('panel') ?></span>
        <span>Panel</span>
      </span>
      <span class="breadcrumb-separador">/</span>
      <span class="breadcrumb-actual"><?= htmlspecialchars($datosApartado['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>

    <div class="encabezado-titular">
      <p class="etiqueta">Sesion activa</p>
      <p class="usuario-encabezado">
        <?= htmlspecialchars((string) ($usuarioSesion['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        <span><?= htmlspecialchars((string) ($usuarioSesion['rol'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
      </p>
    </div>
  </div>

  <div class="acciones-encabezado">
    <div class="chip-fecha">
      <span class="chip-fecha-icono"><?= $iconosPanel('calendario') ?></span>
      <span><?= htmlspecialchars($fechaHoyLegible, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <span class="badge"><?= htmlspecialchars((string) ($usuarioSesion['correo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
    <a class="boton-salir" href="<?= htmlspecialchars(urlRuta('/logout'), ENT_QUOTES, 'UTF-8') ?>">Cerrar sesion</a>
  </div>
</header>
