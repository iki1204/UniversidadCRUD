<?php
require_once __DIR__ . '/../helpers.php';
$config = require __DIR__ . '/../config.php';
$meta = require __DIR__ . '/../meta.php';
$flash = flash_get();
$m = $_GET['m'] ?? '';
$user = auth_user();
$roleLabel = $user['role_label'] ?? 'Usuario';
$visibleMeta = array_filter($meta, fn($def, $key) => auth_can_access($key), ARRAY_FILTER_USE_BOTH);
$navIcons = [
  'categoria' => 'bi-tags',
  'talla' => 'bi-rulers',
  'proveedor' => 'bi-truck',
  'cliente' => 'bi-people',
  'empleado' => 'bi-person-badge',
  'producto' => 'bi-box-seam',
  'ventas' => 'bi-cash-stack',
  'detalle_venta' => 'bi-receipt',
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($config['app']['name']) ?><?= $page_title ? ' · ' . h($page_title) : '' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="d-flex" style="min-height:100vh;">
    <aside class="sidebar border-end bg-white">
      <div class="p-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary rounded-pill"><?= h($roleLabel) ?></span>
          <div>
            <div class="fw-semibold"><?= h($config['app']['name']) ?></div>
            <div class="text-muted small"><?= h($user['user'] ?? 'Dashboard') ?></div>
          </div>
        </div>
      </div>
      <div class="p-2">
        <a class="nav-link px-3 py-2 rounded <?= $m==''?'active':'' ?>" href="<?= url('/public/index.php') ?>">
          <i class="bi bi-speedometer2 me-2"></i>Inicio
        </a>
        <div class="text-uppercase small text-muted px-3 mt-3 mb-1">Gestión</div>
        <?php foreach ($visibleMeta as $key => $def): ?>
          <a class="nav-link px-3 py-2 rounded <?= $m===$key?'active':'' ?>" href="<?= url('/public/index.php?m=' . $key) ?>">
            <i class="bi <?= h($navIcons[$key] ?? 'bi-table') ?> me-2"></i><?= h($def['title']) ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="mt-auto p-3 border-top text-muted small">
        <?= date('Y') ?> · Boutique
      </div>
    </aside>

    <main class="flex-grow-1">
      <header class="bg-white border-bottom">
        <div class="container-fluid py-3 d-flex align-items-center justify-content-between">
          <div>
            <div class="fw-semibold"><?= h($page_title ?? 'Dashboard') ?></div>
            <div class="text-muted small"><?= h($page_subtitle ?? 'Panel de administración') ?></div>
          </div>
          <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="<?= url('/public/landing.php') ?>">
              <i class="bi bi-house"></i>
            </a>
            <a class="btn btn-outline-danger btn-sm" href="<?= url('/public/logout.php') ?>">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          </div>
        </div>
      </header>

      <div class="container-fluid py-4">
        <?php if ($flash): ?>
          <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>

        <?= $content ?>

        <div class="text-center text-muted small mt-5">
          © 2026 Boutique. Todos los derechos reservados.
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>
