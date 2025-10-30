<?php require __DIR__.'/../core/config.php'; require_login(); ?>
<!doctype html><html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=APP_NAME?></title>
<link rel="stylesheet" href="<?=ASSETS_URL?>/admin.css">
</head><body>
<header class="topbar">
  <div class="brand"><?=APP_NAME?></div>
  <nav class="topnav">
    <span class="user">👤 <?=e(user()['nombre'] ?? 'Usuario')?></span>
    <a class="link" href="<?=BASE_URL?>/auth/logout.php">Salir</a>
  </nav>
</header>
<div class="wrap">
  <aside class="sidebar">
    <a class="item <?=active_class('/modules/formacion/')?>" href="<?=BASE_URL?>/modules/formacion/">Formaciones</a>
    <a class="item <?=active_class('/modules/tipo_formacion/')?>" href="<?=BASE_URL?>/modules/tipo_formacion/">Tipos de formación</a>
    <?php if (has_role('admin') || has_role('root')): ?>
      <a class="item <?=active_class('/modules/usuarios/')?>" href="<?=BASE_URL?>/modules/usuarios/">Usuarios</a>
    <?php endif; ?>
  </aside>
  <main class="main">
    <?php if ($m=flash('ok')): ?><div class="alert ok"><?=$m?></div><?php endif; ?>
    <?php if ($m=flash('err')): ?><div class="alert err"><?=$m?></div><?php endif; ?>
