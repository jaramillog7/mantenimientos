<section class="modulo-bloque tarjeta tarjeta-tabla-premium" id="tabla-programados">
  <?php $fechaHoyFormulario = (new DateTimeImmutable('today'))->format('Y-m-d'); ?>
  <div class="cabecera-modulo">
    <div>
      <p class="cabecera-modulo-eyebrow">Agenda confirmada</p>
      <h3>Mantenimientos programados</h3>
      <p class="cabecera-modulo-texto">Consulta los mantenimientos agendados.</p>
    </div>
    <div class="cabecera-modulo-aside">
      <span class="dato-superior">Total registros</span>
      <strong><?= (int) ($totalProgramados ?? 0) ?></strong>
    </div>
  </div>

  <form method="get" action="<?= htmlspecialchars(urlRuta('/panel'), ENT_QUOTES, 'UTF-8') ?>" class="filtros-premium">
    <input type="hidden" name="apartado" value="programados">
    <input type="hidden" name="programados_pagina" value="1">

    <label class="campo-filtro campo-filtro-select">
      <span>Registros por pagina</span>
      <select id="programados_por_pagina" name="programados_por_pagina">
        <?php foreach (($opcionesPorPagina ?? [10, 25, 50]) as $opcion) : ?>
          <option value="<?= (int) $opcion ?>" <?= (int) ($porPaginaProgramados ?? 10) === (int) $opcion ? 'selected' : '' ?>>
            <?= (int) $opcion ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <div class="grupo-botones-filtro">
      <button type="submit">Actualizar vista</button>
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
            <th>Ultimo mantenimiento</th>
            <th>Fecha programada</th>
            <th>Accion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (($programados ?? []) as $fila) : ?>
            <tr>
              <td data-label="Area / Equipo"><span class="badge-area"><?= htmlspecialchars((string) ($fila['area_nombre'] ?? 'SIN AREA'), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Usuario"><strong class="texto-principal-tabla"><?= htmlspecialchars((string) ($fila['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td data-label="Activo"><span class="codigo-activo"><?= htmlspecialchars((string) ($fila['codigo_activo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Ultima fecha"><?= htmlspecialchars($formatearFecha((string) ($fila['ultima_fecha_programada'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
              <td data-label="Fecha programada">
                <div class="proximo-resumen">
                  <span class="fecha-destacada" data-fecha-id="<?= (int) ($fila['mantenimiento_id'] ?? 0) ?>">
                    <?= htmlspecialchars($formatearFecha((string) ($fila['proxima_fecha_programada'] ?? '')), ENT_QUOTES, 'UTF-8') ?>
                  </span>
                  <span class="estado-tabla estado-tabla-info">Programado</span>
                </div>
              </td>
              <td data-label="Accion">
                <div class="acciones-programados">
                  <form
                    method="post"
                    action="<?= htmlspecialchars(urlRuta('/panel/programados/hecho'), ENT_QUOTES, 'UTF-8') ?>"
                    class="accion-form accion-form-programar accion-form-hecho"
                  >
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="mantenimiento_id" value="<?= (int) ($fila['mantenimiento_id'] ?? 0) ?>">
                    <input type="hidden" name="programados_pagina" value="<?= (int) ($paginaProgramados ?? 1) ?>">
                    <input type="hidden" name="programados_por_pagina" value="<?= (int) ($porPaginaProgramados ?? 10) ?>">
                    <label class="campo-fecha-inline">
                      <span>Fecha</span>
                      <input
                        type="date"
                        name="fecha_ejecucion"
                        class="input-fecha-programar"
                        value="<?= htmlspecialchars($fechaHoyFormulario, ENT_QUOTES, 'UTF-8') ?>"
                        max="<?= htmlspecialchars($fechaHoyFormulario, ENT_QUOTES, 'UTF-8') ?>"
                        required
                      >
                    </label>
                    <label class="campo-fecha-inline">
                      <span>Tecnico</span>
                      <select name="tecnico_id" class="input-fecha-programar" required>
                        <option value="">Selecciona un tecnico</option>
                        <?php foreach (($tecnicos ?? []) as $tecnico) : ?>
                          <option value="<?= (int) ($tecnico['id'] ?? 0) ?>">
                            <?= htmlspecialchars((string) ($tecnico['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </label>
                    <label class="campo-fecha-inline campo-fecha-inline-amplio">
                      <span>Observaciones</span>
                      <textarea
                        name="observaciones"
                        class="input-fecha-programar input-observaciones-programado"
                        rows="3"
                        placeholder="Escribe aqui lo que deseas registrar sobre el mantenimiento"
                      ></textarea>
                    </label>
                    <button type="submit" class="boton-accion boton-hecho">Marcar hecho</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($programados)) : ?>
            <tr>
              <td colspan="6">
                <div class="estado-vacio">
                  <span class="estado-vacio-icono"><?= $iconosPanel('calendario') ?></span>
                  <div>
                    <strong>No hay mantenimientos programados</strong>
                    <p>Cuando asignes fechas desde  el apartado proximos mantenimientos, la agenda activa aparecera aqui para confirmar ejecucion.</p>
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
  $paginaActual = (int) ($paginaProgramados ?? 1);
  $totalPaginas = (int) ($totalPaginasProgramados ?? 1);
  $porPaginaActual = (int) ($porPaginaProgramados ?? 10);
  $urlBase = urlRuta('/panel?apartado=programados&programados_por_pagina=' . $porPaginaActual);
  ?>
  <div class="paginacion paginacion-premium">
    <a class="boton-pagina <?= $paginaActual <= 1 ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&programados_pagina=' . max(1, $paginaActual - 1) . '#tabla-programados', ENT_QUOTES, 'UTF-8') ?>">Anterior</a>
    <span>Pagina <?= $paginaActual ?> de <?= $totalPaginas ?></span>
    <a class="boton-pagina <?= $paginaActual >= $totalPaginas ? 'deshabilitado' : '' ?>" href="<?= htmlspecialchars($urlBase . '&programados_pagina=' . min($totalPaginas, $paginaActual + 1) . '#tabla-programados', ENT_QUOTES, 'UTF-8') ?>">Siguiente</a>
  </div>
</section>
