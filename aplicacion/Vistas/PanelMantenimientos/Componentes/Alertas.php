<?php if (!empty($flashOk)) : ?>
  <section class="alerta alerta-ok" role="status">
    <span class="alerta-icono"><?= $iconosPanel('check') ?></span>
    <div class="alerta-contenido">
      <strong>Operacion completada</strong>
      <span><?= htmlspecialchars((string) $flashOk, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </section>
<?php endif; ?>

<?php if (!empty($flashError)) : ?>
  <section class="alerta alerta-error" role="alert">
    <span class="alerta-icono"><?= $iconosPanel('close') ?></span>
    <div class="alerta-contenido">
      <strong>Revision requerida</strong>
      <span><?= htmlspecialchars((string) $flashError, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
  </section>
<?php endif; ?>
