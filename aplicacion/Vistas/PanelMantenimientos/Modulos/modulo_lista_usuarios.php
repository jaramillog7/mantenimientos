<section class="modulo-bloque tarjeta tarjeta-tabla-premium" id="gestion-usuarios">
  <div class="cabecera-modulo">
    <div>
      <h3>Lista de usuarios</h3>
      <p class="cabecera-modulo-texto">Consulta la informacion de cada usuario.</p>
    </div>
    <div class="cabecera-modulo-aside">
      <span class="dato-superior">Total usuarios</span>
      <strong><?= count($listaUsuarios ?? []) ?></strong>
    </div>
  </div>

  <?php if (!empty($usuarioEdicion)) : ?>
    <section class="tarjeta tarjeta-subseccion tarjeta-edicion">
      <div class="cabecera-subpanel">
        <div>
          <span class="estado-tabla estado-tabla-info">Edicion en curso</span>
          <h4>Actualizar usuario seleccionado</h4>
        </div>
        <a class="boton-limpiar" href="<?= htmlspecialchars(urlRuta('/panel?apartado=lista_usuarios&area_id=' . (int) ($areaIdFiltro ?? 0) . '&q_usuarios=' . urlencode((string) ($busquedaUsuarios ?? ''))), ENT_QUOTES, 'UTF-8') ?>">Cancelar</a>
      </div>

      <form method="post" action="<?= htmlspecialchars(urlRuta('/panel/usuarios/actualizar'), ENT_QUOTES, 'UTF-8') ?>" class="filtros-premium filtros-edicion">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="usuario_id" value="<?= (int) ($usuarioEdicion['id'] ?? 0) ?>">
        <input type="hidden" name="filtro_area_id" value="<?= (int) ($areaIdFiltro ?? 0) ?>">
        <input type="hidden" name="filtro_q_usuarios" value="<?= htmlspecialchars((string) ($busquedaUsuarios ?? ''), ENT_QUOTES, 'UTF-8') ?>">

        <label class="campo-filtro">
          <span>Nombre completo</span>
          <input id="edit_nombre" name="nombre" type="text" required value="<?= htmlspecialchars((string) ($usuarioEdicion['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </label>

        <label class="campo-filtro">
          <span>Codigo activo</span>
          <input id="edit_codigo" name="codigo_activo" type="number" min="1" required value="<?= (int) ($usuarioEdicion['codigo_activo'] ?? 0) ?>">
        </label>

        <label class="campo-filtro">
          <span>Serial del equipo</span>
          <input id="edit_serial" name="serial_equipo" type="text" required value="<?= htmlspecialchars((string) ($usuarioEdicion['serial_equipo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </label>

        <label class="campo-filtro campo-filtro-select">
          <span>Area / Equipo</span>
          <select id="edit_area" name="area_id">
            <option value="0">Sin area</option>
            <?php foreach (($listaAreas ?? []) as $area) : ?>
              <option
                value="<?= (int) ($area['id'] ?? 0) ?>"
                <?= (int) ($usuarioEdicion['area_id'] ?? 0) === (int) ($area['id'] ?? 0) ? 'selected' : '' ?>
              >
                <?= htmlspecialchars((string) ($area['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </label>

        <div class="grupo-botones-filtro">
          <button type="submit">Guardar cambios</button>
        </div>
      </form>
    </section>
  <?php endif; ?>

  <form method="get" action="<?= htmlspecialchars(urlRuta('/panel'), ENT_QUOTES, 'UTF-8') ?>" class="filtros-premium">
    <input type="hidden" name="apartado" value="lista_usuarios">

    <label class="campo-filtro campo-filtro-select">
      <span>Filtrar por equipo</span>
      <select id="area_id" name="area_id">
        <option value="0">Todos los equipos</option>
        <?php foreach (($listaAreas ?? []) as $area) : ?>
          <option
            value="<?= (int) ($area['id'] ?? 0) ?>"
            <?= (int) ($areaIdFiltro ?? 0) === (int) ($area['id'] ?? 0) ? 'selected' : '' ?>
          >
            <?= htmlspecialchars((string) ($area['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label class="campo-filtro">
      <span>Buscar por codigo o nombre</span>
      <input id="q_usuarios" name="q_usuarios" type="text" value="<?= htmlspecialchars((string) ($busquedaUsuarios ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Buscar por codigo o nombre">
    </label>

    <div class="grupo-botones-filtro">
      <button type="submit">Aplicar filtro</button>
      <a class="boton-limpiar" href="<?= htmlspecialchars(urlRuta('/panel?apartado=lista_usuarios'), ENT_QUOTES, 'UTF-8') ?>">Restablecer</a>
    </div>
  </form>

  <div class="tabla-shell">
    <div class="tabla-shell-encabezado"></div>
    <div class="tabla-contenedor">
      <table class="tabla-usuarios tabla-premium">
        <thead>
          <tr>
            <th>Area / Equipo</th>
            <th>Nombre</th>
            <th>Codigo activo</th>
            <th>Serial equipo</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php $areaActual = null; ?>
          <?php foreach (($listaUsuarios ?? []) as $fila) : ?>
            <?php $areaFila = (string) ($fila['area_nombre'] ?? 'SIN AREA'); ?>
            <?php if ($areaFila !== $areaActual) : ?>
              <tr class="fila-grupo">
                <td colspan="6">Equipo: <?= htmlspecialchars($areaFila, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <?php $areaActual = $areaFila; ?>
            <?php endif; ?>
            <?php $usuarioId = (int) ($fila['id'] ?? 0); ?>
            <?php $estadoActivo = (int) ($fila['estado_usuario'] ?? 1) === 1; ?>
            <tr>
              <td data-label="Area / Equipo"><span class="badge-area"><?= htmlspecialchars($areaFila, ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Nombre"><strong class="texto-principal-tabla"><?= htmlspecialchars((string) ($fila['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></strong></td>
              <td data-label="Codigo activo"><span class="codigo-activo"><?= htmlspecialchars((string) ($fila['codigo_activo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></td>
              <td data-label="Serial equipo"><?= htmlspecialchars((string) ($fila['serial_equipo'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
              <td data-label="Estado">
                <span class="estado-usuario-badge <?= $estadoActivo ? 'estado-usuario-activo' : 'estado-usuario-inactivo' ?>">
                  <?= $estadoActivo ? 'Activo' : 'Inactivo' ?>
                </span>
              </td>
              <td data-label="Acciones">
                <div class="acciones-usuarios">
                  <a
                    class="boton-accion-tabla boton-editar boton-compacto"
                    href="<?= htmlspecialchars(urlRuta('/panel?apartado=lista_usuarios&area_id=' . (int) ($areaIdFiltro ?? 0) . '&q_usuarios=' . urlencode((string) ($busquedaUsuarios ?? '')) . '&editar_usuario_id=' . $usuarioId), ENT_QUOTES, 'UTF-8') ?>"
                  >Editar</a>

                  <form method="post" action="<?= htmlspecialchars(urlRuta('/panel/usuarios/eliminar'), ENT_QUOTES, 'UTF-8') ?>" class="accion-form" onsubmit="return confirm('Esta accion elimina el usuario si no tiene mantenimientos. Deseas continuar?');">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="usuario_id" value="<?= $usuarioId ?>">
                    <input type="hidden" name="filtro_area_id" value="<?= (int) ($areaIdFiltro ?? 0) ?>">
                    <input type="hidden" name="filtro_q_usuarios" value="<?= htmlspecialchars((string) ($busquedaUsuarios ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="boton-accion-tabla boton-eliminar boton-compacto">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($listaUsuarios)) : ?>
            <tr>
              <td colspan="6">
                <div class="estado-vacio">
                  <span class="estado-vacio-icono"><?= $iconosPanel('usuarios') ?></span>
                  <div>
                    <strong>No hay usuarios para mostrar</strong>
                    <p>Prueba con otro equipo, limpia filtros o agrega registros desde la carga inicial de usuarios.</p>
                  </div>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
