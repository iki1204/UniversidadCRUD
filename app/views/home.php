<?php
$page_title = "Dashboard";
$page_subtitle = "Resumen general";
ob_start();
?>
<div class="row g-3">
  <?php foreach ($cards as $c): ?>
    <div class="col-12 col-md-6 col-xl-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div>
              <div class="text-muted small"><?= h($c['label']) ?></div>
              <div class="display-6 fw-semibold"><?= h($c['value']) ?></div>
            </div>
            <div class="icon-bubble">
              <i class="bi <?= h($c['icon']) ?>"></i>
            </div>
          </div>
          <a class="stretched-link" href="<?= h($c['href']) ?>"></a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mt-1">
  <div class="col-12 col-xl-8">
    <div class="card shadow-sm">
      <div class="card-header bg-white">
        <strong>Últimas ventas</strong>
        <span class="text-muted small">· últimas 8</span>
      </div>
      <div class="p-3 border-bottom">
        <div class="input-group input-group-sm">
          <input class="form-control" type="search" placeholder="Buscar Registros..." data-table-search="#lastSalesTable">
          <button class="btn btn-outline-secondary" type="button" data-table-search-button>Buscar</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="lastSalesTable">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>Total</th>
              <th>Estado</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lastSales as $v): ?>
              <tr>
                <td><?= h($v['VENTA_ID']) ?></td>
                <td class="text-muted"><?= h($v['FECHA']) ?></td>
                <td><?= h($v['CLIENTE']) ?></td>
                <td>$<?= h(number_format((float)$v['TOTAL'],2)) ?></td>
                <td><span class="badge bg-secondary"><?= h($v['ESTADO'] ?: '—') ?></span></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="<?= url('/public/index.php?m=ventas&a=view&id=' . $v['VENTA_ID']) ?>">Ver</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($lastSales)===0): ?>
              <tr><td colspan="6" class="text-center text-muted py-4">Sin ventas aún.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="card shadow-sm">
      <div class="card-header bg-white"><strong>Atajos</strong></div>
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action" href="<?= url('/public/index.php?m=ventas&a=create') ?>">
          <i class="bi bi-plus-circle me-2"></i>Nueva venta
        </a>
        <a class="list-group-item list-group-item-action" href="<?= url('/public/index.php?m=producto&a=create') ?>">
          <i class="bi bi-bag-plus me-2"></i>Nuevo producto
        </a>
        <a class="list-group-item list-group-item-action" href="<?= url('/public/index.php?m=cliente&a=create') ?>">
          <i class="bi bi-person-plus me-2"></i>Nuevo cliente
        </a>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
