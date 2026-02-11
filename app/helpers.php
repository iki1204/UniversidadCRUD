<?php
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function redirect(string $to): void {
  header("Location: $to");
  exit;
}

function url(string $path = ''): string {
  $config = require __DIR__ . '/config.php';
  $base = rtrim($config['app']['base_url'], '/');
  return $base . $path;
}

function flash_set(string $type, string $message): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function flash_get(): ?array {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}

function csrf_token(): string {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['_csrf'];
}
function csrf_check(): void {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $token = $_POST['_csrf'] ?? '';
  if (!$token || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
    http_response_code(403);
    echo "CSRF token inválido.";
    exit;
  }
}

function auth_user(): ?array {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  return $_SESSION['auth'] ?? null;
}

function auth_require(): void {
  if (auth_user()) return;
  flash_set('warning', 'Inicia sesión para continuar.');
  redirect(url('/public/login.php'));
}

function auth_role(): ?string {
  $user = auth_user();
  return $user['role'] ?? null;
}

function auth_permissions(): array {
  $config = require __DIR__ . '/config.php';
  return $config['auth']['roles'] ?? [];
}

function auth_can_access(?string $module): bool {
  if ($module === null || $module === '') return true;
  $role = auth_role();
  if (!$role) return false;
  $roles = auth_permissions();
  if (!isset($roles[$role])) return false;
  $allowed = $roles[$role]['modules'] ?? [];
  if ($allowed === '*') return true;
  return in_array($module, $allowed, true);
}

function auth_can_write(?string $module): bool {
  if (!auth_can_access($module)) return false;
  $role = auth_role();
  if (!$role) return false;
  $roles = auth_permissions();
  if (!isset($roles[$role])) return false;
  return !empty($roles[$role]['write']);
}

function auth_logout(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  unset($_SESSION['auth']);
}
