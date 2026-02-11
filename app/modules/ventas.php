<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers.php';

$title = "Ventas";
$action = $_GET['a'] ?? 'list';
$id = $_GET['id'] ?? null;
$canWrite = auth_can_write('ventas');

csrf_check();

if (!$canWrite && in_array($action, ['create', 'edit', 'delete'], true)) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'delete') {
    http_response_code(403);
    echo "No tienes permisos para modificar ventas.";
    exit;
  }
  flash_set('warning', 'No tienes permisos para crear o editar ventas.');
  redirect(url("/public/index.php?m=ventas"));
}

function clients(PDO $pdo): array {
  return $pdo->query("SELECT CLIENTE_ID AS id, CONCAT(NOMBRE,' ',APELLIDO) AS label FROM CLIENTE ORDER BY APELLIDO")->fetchAll();
}
function employees(PDO $pdo): array {
  return $pdo->query("SELECT EMPLEADO_ID AS id, CONCAT(NOMBRE,' ',APELLIDO) AS label FROM EMPLEADO ORDER BY APELLIDO")->fetchAll();
}
function products(PDO $pdo): array {
  return $pdo->query("SELECT p.PRODUCTO_ID AS id,
                             CONCAT(p.CODIGO,' - ',p.DESCRIPCION,' (Talla: ',t.DESCRIPCION,', Stock: ',p.STOCK,')') AS label,
                             p.PRECIO AS precio,
                             p.STOCK AS stock
                      FROM PRODUCTO p
                      JOIN TALLA t ON t.TALLA_ID = p.TALLA_ID
                      ORDER BY p.DESCRIPCION")->fetchAll();
}

if ($action === 'delete' && $id) {
  // Restock before delete (simple approach)
  $pdo->beginTransaction();
  try {
    $d = $pdo->prepare("SELECT PRODUCTO_ID, CANTIDAD FROM DETALLE_VENTA WHERE VENTA_ID=?");
    $d->execute([$id]);
    foreach ($d->fetchAll() as $row) {
      $u = $pdo->prepare("UPDATE PRODUCTO SET STOCK = STOCK + ? WHERE PRODUCTO_ID=?");
      $u->execute([$row['CANTIDAD'], $row['PRODUCTO_ID']]);
    }
    $pdo->prepare("DELETE FROM DETALLE_VENTA WHERE VENTA_ID=?")->execute([$id]);
    $pdo->prepare("DELETE FROM VENTAS WHERE VENTA_ID=?")->execute([$id]);
    $pdo->commit();
    flash_set('success','Venta eliminada (y stock restaurado).');
  } catch (Throwable $e) {
    $pdo->rollBack();
    flash_set('danger','No se pudo eliminar: '.$e->getMessage());
  }
  redirect(url("/public/index.php?m=ventas"));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($action === 'create') {
    $cliente_id = (int)($_POST['CLIENTE_ID'] ?? 0);
    $empleado_id = (int)($_POST['EMPLEADO_ID'] ?? 0);
    $fecha = $_POST['FECHA'] ?? date('Y-m-d\TH:i');
    $estado = trim($_POST['ESTADO'] ?? 'COMPLETADA');
    $metodo = trim($_POST['METODO_PAGO'] ?? 'EFECTIVO');

    $items = $_POST['items'] ?? [];
    $items = array_values(array_filter($items, fn($it)=>!empty($it['PRODUCTO_ID']) && (int)$it['CANTIDAD']>0));
    if (!$cliente_id || !$empleado_id || count($items)===0) {
      flash_set('danger','Completa cliente, empleado y al menos 1 producto.');
      redirect(url("/public/index.php?m=ventas&a=create"));
    }

    $pdo->beginTransaction();
    try {
      $total = 0.0;

      // Insert venta first with total 0; update later
      $fecha_db = date('Y-m-d H:i:s', strtotime($fecha));
      $stmt = $pdo->prepare("INSERT INTO VENTAS (CLIENTE_ID, EMPLEADO_ID, FECHA, TOTAL, ESTADO, METODO_PAGO) VALUES (?,?,?,?,?,?)");
      $stmt->execute([$cliente_id, $empleado_id, $fecha_db, 0, $estado, $metodo]);
      $venta_id = (int)$pdo->lastInsertId();

      foreach ($items as $it) {
        $producto_id = (int)$it['PRODUCTO_ID'];
        $cantidad = (int)$it['CANTIDAD'];
        $precio = (float)$it['PRECIO'];

        // validate stock
        $p = $pdo->prepare("SELECT STOCK, PRECIO FROM PRODUCTO WHERE PRODUCTO_ID=? FOR UPDATE");
        $p->execute([$producto_id]);
        $prod = $p->fetch();
        if (!$prod) throw new Exception("Producto inválido: $producto_id");
        if ($precio <= 0) $precio = (float)$prod['PRECIO'];
        if ((int)$prod['STOCK'] < $cantidad) throw new Exception("Stock insuficiente para producto $producto_id");

        $pdo->prepare("INSERT INTO DETALLE_VENTA (VENTA_ID, PRODUCTO_ID, CANTIDAD, PRECIO) VALUES (?,?,?,?)")
            ->execute([$venta_id, $producto_id, $cantidad, $precio]);

        $pdo->prepare("UPDATE PRODUCTO SET STOCK = STOCK - ? WHERE PRODUCTO_ID=?")->execute([$cantidad, $producto_id]);
        $total += $cantidad * $precio;
      }

      $pdo->prepare("UPDATE VENTAS SET TOTAL=? WHERE VENTA_ID=?")->execute([$total, $venta_id]);

      $pdo->commit();
      flash_set('success','Venta creada. Total: $'.number_format($total,2));
      redirect(url("/public/index.php?m=ventas&a=view&id=$venta_id"));
    } catch (Throwable $e) {
      $pdo->rollBack();
      flash_set('danger','Error: '.$e->getMessage());
      redirect(url("/public/index.php?m=ventas&a=create"));
    }
  }

  if ($action === 'edit' && $id) {
    $estado = trim($_POST['ESTADO'] ?? '');
    $metodo = trim($_POST['METODO_PAGO'] ?? '');
    $pdo->prepare("UPDATE VENTAS SET ESTADO=?, METODO_PAGO=? WHERE VENTA_ID=?")->execute([$estado, $metodo, $id]);
    flash_set('success','Venta actualizada.');
    redirect(url("/public/index.php?m=ventas&a=view&id=$id"));
  }
}

// Views data
$list = [];
$venta = null;
$detalle = [];
$stats = null;

if ($action === 'list') {
  $sql = "SELECT v.*, 
            CONCAT(c.NOMBRE,' ',c.APELLIDO) AS CLIENTE,
            CONCAT(e.NOMBRE,' ',e.APELLIDO) AS EMPLEADO
          FROM VENTAS v
          JOIN CLIENTE c ON c.CLIENTE_ID=v.CLIENTE_ID
          JOIN EMPLEADO e ON e.EMPLEADO_ID=v.EMPLEADO_ID
          ORDER BY v.VENTA_ID DESC";
  $list = $pdo->query($sql)->fetchAll();
}

if ($action === 'create') {
  $clients = clients($pdo);
  $employees = employees($pdo);
  $products = products($pdo);
}

if ($action === 'view' && $id) {
  $stmt = $pdo->prepare("SELECT v.*, 
            CONCAT(c.NOMBRE,' ',c.APELLIDO) AS CLIENTE,
            CONCAT(e.NOMBRE,' ',e.APELLIDO) AS EMPLEADO
          FROM VENTAS v
          JOIN CLIENTE c ON c.CLIENTE_ID=v.CLIENTE_ID
          JOIN EMPLEADO e ON e.EMPLEADO_ID=v.EMPLEADO_ID
          WHERE v.VENTA_ID=?");
  $stmt->execute([$id]);
  $venta = $stmt->fetch();
  if (!$venta) { http_response_code(404); echo "Venta no encontrada."; exit; }

  $d = $pdo->prepare("SELECT d.*, p.CODIGO, p.DESCRIPCION
                      FROM DETALLE_VENTA d
                      JOIN PRODUCTO p ON p.PRODUCTO_ID=d.PRODUCTO_ID
                      WHERE d.VENTA_ID=?
                      ORDER BY d.DETALLE_ID");
  $d->execute([$id]);
  $detalle = $d->fetchAll();
}

if ($action === 'edit' && $id) {
  $stmt = $pdo->prepare("SELECT * FROM VENTAS WHERE VENTA_ID=?");
  $stmt->execute([$id]);
  $venta = $stmt->fetch();
  if (!$venta) { http_response_code(404); echo "Venta no encontrada."; exit; }
}

include __DIR__ . '/../views/ventas.php';
