<?php
$page_title = $title;
$canWrite = auth_can_write('detalle_venta');
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="d-flex gap-2">
    <?php if ($venta_id): ?>
      <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=ventas&a=view&id=' . h($venta_id)) ?>">Volver a venta</a>
      <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=detalle_venta') ?>">Ver todo</a>
    <?php endif; ?>
  </div>
</div>

<div class="card shadow-sm">
  <div class="p-3 border-bottom">
    <div class="input-group input-group-sm">
      <input class="form-control" type="search" placeholder="Buscar..." data-table-search="#detalleVentaTable">
      <button class="btn btn-outline-secondary" type="button" data-table-search-button>Buscar</button>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="detalleVentaTable">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Venta</th>
          <th>Fecha</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Subtotal</th>
          <?php if ($canWrite): ?>
            <th class="text-end">Acciones</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($list as $row): ?>
          <tr>
            <td><?= h($row['DETALLE_ID']) ?></td>
            <td>
              <a href="<?= url('/public/index.php?m=ventas&a=view&id=' . h($row['VENTA_ID'])) ?>">#<?= h($row['VENTA_ID']) ?></a>
            </td>
            <td class="text-muted"><?= h($row['FECHA_VENTA']) ?></td>
            <td><?= h($row['PRODUCTO']) ?></td>
            <td><?= h($row['CANTIDAD']) ?></td>
            <td>$<?= h(number_format((float)$row['PRECIO'],2)) ?></td>
            <td>$<?= h(number_format((float)$row['CANTIDAD']*(float)$row['PRECIO'],2)) ?></td>
            <?php if ($canWrite): ?>
              <td class="text-end">
                <form class="d-inline" method="post"
                  action="<?= url('/public/index.php?m=detalle_venta&a=delete&id=' . h($row['DETALLE_ID']) . ($venta_id ? '&venta_id=' . h($venta_id) : '')) ?>"
                  onsubmit="return confirm('¿Eliminar esta línea? Se ajustará stock y total.');">
                  <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                </form>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
        <?php if (count($list)===0): ?>
          <tr><td colspan="<?= $canWrite ? 8 : 7 ?>" class="text-center text-muted py-4">No hay líneas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
