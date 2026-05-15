<?php
/**
 * views/form.php (V27.0)
 * Orchestrates the input form sections and orientation loops.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<form action="index.php" method="POST" class="grid" style="grid-column: span 12; gap: 15px; margin-top:0;">
    
    <!-- GLOBAL SETTINGS (Top Row) -->
    <?php include 'views/partials/form_settings_basic.php'; ?>
    <?php include 'views/partials/form_settings_envelope.php'; ?>
    <?php include 'views/partials/form_settings_safety.php'; ?>

    <!-- SECTION DIVIDER -->
    <div style="grid-column: span 12; margin: 15px 0 5px 0;">
        <div class="section-title" style="border-bottom: 2px solid var(--accent_low); padding-bottom: 5px; color: #fff; opacity: 0.5;">
            ΑΝΑΛΥΣΗ ΚΕΛΥΦΟΥΣ ΑΝΑ ΠΡΟΣΑΝΑΤΟΛΙΣΜΟ
        </div>
    </div>

    <!-- ORIENTATION GRID -->
    <div style="grid-column: span 12; display: flex; flex-wrap: wrap; gap: 12px;">
        <?php 
        $orientations = [
            'north' => 'ΒΟΡΡΑΣ (North)', 
            'south' => 'ΝΟΤΟΣ (South)', 
            'east'  => 'ΑΝΑΤΟΛΗ (East)', 
            'west'  => 'ΔΥΣΗ (West)'
        ];

        foreach($orientations as $id => $label) {
            // Each wall row partial handles its own win_custom checkbox state
            include 'views/partials/form_wall_row.php';
        }
        ?>
    </div>
    
</form>
