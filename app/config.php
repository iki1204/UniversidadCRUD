<?php
return [
  'db' => [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'name' => getenv('DB_NAME') ?: 'boutique',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
  ],
  'app' => [
    'name' => 'Boutique ',
    'base_url' => '/BoutiqueCrud',
  ],
  'auth' => [
    'roles' => [
      'admin' => [
        'label' => 'Administrador',
        'modules' => '*',
        'write' => true,
      ],
      'developer' => [
        'label' => 'Desarrollador',
        'modules' => ['categoria', 'talla', 'producto','cliente','empleado','proveedor','ventas'],
        'write' => true,
      ],
      'supervisor' => [
        'label' => 'Supervisor',
        'modules' => ['cliente', 'ventas', 'detalle_venta', 'proveedor'],
        'write' => false,
      ],
    ],
    'default_role' => 'admin',
    'user_roles' => [
      'devBoutique' => 'developer',
      'supBoutique' => 'supervisor',
      'adminBoutique' => 'admin',
    ],
  ],
];
