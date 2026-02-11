<?php
require_once __DIR__ . '/../app/helpers.php';

$config = require __DIR__ . '/../app/config.php';

if (auth_user()) {
  redirect(url('/public/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__ . '/../app/db.php';
  csrf_check();
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    flash_set('danger', 'Completa usuario y contraseña.');
    redirect(url('/public/login.php'));
  }

  $role = $config['auth']['user_roles'][$username] ?? ($config['auth']['default_role'] ?? null);
  if (!$role || empty($config['auth']['roles'][$role])) {
    flash_set('danger', 'Rol inválido para el panel.');
    redirect(url('/public/login.php'));
  }

  $stmt = $pdo->prepare("SELECT User, Host FROM mysql.user WHERE User = ? LIMIT 1");
  $stmt->execute([$username]);
  $dbUser = $stmt->fetch();
  if (!$dbUser) {
    flash_set('danger', 'Usuario no encontrado en mysql.user.');
    redirect(url('/public/login.php'));
  }

  $db = $config['db'];
  $dsn = "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  try {
    new PDO($dsn, $username, $password, $options);
  } catch (PDOException $e) {
    flash_set('danger', 'Credenciales inválidas.');
    redirect(url('/public/login.php'));
  }

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['auth'] = [
    'user' => $username,
    'role' => $role,
    'role_label' => $config['auth']['roles'][$role]['label'] ?? $role,
    'db_user' => $username,
    'db_pass' => $password,
  ];

  flash_set('success', 'Bienvenido, ' . $username . '.');
  redirect(url('/public/index.php'));
}

include __DIR__ . '/../app/views/login.php';
