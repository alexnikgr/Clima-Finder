<?php
/**
 * views/partials/results_graph.php (V22.0)
 * Logic: Inline SVG generation for Sensitivity Analysis.
 * Visualizes BTU/kW requirements across three temperature scenarios.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div style="margin-top: 35px; background: rgba(0,0,0,0.3); padding: 22px; border-radius: 24px; border: 1px solid var(--accent_low); max-width: 480px; box-shadow: inset 0 0 20px rgba(0,0,0,0.2);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <label style="font-size: 0.65rem; color: #fff; opacity: 0.7; margin: 0;">
            ΑΝΑΛΥΣΗ ΕΥΑΙΣΘΗΣΙΑΣ ΦΟΡΤΙΟΥ
        </label>
        <span style="font-size: 0.55rem; color: var(--accent); font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
            BTU vs Temp
        </span>
    </div>

    <div style="position: relative; height: 160px;">
        <svg viewBox="0 0 400 160" style="width: 100%; height: 100%; overflow: visible;">
            <?php 
                // Map scenarios to X-coordinates for the graph
                $scenarios = [
                    ['btu' => $results['mild']['btu'], 'kw' => $results['mild']['kw'], 'x' => 40],
                    ['btu' => $results['btu'], 'kw' => $results['kw'], 'x' => 200],
                    ['btu' => $results['extreme']['btu'], 'kw' => $results['extreme']['kw'], 'x' => 360]
                ];

                // Scaling logic: Find max value and add 20% buffer for labels
                $max_b = max($results['extreme']['btu'], $results['btu'], 1) * 1.25;
                $points = [];
                
                foreach($scenarios as $s) {
                    // SVG Y-axis is inverted (0 is top)
                    $y = 150 - ($s['btu'] / $max_b * 120);
                    $points[] = "{$s['x']},$y";
                }
            ?>
            
            <!-- Background Grid Line (Baseline) -->
            <line x1="20" y1="150" x2="380" y2="150" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="4" />
            
            <!-- Connection Line -->
            <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="var(--accent)" stroke-width="4" stroke-linejoin="round" />
            
            <!-- Data Points & Labels -->
            <?php foreach($scenarios as $idx => $s): 
                $coords = explode(',', $points[$idx]); 
                $isStress = ($idx == 2);
                $pColor = $isStress ? '#ff453a' : 'var(--accent)'; // Highlight Stress Test in Red
            ?>
                <!-- Scenario Point -->
                <circle cx="<?= $s['x'] ?>" cy="<?= $coords[1] ?>" r="6" fill="<?= $pColor ?>" stroke="<?= var_export($isCooling ? '#1c1c1e' : '#2c2c2e', true) ?>" stroke-width="2" />
                
                <!-- BTU Label -->
                <text x="<?= $s['x'] ?>" y="<?= $coords[1] - 28 ?>" fill="<?= $pColor ?>" font-size="11" text-anchor="middle" font-weight="900">
                    <?= number_format($s['btu'], 0) ?>
                </text>
                
                <!-- kW Label -->
                <text x="<?= $s['x'] ?>" y="<?= $coords[1] - 15 ?>" fill="<?= $pColor ?>" font-size="8" text-anchor="middle" opacity="0.8">
                    <?= number_format($s['kw'], 1) ?> kW
                </text>
            <?php endforeach; ?>
        </svg>
    </div>

    <!-- X-Axis Labels -->
    <div style="display: flex; justify-content: space-between; font-size: 0.6rem; margin-top: 15px; color: var(--label); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
        <span style="width: 80px; text-align: left;">Ήπιο</span>
        <span style="color: var(--accent);">Standard</span>
        <span style="width: 80px; text-align: right; color: #ff453a;">Stress Test</span>
    </div>
</div>
