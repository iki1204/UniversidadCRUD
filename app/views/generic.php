<?php
$page_title = $title;
$canWrite = auth_can_write($module);
ob_start();
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="text-muted small">Lista de <code><?= h($title) ?></code></div>
  <?php if ($canWrite): ?>
    <a class="btn btn-primary" href="<?= url('/public/index.php?m=' . h($module) . '&a=create') ?>">
      <i class="bi bi-plus-lg me-1"></i>Nuevo
    </a>
  <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
  <?php
    $searchLabels = array_map(
      fn($def, $name) => $def['label'] ?? $name,
      $cols,
      array_keys($cols)
    );
    $searchPlaceholder = 'Buscar por: ' . implode(', ', $searchLabels);
  ?>
  <div class="card shadow-sm">
    <div class="p-3 border-bottom">
      <div class="input-group input-group-sm">
        <input class="form-control" type="search" placeholder="Buscar ..." data-table-search="#genericTable">
        <button class="btn btn-outline-secondary" type="button" data-table-search-button>Buscar</button>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="genericTable">
        <thead class="table-light">
          <tr>
            <?php foreach ($cols as $name => $def): ?>
              <th><?= h($def['label'] ?? $name) ?></th>
            <?php endforeach; ?>
            <?php if ($canWrite): ?>
              <th class="text-end">Acciones</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list as $row): ?>
            <tr>
              <?php foreach ($cols as $name => $def): ?>
                <td>
                  <?php if (($def['type'] ?? '') === 'fk'): ?>
                    <?= h($row[$name . '__label'] ?? $row[$name]) ?>
                  <?php else: ?>
                    <?= h($row[$name]) ?>
                  <?php endif; ?>
                </td>
              <?php endforeach; ?>
              <?php if ($canWrite): ?>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="<?= url('/public/index.php?m=' . h($module) . '&a=edit&id=' . h($row[$pk])) ?>">Editar</a>
                  <form class="d-inline" method="post" action="<?= url('/public/index.php?m=' . h($module) . '&a=delete&id=' . h($row[$pk])) ?>" onsubmit="return confirm('¿Eliminar este registro?');">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                  </form>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
          <?php if (count($list)===0): ?>
            <tr><td colspan="<?= count($cols)+($canWrite ? 1 : 0) ?>" class="text-center text-muted py-4">No hay registros.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php if (($action === 'create' || $action === 'edit') && $canWrite): ?>
  <?php
    $isEdit = ($action==='edit');
    $formAction = url('/public/index.php?m=' . h($module) . '&a=' . h($action) . ($isEdit ? '&id=' . h($id) : ''));
  ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= $formAction ?>" class="row g-3">
        <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
        <?php foreach ($cols as $name => $def): ?>
          <?php
            $label = $def['label'] ?? $name;
            $type = $def['type'] ?? 'text';
            $readonly = !empty($def['readonly']);
            $required = !empty($def['required']);
            $value = $record[$name] ?? '';
          ?>
          <div class="col-12 col-md-6">
            <label class="form-label"><?= h($label) ?><?= $required ? ' <span class="text-danger">*</span>' : '' ?></label>

            <?php if ($type === 'fk'): ?>
              <?php $options = fk_options($pdo, $def['ref']); ?>
              <select class="form-select" name="<?= h($name) ?>" <?= $required?'required':'' ?> <?= $readonly?'disabled':'' ?>>
                <option value="">Seleccione...</option>
                <?php foreach ($options as $opt): ?>
                  <option value="<?= h($opt['id']) ?>" <?= (string)$opt['id']===(string)$value ? 'selected' : '' ?>>
                    <?= h($opt['label']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if ($readonly): ?><input type="hidden" name="<?= h($name) ?>" value="<?= h($value) ?>"><?php endif; ?>

            <?php else: ?>
              <input
                class="form-control"
                name="<?= h($name) ?>"
                type="<?= h($type==='number'?'number':$type) ?>"
                value="<?= h($value) ?>"
                <?= $required?'required':'' ?>
                <?= $readonly?'readonly':'' ?>
                <?= isset($def['step']) ? 'step="'.h($def['step']).'"' : '' ?>
              >
            <?php endif; ?>

          </div>
        <?php endforeach; ?>

        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-check2-circle me-1"></i><?= $isEdit ? 'Guardar cambios' : 'Crear' ?>
          </button>
          <a class="btn btn-outline-secondary" href="<?= url('/public/index.php?m=' . h($module)) ?>">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../partials/layout.php';
