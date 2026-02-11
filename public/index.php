<?php
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/helpers.php';

auth_require();

$m = $_GET['m'] ?? '';
$meta = require __DIR__ . '/../app/meta.php';

if ($m === '') {
  // Dashboard counts
  $cards = [];
  if (auth_can_access('producto')) {
    $cards[] = ['label'=>'Productos','value'=>$pdo->query("SELECT COUNT(*) c FROM PRODUCTO")->fetch()['c'],'icon'=>'bi-bag','href'=>url('/public/index.php?m=producto')];
  }
  if (auth_can_access('cliente')) {
    $cards[] = ['label'=>'Clientes','value'=>$pdo->query("SELECT COUNT(*) c FROM CLIENTE")->fetch()['c'],'icon'=>'bi-people','href'=>url('/public/index.php?m=cliente')];
  }
  if (auth_can_access('ventas')) {
    $cards[] = ['label'=>'Ventas','value'=>$pdo->query("SELECT COUNT(*) c FROM VENTAS")->fetch()['c'],'icon'=>'bi-receipt','href'=>url('/public/index.php?m=ventas')];
  }
  if (auth_can_access('proveedor')) {
    $cards[] = ['label'=>'Proveedores','value'=>$pdo->query("SELECT COUNT(*) c FROM PROVEEDOR")->fetch()['c'],'icon'=>'bi-truck','href'=>url('/public/index.php?m=proveedor')];
  }

  $lastSales = [];
  if (auth_can_access('ventas')) {
    $lastSales = $pdo->query("SELECT v.VENTA_ID, v.FECHA, v.TOTAL, v.ESTADO, CONCAT(c.NOMBRE,' ',c.APELLIDO) AS CLIENTE
                              FROM VENTAS v
                              JOIN CLIENTE c ON c.CLIENTE_ID=v.CLIENTE_ID
                              ORDER BY v.VENTA_ID DESC LIMIT 8")->fetchAll();
  }

  include __DIR__ . '/../app/views/home.php';
  exit;
}

// route to module
if (!isset($meta[$m])) {
  http_response_code(404);
  echo "Ruta no encontrada.";
  exit;
}

if (!auth_can_access($m)) {
  http_response_code(403);
  echo "No tienes permisos para acceder a este módulo.";
  exit;
}

$def = $meta[$m];
if (!empty($def['module'])) {
  include __DIR__ . '/../app/modules/' . $def['module'] . '.php';
} else {
  include __DIR__ . '/../app/modules/generic.php';
}
