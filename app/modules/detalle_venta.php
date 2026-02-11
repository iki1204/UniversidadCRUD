<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

$title = "Detalle de Venta";
$action = $_GET['a'] ?? 'list';
$id = $_GET['id'] ?? null;
$venta_id = $_GET['venta_id'] ?? null;
$canWrite = auth_can_write('detalle_venta');

csrf_check();

if (!$canWrite && $action === 'delete') {
  http_response_code(403);
  echo "No tienes permisos para modificar el detalle de venta.";
  exit;
}

if ($action === 'delete' && $id) {
  $pdo->beginTransaction();
  try {
    $row = $pdo->prepare("SELECT * FROM DETALLE_VENTA WHERE DETALLE_ID=?");
    $row->execute([$id]);
    $d = $row->fetch();
    if ($d) {
      // restore stock and subtract total
      $pdo->prepare("UPDATE PRODUCTO SET STOCK = STOCK + ? WHERE PRODUCTO_ID=?")->execute([$d['CANTIDAD'], $d['PRODUCTO_ID']]);
      $pdo->prepare("UPDATE VENTAS SET TOTAL = TOTAL - (? * ?) WHERE VENTA_ID=?")->execute([$d['CANTIDAD'], $d['PRECIO'], $d['VENTA_ID']]);
      $pdo->prepare("DELETE FROM DETALLE_VENTA WHERE DETALLE_ID=?")->execute([$id]);
    }
    $pdo->commit();
    flash_set('success','Detalle eliminado (y totales ajustados).');
  } catch (Throwable $e) {
    $pdo->rollBack();
    flash_set('danger','Error: '.$e->getMessage());
  }
  redirect(url("/public/index.php?m=detalle_venta" . ($venta_id ? "&venta_id=$venta_id" : "")));
}

$list = [];
$params = [];
$where = "";

if ($venta_id) {
  $where = "WHERE d.VENTA_ID = ?";
  $params[] = $venta_id;
}

$sql = "SELECT d.*,
          CONCAT(p.CODIGO,' - ',p.DESCRIPCION,' (Talla: ',t.DESCRIPCION,')') AS PRODUCTO,
          v.FECHA AS FECHA_VENTA
        FROM DETALLE_VENTA d
        JOIN PRODUCTO p ON p.PRODUCTO_ID=d.PRODUCTO_ID
        JOIN TALLA t ON t.TALLA_ID=p.TALLA_ID
        JOIN VENTAS v ON v.VENTA_ID=d.VENTA_ID
        $where
        ORDER BY d.DETALLE_ID DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

include __DIR__ . '/../views/detalle_venta.php';
