<?php

declare(strict_types=1);
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo ?? 'Inicio', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars(urlRecurso('publico/assets/css/inicio.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
  <main class="contenedor">
    <h1><?= htmlspecialchars($titulo ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="subtitulo"><?= htmlspecialchars($mensaje ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <p class="estado-bd"><strong>Estado de conexion:</strong> <?= htmlspecialchars($estadoConexion ?? '', ENT_QUOTES, 'UTF-8') ?></p>

    <section class="bloque-formulario">
      <form method="get" action="" data-form-consulta>
        <div class="fila-formulario">
          <div class="campo">
            <label for="codigo"><strong>Codigo activo</strong></label>
            <input
              id="codigo"
              name="codigo"
              type="number"
              min="1"
              required
              value="<?= htmlspecialchars((string) ($codigoActivoConsulta ?? ''), ENT_QUOTES, 'UTF-8') ?>"
            >
          </div>
          <button class="boton" type="submit">Consultar mantenimiento</button>
        </div>
      </form>
    </section>

    <section class="tarjetas">
      <article class="tarjeta">
        <h3>Codigo consultado</h3>
        <p><?= htmlspecialchars((string) ($codigoActivoConsulta ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
      <article class="tarjeta">
        <h3>Usuario</h3>
        <p><?= htmlspecialchars((string) ($resultadoCalculo['nombre'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
      <article class="tarjeta">
        <h3>Ultima fecha programada</h3>
        <p><?= htmlspecialchars((string) ($resultadoCalculo['ultima_fecha_programada'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
      <article class="tarjeta">
        <h3>Proxima fecha programada</h3>
        <p><?= htmlspecialchars((string) ($resultadoCalculo['proxima_fecha_programada'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
      <article class="tarjeta">
        <h3>Recordatorio (7 dias antes)</h3>
        <p><?= htmlspecialchars((string) ($resultadoCalculo['fecha_recordatorio'] ?? 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
      <article class="tarjeta">
        <h3>Resultado</h3>
        <p><?= htmlspecialchars((string) ($resultadoCalculo['mensaje'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
      </article>
    </section>

    <section class="bloque-resultado">
      <p class="nota"><strong>Tip:</strong> tambien puedes consultar por URL con <code>?codigo=845</code>.</p>
    </section>
  </main>
  <script src="<?= htmlspecialchars(urlRecurso('publico/assets/js/inicio.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
