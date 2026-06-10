<section class="modulo-bloque tarjeta tarjeta-tabla-premium">
  <div class="cabecera-modulo">
    <div>
      <h3>Configuracion</h3>
      <p class="cabecera-modulo-texto">Controles de recuperacion, trazabilidad operativa y gestion segura de la sesion.</p>
    </div>
    <div class="cabecera-modulo-aside">
    </div>
  </div>

  <section class="tarjeta tarjeta-subseccion tarjeta-configuracion-bloque">
    <div class="cabecera-subpanel">
      <div>
        <span class="estado-tabla estado-tabla-alerta">Recuperacion</span>
        <h4>Revertir ultimas acciones por usuario</h4>
      </div>
    </div>

    <p class="texto-ayuda">
      Esta herramienta revierte unicamente acciones hechas desde el panel.
    </p>

    <form
      method="post"
      action="<?= htmlspecialchars(urlRuta('/panel/reversiones/revertir'), ENT_QUOTES, 'UTF-8') ?>"
      class="filtros-premium"
      onsubmit="return confirm('Estas seguro de revertir las ultimas acciones de este usuario?');"
    >
      <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

      <label class="campo-filtro">
        <span>Usuario (codigo activo o nombre)</span>
        <input id="busqueda_usuario_reversion" name="busqueda_usuario" type="text" placeholder="Buscar por codigo o nombre" required>
      </label>

      <label class="campo-filtro">
        <span>Cantidad de reversiones</span>
        <input id="cantidad_reversiones" name="cantidad_reversiones" type="number" min="1" max="10" value="1" required>
      </label>

      <div class="grupo-botones-filtro">
        <button type="submit" class="boton-accion boton-revertir">Revertir acciones</button>
      </div>
    </form>
  </section>

  <section class="tarjeta tarjeta-subseccion tarjeta-configuracion-bloque">
    <div class="cabecera-subpanel">
      <div>
        <span class="estado-tabla estado-tabla-info">Tecnicos</span>
        <h4>Tecnicos y responsables</h4>
      </div>
    </div>

    <p class="texto-ayuda">
      Registra aqui los nombres que luego podras seleccionar al marcar un mantenimiento como realizado.
    </p>

    <form
      method="post"
      action="<?= htmlspecialchars(urlRuta('/panel/tecnicos/crear'), ENT_QUOTES, 'UTF-8') ?>"
      class="filtros-premium"
    >
      <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

      <label class="campo-filtro">
        <span>Nombre del tecnico o responsable</span>
        <input id="nombre_tecnico" name="nombre_tecnico" type="text" placeholder="Ejemplo: Juan Perez" required>
      </label>

      <div class="grupo-botones-filtro">
        <button type="submit" class="boton-accion">Agregar tecnico</button>
      </div>
    </form>

    <div class="configuracion-resumen-auditoria">
      <?php if (!empty($tecnicos)) : ?>
        <?php foreach ($tecnicos as $tecnico) : ?>
          <article class="configuracion-mini-card">
            <span>Tecnico disponible</span>
            <strong><?= htmlspecialchars((string) ($tecnico['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong>
          </article>
        <?php endforeach; ?>
      <?php else : ?>
        <article class="configuracion-mini-card">
          <span>Catalogo</span>
          <strong>No hay tecnicos registrados</strong>
        </article>
      <?php endif; ?>
    </div>
  </section>

  <section class="tarjeta tarjeta-subseccion tarjeta-configuracion-bloque">
    <div class="cabecera-subpanel">
      <div>
        <span class="estado-tabla estado-tabla-info">Auditoria del sistema</span>
        <h4>Monitoreo operativo</h4>
      </div>
    </div>

    <p class="texto-ayuda">
      Usa este panel para vigilar el volumen general de mantenimientos, la carga pendiente y el estado de ejecucion desde el dashboard de reportes.
    </p>

    <div class="configuracion-resumen-auditoria">
      <article class="configuracion-mini-card">
        <span>Total mantenimientos</span>
        <strong><?= (int) ($resumen['total_mantenimientos'] ?? 0) ?></strong>
      </article>
      <article class="configuracion-mini-card">
        <span>Pendientes</span>
        <strong><?= (int) ($resumen['total_pendientes'] ?? 0) ?></strong>
      </article>
      <article class="configuracion-mini-card">
        <span>Programados</span>
        <strong><?= (int) ($resumen['total_programados'] ?? 0) ?></strong>
      </article>
      <article class="configuracion-mini-card">
        <span>Realizados</span>
        <strong><?= (int) ($resumen['total_realizados'] ?? 0) ?></strong>
      </article>
    </div>
  </section>

  <section class="tarjeta tarjeta-subseccion tarjeta-configuracion-bloque">
    <div class="cabecera-subpanel">
      <div>
        <span class="estado-tabla estado-tabla-info">Sesion</span>
        <h4>Sesion</h4>
      </div>
    </div>

    <p class="texto-ayuda">
      Finaliza tu sesion actual de forma segura.
    </p>

    <div class="grupo-botones-filtro">
      <a class="boton-limpiar" href="<?= htmlspecialchars(urlRuta('/logout'), ENT_QUOTES, 'UTF-8') ?>">Cerrar sesion</a>
    </div>
  </section>
</section>
