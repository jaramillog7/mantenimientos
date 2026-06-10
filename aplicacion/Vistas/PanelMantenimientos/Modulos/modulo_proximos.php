<section class="modulo-bloque tarjeta tarjeta-tabla-premium" id="tabla-proximos">
  <div class="cabecera-modulo">
    <div>
      <h3>Cronograma de proximos mantenimientos</h3>
      <p class="cabecera-modulo-texto">Agenda los proximos mantenimientos preventivos.</p>
    </div>
    <div class="cabecera-modulo-aside">
    
    </div>
  </div>

  <form method="get" action="<?= htmlspecialchars(urlRuta('/panel'), ENT_QUOTES, 'UTF-8') ?>" class="filtros-premium">
    <input type="hidden" name="apartado" value="proximos">
    <input type="hidden" name="proximos_pagina" value="1">

    <label class="campo-filtro">
      <span>Buscar usuario o activo</span>
      <input id="q_proximos" name="q_proximos" type="text" value="<?= htmlspecialchars((string) ($busquedaProximos ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por codigo o nombre">
    </label>

    <label class="campo-filtro campo-filtro-select">
      <span>Registros por pagina</span>
      <select id="proximos_por_pagina" name="proximos_por_pagina">
        <?php foreach (($opcionesPorPagina ?? [10, 25, 50]) as $opcion) : ?>
          <option value="<?= (int) $opcion ?>" <?= (int) ($porPaginaProximos ?? 10) === (int) $opcion ? 'selected' : '' ?>>
            <?= (int) $opcion ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="grupo-botones-filtro">
      <button type="submit">Aplicar filtro</button>
      <a class="boton-limpiar" href="<?= htmlspecialchars(urlRuta('/panel?apartado=proximos'), ENT_QUOTES, 'UTF-8') ?>">Restablecer</a>
    </div>
  </form>

  <div class="tabla-shell">
    <div class="tabla-shell-encabezado">
      <span class="estado-tabla estado-tabla-activo">Planeacion activa</span>
    </div>

    <div class="tabla-contenedor" data-sort-table-wrapper>
      <table class="tabla-usuarios tabla-premium" data-sort-table>
        <thead>
          <tr>
            <th>Area / Equipo</th>
            <th>Usuario</th>
            <th>Activo</th>
            <th>Ultimo mantenimiento</th>
            <th>Proximo mantenimiento</th>
            <th>Programacion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($proximos ?? []) as $fila) : ?>
            <?php
            $fechaProxima = (string) ($fila['proxima_fecha_programada'] ?? '');
            $fechaProximaObj = DateTimeImmutable::createFromFormat('Y-m-d', $fechaProxima);
            $fechaHoy = new DateTimeImmutable('today');
            $estadoAgenda = 'En rango';
            $claseAgenda = 'estado-tabla-exito';
            if ($fechaProximaObj instanceof DateTimeImmutable) {
                $dias = (int) $fechaHoy->diff($fechaProximaObj)->format('%r%a');
                if ($dias < 0) {
                    $estadoAgenda = 'Vencido';
                    $claseAgenda = 'estado-tabla-alerta';
                } elseif ($dias <= 7) {
                    $estadoAgenda = 'Proximo';
                    $claseAgenda = 'estado-tabla-info';
                }
            }
            ?>
            <tr>
              <td data-label="Area / Equipo"><span class="badge-area"><?= htmlspecialchars((string) ($fila['area_nombre'] ?? 'SIN AREA'), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Usuario"><strong class="texto-principal-tabla"><?= htmlspecialchars((string) ($fila['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td data-label="Activo"><span class="codigo-activo"><?= htmlspecialchars((string) ($fila['codigo_activo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Ultima fecha"><?= htmlspecialchars($formatearFecha((string) ($fila['ultima_fecha_programada'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
              <td data-label="Proxima fecha">
                <div class="proximo-resumen">
                  <span class="fecha-destacada"><?= htmlspecialchars($formatearFecha((string) ($fila['proxima_fecha_programada'] ?? '')), ENT_QUOTES, 'UTF-8') ?></span>
                  <span class="estado-tabla <?= htmlspecialchars($claseAgenda, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($estadoAgenda, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
              </td>
              <td data-label="Programacion">
                <form method="post" action="<?= htmlspecialchars(urlRuta('/panel/proximos/programar'), ENT_QUOTES, 'UTF-8') ?>" class="accion-form accion-form-programar" onsubmit="return confirm('Confirmas programar este mantenimiento?');">
                  <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="mantenimiento_id" value="<?= (int) ($fila['mantenimiento_id'] ?? 0) ?>">
                  <input type="hidden" name="proximos_pagina" value="<?= (int) ($paginaProximos ?? 1) ?>">
                  <input type="hidden" name="proximos_por_pagina" value="<?= (int) ($porPaginaProximos ?? 10) ?>">
                  <input type="hidden" name="q_proximos" value="<?= htmlspecialchars((string) ($busquedaProximos ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                  <label class="campo-fecha-inline">
                    <span>Fecha</span>
                    <input
                      type="date"
                      name="fecha_programada"
                      class="input-fecha-programar"
                      value="<?= htmlspecialchars((string) ($fila['proxima_fecha_programada'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                      required
                    >
                  </label>
                  <button type="submit" class="boton-accion boton-programar">Programar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($proximos)) : ?>
            <tr>
              <td colspan="6">
                <div class="estado-vacio">
                  <span class="estado-vacio-icono"><?= $iconosPanel('check') ?></span>
                  <div>
                    <strong>No hay mantenimientos pendientes por programar</strong>
                    <p>Cuando existan equipos pendientes de agenda apareceran aqui con prioridad y fecha sugerida.</p>
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
  $paginaActual = (int) ($paginaProximos ?? 1);
  $totalPaginas = (int) ($totalPaginasProximos ?? 1);
  $porPaginaActual = (int) ($porPaginaProximos ?? 10);
  $filtroProximos = '&q_proximos=' . urlencode((string) ($busquedaProximos ?? ''));
  $urlBase = urlRuta('/panel?apartado=proximos&proximos_por_pagina=' . $porPaginaActual . $filtroProximos);
  ?>
  <div class="paginacion paginacion-premium">
    <a class="boton-pagina <?= $paginaActual <= 1 ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&proximos_pagina=' . max(1, $paginaActual - 1) . '#tabla-proximos', ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
    <span>Pagina <?= $paginaActual ?> de <?= $totalPaginas ?></span>
    <a class="boton-pagina <?= $paginaActual >= $totalPaginas ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&proximos_pagina=' . min($totalPaginas, $paginaActual + 1) . '#tabla-proximos', ENT_QUOTES, 'UTF-8') ?>">Siguiente</a>
  </div>
</section>
