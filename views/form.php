<?php
/**
 * views/form.php
 * Orchestrates the input form sections.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<form action="index.php" method="POST" class="grid" style="grid-column: span 12; gap: 15px; margin-top:0;">
    
    <!-- Settings Row -->
    <?php include 'views/partials/form_settings_basic.php'; ?>
    <?php include 'views/partials/form_settings_envelope.php'; ?>
    <?php include 'views/partials/form_settings_safety.php'; ?>

	<!-- Orientations Section -->
	<div style="grid-column: span 12; display: flex; flex-wrap: wrap; gap: 12px; margin-top: 10px;">
		<?php 
		foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
			include 'views/partials/form_wall_row.php';
		}
		?>
	</div>
    
</form>
