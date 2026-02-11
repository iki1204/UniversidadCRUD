<?php
require_once __DIR__ . '/../app/helpers.php';
$config = require __DIR__ . '/../app/config.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($config['app']['name']) ?> · Boutique</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('/assets/css/app.css') ?>" rel="stylesheet">
</head>
<body class="landing-simple">
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container py-2">
      <a class="navbar-brand fw-semibold" href="<?= url('/public/landing.php') ?>">
        <i class="bi bi-stars text-primary me-2"></i><?= h($config['app']['name']) ?>
      </a>
      <div class="ms-auto">
        <a class="btn btn-outline-primary" href="<?= url('/public/login.php') ?>">Ingresar</a>
      </div>
    </div>
  </nav>

  <main>
    <section class="landing-simple__hero">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-12 col-lg-6">
            <span class="badge text-bg-light border mb-3">Boutique · Nueva temporada</span>
            <h1 class="display-5 fw-semibold">Estilo curado para cada ocasión.</h1>
            <p class="lead text-muted">Prendas seleccionadas, asesoría cercana y una experiencia de compra cálida.</p>
            <div class="d-flex flex-wrap gap-3">
              <img src="../assets/img/background.jpg" class="img-fluid rounded" alt="Imagen de fondo de la boutique">
            </div>
          </div>
          <div class="col-12 col-lg-6">
            <div class="landing-simple__card">
              <h3 class="fw-semibold">Lo esencial de la semana</h3>
              <p class="text-muted">Looks listos para oficina, fin de semana y eventos.</p>
              <ul class="list-unstyled mb-0 landing-simple__list">
                <li><i class="bi bi-check2"></i> Vestidos fluidos y suaves.</li>
                <li><i class="bi bi-check2"></i> Blazers ligeros y pantalones de tiro alto.</li>
                <li><i class="bi bi-check2"></i> Accesorios minimalistas.</li>
              </ul>
            </div>
            <div class="container mt-4">
              <div class="row g-4">
                <div class="col-12 col-md-4">
                  <div class="landing-simple__tile">
                    <h5>Office chic</h5>
                    <p class="text-muted">Cortes limpios y tonos neutros.</p>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="landing-simple__tile">
                    <h5>Weekend</h5>
                    <p class="text-muted">Denim premium y básicos cómodos.</p>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="landing-simple__tile">
                    <h5>Eventos</h5>
                    <p class="text-muted">Brillo sutil y siluetas elegantes.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

   

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
