<?php
/**
 * views/results.php (V27.0)
 * Results Dashboard Orchestrator.
 * Handles the high-level layout for displaying the calculation output.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

// Logic: Determine mode for sub-partial coloring and labels
$isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling';
?>

<div class="hero box" style="grid-column: span 12; margin-bottom: 20px;">
    <!-- Main Results Flex Container -->
    <div style="display: flex; justify-content: space-between; align-items: stretch; gap: 30px; flex-wrap: wrap;">
        
        <!-- LEFT COLUMN: Key Metrics & Visualization -->
        <div style="flex: 1; min-width: 350px; display: flex; flex-direction: column; justify-content: space-between;">
            
            <div>
                <!-- Primary BTU/kW Display (hero_stats.php) -->
                <?php include 'views/partials/hero_stats.php'; ?>
                
                <!-- Sensitivity Analysis SVG Graph (results_graph.php) -->
                <?php include 'views/partials/results_graph.php'; ?>
            </div>

            <!-- Engine Stamp & Versioning -->
            <div style="font-size: 0.6rem; color: var(--label); margin-top: 30px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.08); text-transform: uppercase; letter-spacing: 1px;">
                <strong>MODEL:</strong> THERMAL PRO V27.0 | <strong>ENGINE:</strong> MVC HYBRID PHYSICS (M2-STABLE)
            </div>
        </div>

        <!-- RIGHT COLUMN: Detailed Technical Audit Terminal (technical_report.php) -->
        <div style="flex-shrink: 0;">
            <?php include 'views/partials/technical_report.php'; ?>
        </div>
        
    </div>
</div>
