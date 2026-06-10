<section class="modulo-bloque tarjeta tarjeta-tabla-premium" id="tabla-realizados">
  <div class="cabecera-modulo">
    <div>
      <h3>Mantenimientos realizados</h3>
      <p class="cabecera-modulo-texto">Consulta los mantenimientos realizados.</p>
    </div>
    <div class="cabecera-modulo-aside">
      <span class="dato-superior">Total registros</span>
      <strong><?= (int) ($totalRealizados ?? 0) ?></strong>
    </div>
  </div>

  <form method="get" action="<?= htmlspecialchars(urlRuta('/panel'), ENT_QUOTES, 'UTF-8') ?>" class="filtros-premium">
    <input type="hidden" name="apartado" value="realizados">
    <input type="hidden" name="realizados_pagina" value="1">

    <label class="campo-filtro">
      <span>Buscar usuario o activo</span>
      <input id="q_realizados" name="q_realizados" type="text" value="<?= htmlspecialchars((string) ($busquedaRealizados ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por codigo o nombre">
    </label>

    <label class="campo-filtro campo-filtro-select">
      <span>Registros por pagina</span>
      <select id="realizados_por_pagina" name="realizados_por_pagina">
        <?php foreach (($opcionesPorPagina ?? [10, 25, 50]) as $opcion) : ?>
          <option value="<?= (int) $opcion ?>" <?= (int) ($porPaginaRealizados ?? 10) === (int) $opcion ? 'selected' : '' ?>>
            <?= (int) $opcion ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="grupo-botones-filtro">
      <button type="submit">Aplicar filtro</button>
      <a class="boton-limpiar" href="<?= htmlspecialchars(urlRuta('/panel?apartado=realizados'), ENT_QUOTES, 'UTF-8') ?>">Restablecer</a>
    </div>
  </form>

  <div class="tabla-shell">
    <div class="tabla-shell-encabezado">
    </div>

    <div class="tabla-contenedor">
      <table class="tabla-usuarios tabla-premium" data-sort-table>
        <thead>
          <tr>
            <th>Area / Equipo</th>
            <th>Usuario</th>
            <th>Activo</th>
            <th>Fecha programada</th>
            <th>Fecha ejecucion</th>
            <th>Tecnico / Responsable</th>
            <th>Observaciones</th>
            <th>Evidencia</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($realizados ?? []) as $fila) : ?>
            <tr>
              <td data-label="Area / Equipo"><span class="badge-area"><?= htmlspecialchars((string) ($fila['area_nombre'] ?? 'SIN AREA'), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Usuario"><strong class="texto-principal-tabla"><?= htmlspecialchars((string) ($fila['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td data-label="Activo"><span class="codigo-activo"><?= htmlspecialchars((string) ($fila['codigo_activo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Fecha programada"><?= htmlspecialchars($formatearFecha((string) ($fila['proxima_fecha_programada'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
              <td data-label="Fecha ejecucion"><span class="fecha-destacada"><?= htmlspecialchars($formatearFecha((string) ($fila['fecha_ejecucion'] ?? '')), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Tecnico / Responsable"><span class="estado-tabla estado-tabla-exito"><?= htmlspecialchars((string) ((trim((string) ($fila['tecnico_responsable'] ?? '')) !== '') ? $fila['tecnico_responsable'] : 'No registrado'), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Observaciones"><span class="texto-secundario-tabla"><?= htmlspecialchars((string) ((trim((string) ($fila['observaciones'] ?? '')) !== '') ? $fila['observaciones'] : 'No registrado'), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Evidencia"><span class="estado-tabla estado-tabla-info">No registrado</span></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($realizados)) : ?>
            <tr>
              <td colspan="8">
                <div class="estado-vacio">
                  <span class="estado-vacio-icono"><?= $iconosPanel('spark') ?></span>
                  <div>
                    <strong>Aun no hay mantenimientos realizados</strong>
                    <p>Los mantenimientos marcados como hechos apareceran aqui automaticamente para trazabilidad y reporte.</p>
                  </div>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php
  $paginaActual = (int) ($paginaRealizados ?? 1);
  $totalPaginas = (int) ($totalPaginasRealizados ?? 1);
  $porPaginaActual = (int) ($porPaginaRealizados ?? 10);
  $filtroRealizados = '&q_realizados=' . urlencode((string) ($busquedaRealizados ?? ''));
  $urlBase = urlRuta('/panel?apartado=realizados&realizados_por_pagina=' . $porPaginaActual . $filtroRealizados);
  ?>
  <div class="paginacion paginacion-premium">
    <a class="boton-pagina <?= $paginaActual <= 1 ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&realizados_pagina=' . max(1, $paginaActual - 1) . '#tabla-realizados', ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
    <span>Pagina <?= $paginaActual ?> de <?= $totalPaginas ?></span>
    <a class="boton-pagina <?= $paginaActual >= $totalPaginas ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&realizados_pagina=' . min($totalPaginas, $paginaActual + 1) . '#tabla-realizados', ENT_QUOTES, 'UTF-8') ?>">Siguiente</a>
  </div>
</section>
