<?php
$page_title = $title;
$canWrite = auth_can_write('ventas');
ob_start();
?>
<?php if ($action === 'list'): ?>
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div class="text-muted small">Crea ventas con múltiples productos y el sistema actualiza stock y total.</div>
    <?php if ($canWrite): ?>
      <a class="btn btn-primary" href="<?= url('/public/index.php?m=ventas&a=create') ?>">
        <i class="bi bi-plus-lg me-1"></i>Nueva venta
      </a>
    <?php endif; ?>
  </div>

  <div class="card shadow-sm">
    <div class="p-3 border-bottom">
      <div class="input-group input-group-sm">
        <input class="form-control" type="search" placeholder="Buscar ..." data-table-search="#ventasTable">
        <button class="btn btn-outline-secondary" type="button" data-table-search-button>Buscar</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="ventasTable">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Empleado</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Método</th>
            <th class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list as $v): ?>
            <tr>
              <td><?= h($v['VENTA_ID']) ?></td>
              <td class="text-muted"><?= h($v['FECHA']) ?></td>
              <td><?= h($v['CLIENTE']) ?></td>
              <td><?= h($v['EMPLEADO']) ?></td>
              <td>$<?= h(number_format((float)$v['TOTAL'],2)) ?></td>
              <td><span class="badge bg-secondary"><?= h($v['ESTADO'] ?: '—') ?></span></td>
              <td><?= h($v['METODO_PAGO'] ?: '—') ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="<?= url('/public/index.php?m=ventas&a=view&id=' . $v['VENTA_ID']) ?>">Ver</a>
                <?php if ($canWrite): ?>
                  <a class="btn btn-sm btn-outline-secondary" href="<?= url('/public/index.php?m=ventas&a=edit&id=' . $v['VENTA_ID']) ?>">Editar</a>
                  <form class="d-inline" method="post" action="<?= url('/public/index.php?m=ventas&a=delete&id=' . $v['VENTA_ID']) ?>" onsubmit="return confirm('¿Eliminar la venta? Se restaurará stock.');">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (count($list)===0): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">No hay ventas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php if ($action === 'create'): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= url('/public/index.php?m=ventas&a=create') ?>">
        <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
        <div class="row g-3">
          <div class="col-12 col-lg-4">
            <label class="form-label">Cliente <span class="text-danger">*</span></label>
            <select class="form-select" name="CLIENTE_ID" required>
              <option value="">Seleccione...</option>
              <?php foreach ($clients as $c): ?>
                <option value="<?= h($c['id']) ?>"><?= h($c['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Empleado <span class="text-danger">*</span></label>
            <select class="form-select" name="EMPLEADO_ID" required>
              <option value="">Seleccione...</option>
              <?php foreach ($employees as $e): ?>
                <option value="<?= h($e['id']) ?>"><?= h($e['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Fecha</label>
            <input class="form-control" name="FECHA" type="datetime-local" value="<?= h(date('Y-m-d\TH:i')) ?>">
          </div>

          <div class="col-12 col-lg-4">
            <label class="form-label">Estado</label>
            <input class="form-control" name="ESTADO" value="COMPLETADA">
          </div>
          <div class="col-12 col-lg-4">
            <label class="form-label">Método de pago</label>
            <input class="form-control" name="METODO_PAGO" value="EFECTIVO">
          </div>
        </div>

        <hr class="my-4">

        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Detalle de venta</div>
          <button class="btn btn-outline-primary btn-sm" type="button" id="addRow">
            <i class="bi bi-plus"></i> Añadir producto
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered align-middle" id="itemsTable">
            <thead class="table-light">
              <tr>
                <th style="min-width:320px;">Producto <span class="text-danger">*</span></th>
                <th style="width:140px;">Cantidad</th>
                <th style="width:160px;">Precio</th>
                <th style="width:160px;">Subtotal</th>
                <th style="width:70px;"></th>
              </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-end">TOTAL</th>
                <th id="totalCell">$0.00</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-1"></i>Registrar venta</button>
          <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=ventas') ?>">Cancelar</a>
        </div>
      </form>
    </div>
  </div>

  <script>
    window.__products = <?= json_encode($products) ?>;
  </script>
<?php endif; ?>

<?php if ($action === 'view' && $venta): ?>
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="fw-semibold">Venta #<?= h($venta['VENTA_ID']) ?></div>
      <div class="text-muted small"><?= h($venta['FECHA']) ?> · <?= h($venta['CLIENTE']) ?> · <?= h($venta['EMPLEADO']) ?></div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=ventas') ?>">Volver</a>
      <a class="btn btn-outline-primary" href="<?= url('/public/index.php?m=detalle_venta&venta_id=' . $venta['VENTA_ID']) ?>">Ver detalle</a>
      <?php if ($canWrite): ?>
        <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=ventas&a=edit&id=' . $venta['VENTA_ID']) ?>">Editar</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="text-muted small">Total</div>
          <div class="display-6 fw-semibold">$<?= h(number_format((float)$venta['TOTAL'],2)) ?></div>
          <div class="mt-2">
            <span class="badge bg-secondary"><?= h($venta['ESTADO'] ?: '—') ?></span>
            <span class="badge bg-light text-dark border"><?= h($venta['METODO_PAGO'] ?: '—') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-white"><strong>Productos</strong></div>
        <div class="p-3 border-bottom">
          <div class="input-group input-group-sm">
            <input class="form-control" type="search" placeholder="Buscar en la tabla..." data-table-search="#ventaProductosTable">
            <button class="btn btn-outline-secondary" type="button" data-table-search-button>Buscar</button>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="ventaProductosTable">
            <thead class="table-light">
              <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($detalle as $d): ?>
                <tr>
                  <td><?= h($d['CODIGO']) ?> · <?= h($d['DESCRIPCION']) ?></td>
                  <td><?= h($d['CANTIDAD']) ?></td>
                  <td>$<?= h(number_format((float)$d['PRECIO'],2)) ?></td>
                  <td>$<?= h(number_format((float)$d['CANTIDAD']*(float)$d['PRECIO'],2)) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (count($detalle)===0): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Sin detalle.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($action === 'edit' && $venta && $canWrite): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= url('/public/index.php?m=ventas&a=edit&id=' . h($venta['VENTA_ID'])) ?>">
        <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Estado</label>
            <input class="form-control" name="ESTADO" value="<?= h($venta['ESTADO']) ?>">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Método de pago</label>
            <input class="form-control" name="METODO_PAGO" value="<?= h($venta['METODO_PAGO']) ?>">
          </div>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary" type="submit">Guardar</button>
          <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=ventas&a=view&id=' . h($venta['VENTA_ID'])) ?>">Cancelar</a>
        </div>
      </form>
      <div class="text-muted small mt-3">
        Para modificar productos, elimina la venta y créala de nuevo (simple). Puedes extender este módulo para editar líneas del detalle.
      </div>
    </div>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
