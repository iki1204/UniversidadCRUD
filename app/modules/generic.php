<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

$metaAll = require __DIR__ . '/../meta.php';
$module = $_GET['m'] ?? '';
if (!$module || !isset($metaAll[$module]) || isset($metaAll[$module]['module'])) {
  http_response_code(404);
  echo "Módulo inválido.";
  exit;
}

$meta = $metaAll[$module];
$title = $meta['title'];
$table = $meta['table'];
$pk = $meta['pk'];
$cols = $meta['columns'];

$action = $_GET['a'] ?? 'list';
$id = $_GET['id'] ?? null;
$canWrite = auth_can_write($module);

function fk_options(PDO $pdo, array $ref): array {
  [$t,$idcol,$labelcol] = $ref;
  return $pdo->query("SELECT $idcol AS id, $labelcol AS label FROM $t ORDER BY $labelcol")->fetchAll();
}

csrf_check();

if (!$canWrite && in_array($action, ['create', 'edit', 'delete'], true)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'delete') {
    http_response_code(403);
    echo "No tienes permisos para modificar este módulo.";
    exit;
  }
  flash_set('warning', 'No tienes permisos para crear o editar en este módulo.');
  redirect(url("/public/index.php?m=$module"));
}

if ($action === 'delete' && $id) {
  // delete
  $stmt = $pdo->prepare("DELETE FROM $table WHERE $pk = ?");
  $stmt->execute([$id]);
  flash_set('success', 'Registro eliminado.');
  redirect(url("/public/index.php?m=$module"));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = [];
  foreach ($cols as $name => $def) {
    if (!empty($def['readonly'])) continue;
    $val = $_POST[$name] ?? null;
    $data[$name] = $val === '' ? null : $val;
  }

  if ($action === 'create') {
    $fields = array_keys($data);
    $place = implode(',', array_fill(0, count($fields), '?'));
    $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES ($place)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_values($data));
    flash_set('success', 'Registro creado.');
    redirect(url("/public/index.php?m=$module"));
  }

  if ($action === 'edit' && $id) {
    $fields = array_keys($data);
    $set = implode(',', array_map(fn($f)=>"$f=?", $fields));
    $sql = "UPDATE $table SET $set WHERE $pk=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([...array_values($data), $id]);
    flash_set('success', 'Registro actualizado.');
    redirect(url("/public/index.php?m=$module"));
  }
}

// read record for edit
$record = null;
if (($action === 'edit') && $id) {
  $stmt = $pdo->prepare("SELECT * FROM $table WHERE $pk=?");
  $stmt->execute([$id]);
  $record = $stmt->fetch();
  if (!$record) { http_response_code(404); echo "No encontrado."; exit; }
}

// list
$list = [];
if ($action === 'list') {
  // if has fk columns, fetch joined labels as extra fields
  $select = "t.*";
  $joins = "";
  $i = 0;
  foreach ($cols as $name=>$def) {
    if (($def['type'] ?? '') === 'fk') {
      $i++;
      [$rt,$rid,$rlabel] = $def['ref'];
      $alias = "r$i";
      $joins .= " LEFT JOIN $rt $alias ON $alias.$rid = t.$name";
      $select .= ", $alias.$rlabel AS {$name}__label";
    }
  }
  $sql = "SELECT $select FROM $table t $joins ORDER BY t.$pk DESC";
  $list = $pdo->query($sql)->fetchAll();
}

include __DIR__ . '/../views/generic.php';
