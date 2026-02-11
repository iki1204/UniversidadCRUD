<?php
require_once __DIR__ . '/../app/helpers.php';

auth_logout();
flash_set('success', 'Sesión cerrada correctamente.');
redirect(url('/public/login.php'));
