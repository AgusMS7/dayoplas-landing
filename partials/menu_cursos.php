<?php
// Partial: partials/menu_cursos.php
// Generates the main dropdown menu based on tipos/formaciones.
// Ensures $Modelo and $tipos are available when included from any page.

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Ensure model and connection are available
if (!isset($Modelo)) {
    if (!isset($host)) require_once __DIR__ . '/../conexion.php';
    if (!class_exists('ModelDaylo')) require_once __DIR__ . '/../model_daylo.php';
    $Modelo = new ModelDaylo($host ?? null, $db ?? null, $user ?? null, $pass ?? null);
}

$idioma = $idioma ?? ($_SESSION['idioma'] ?? 'es');
if (!isset($tipos) || empty($tipos)) {
    $tipos = $Modelo->obtenerTiposFormacion($idioma);
}
?>
<nav id="nav">
	<ul>
		<li><a class="detalle" href="index.php">INICIO</a></li>
		<?php foreach ($tipos as $tipo): if (!empty($tipo)):
			$formaciones = $Modelo->obtenerFormacionesPorTipo($tipo['id'], $idioma);
		?>
			<li>
				<a href="#<?= htmlspecialchars($tipo['clave']) ?>"><?php echo htmlspecialchars($tipo['titulo']); ?></a>
				<?php if (!empty($formaciones)): ?>
				<ul>
					<?php foreach ($formaciones as $f): ?>
						<li>
							<a href="detalle.php?slug=<?= urlencode($f['slug']) ?>" target="_blank"><?= htmlspecialchars($f['titulo']) ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</li>
		<?php endif; endforeach; ?>

		<li><a href="#" onclick="manejarContacto(event); return false;">CONTACTO</a></li>
		<li><a href="#" onclick="abrirModalNosotros(); return false;">NOSOTROS</a></li>

		<?php if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Administrador') : ?>
			<li><a href="admin/usuarios.php">Panel Usuarios</a></li>
			<li><a href="admin/cursos.php">Panel Cursos</a></li>
			<li><a href="admin/panel_crud.php" class="admin-crud-link"><i class="fas fa-cogs"></i> Panel CRUD</a></li>
		<?php endif; ?>
	</ul>
</nav>
