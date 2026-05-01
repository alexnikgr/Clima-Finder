<?php
/**
 * views/results.php
 * Orchestrates the output display. (Print button removed).
 */
$isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling';
$final_tout = !empty($inputs['custom_tout']) 
    ? floatval($inputs['custom_tout']) 
    : $GLOBALS['CONSTANTS']['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$inputs['mode'] ?? 'cooling']['tdb'];
?>
<div class="hero box">
    <div style="display: flex; justify-content: space-between; align-items: stretch; gap: 25px;">
        
        <div style="flex: 1;">
            <?php include 'views/partials/hero_stats.php'; ?>
            <?php include 'views/partials/results_graph.php'; ?>
            
            <p style="font-size: 0.65rem; color: var(--label); margin-top: 35px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <strong>MODEL:</strong> V16.0 | <strong>CALCULATION:</strong> MVC ENGINE
            </p>
        </div>

        <?php include 'views/partials/technical_report.php'; ?>
    </div>
</div>

<?php include 'views/partials/legal_disclaimer.php'; ?>