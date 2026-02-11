<?php
$config = require __DIR__ . '/../config.php';
$flash = flash_get();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión · <?= h($config['app']['name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
      <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <div class="text-center mb-3">
              <div class="fw-semibold"><?= h($config['app']['name']) ?></div>
              <div class="text-muted small">Acceso al panel</div>
            </div>
            <?php if ($flash): ?>
              <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
            <?php endif; ?>
            <form method="post" class="d-grid gap-3">
              <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
              <div>
                <label class="form-label">Usuario</label>
                <input class="form-control" name="username" autocomplete="username" required>
              </div>
              <div>
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="password" autocomplete="current-password" required>
              </div>
              <button class="btn btn-primary" type="submit">
                <i class="bi bi-box-arrow-in-right me-1"></i>Ingresar
              </button>
            </form>
          </div>
        </div>
        <div class="text-center text-muted small mt-3">
          <?= date('Y') ?> · Boutique
        </div>
      </div>
    </div>
  </div>
</body>
</html>
