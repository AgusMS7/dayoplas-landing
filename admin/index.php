<?php require __DIR__.'/core/config.php'; require_login(); ?>
<?php include __DIR__.'/partials/layout.php'; ?>
  <h2 class="h2">Panel</h2>
  <div class="cards">
    <div class="card stat">
      <div class="label">Formaciones activas</div>
      <div class="value"><?= (int)$pdo->query("SELECT COUNT(*) FROM formacion WHERE estado='A'")->fetchColumn() ?></div>
    </div>
    <div class="card stat">
      <div class="label">Tipos de formación</div>
      <div class="value"><?= (int)$pdo->query("SELECT COUNT(*) FROM tipo_formacion WHERE estado='A'")->fetchColumn() ?></div>
    </div>
    <div class="card stat">
      <div class="label">Usuarios activos</div>
      <div class="value"><?= (int)$pdo->query("SELECT COUNT(*) FROM users WHERE estado='ACTIVO'")->fetchColumn() ?></div>
    </div>
  </div>
<?php include __DIR__.'/partials/_end.php'; ?>
