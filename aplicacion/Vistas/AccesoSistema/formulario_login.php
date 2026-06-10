<?php

declare(strict_types=1);
?><!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($titulo ?? 'Login', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= htmlspecialchars(urlRecurso('publico/assets/css/login.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
  <main class="login-layout">
    <section class="login-col login-col-form">
      <div class="login-shell">
        <h1 class="login-title">Bienvenido</h1>
        <p class="login-subtitle">Accede a tu cuenta y continua con tu operacion.</p>

        <?php if (!empty($error)) : ?>
          <div class="alerta-error"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars(urlRuta('/login'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" class="login-form">
          <input type="hidden" name="_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

          <label for="correo">Correo</label>
          <div class="glass-input">
            <input id="correo" name="correo" type="email" required maxlength="120" placeholder="admin@mantenimientos.local">
          </div>

          <label for="password">Contraseña</label>
          <div class="glass-input input-password-wrap">
            <input id="password" name="password" type="password" required minlength="8" maxlength="120" placeholder="Ingresa tu contrasena">
            <button type="button" class="toggle-password" data-toggle-password aria-label="Mostrar u ocultar contrasena">
              <svg class="icon-eye icon-eye-open" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z" />
                <circle cx="12" cy="12" r="3.2" />
              </svg>
              <svg class="icon-eye icon-eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                <path d="m3 3 18 18" />
                <path d="M10.6 6.2A9.7 9.7 0 0 1 12 6c6.5 0 10 6 10 6a16.6 16.6 0 0 1-3.1 4.1" />
                <path d="M6.1 6.1A16.3 16.3 0 0 0 2 12s3.5 6 10 6c1.3 0 2.4-.2 3.5-.6" />
              </svg>
            </button>
          </div>

          <div class="login-row">
            <label class="check-remember">
              <input type="checkbox" name="recordar">
              <span>Mantener sesion</span>
            </label>
          </div>

          <button type="submit" class="btn-primary">Iniciar sesion</button>
        </form>
      </div>
    </section>

    <section class="login-col login-col-hero" aria-hidden="true">
      <div class="hero-bg"></div>
    </section>
  </main>

  <script>
    (() => {
      const toggleBtn = document.querySelector('[data-toggle-password]');
      const inputPassword = document.getElementById('password');
      if (toggleBtn && inputPassword) {
        toggleBtn.addEventListener('click', () => {
          const isHidden = inputPassword.type === 'password';
          inputPassword.type = isHidden ? 'text' : 'password';
          toggleBtn.classList.toggle('active', isHidden);
        });
      }
    })();
  </script>
</body>
</html>
