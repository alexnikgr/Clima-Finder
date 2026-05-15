<?php
/**
 * views/partials/results_graph.php (V27.0)
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
        <label style="font-size: 0.65rem; color: #fff; opacity: 0.7; margin: 0; font-weight: 800; letter-spacing: 1px;">
            ΑΝΑΛΥΣΗ ΕΥΑΙΣΘΗΣΙΑΣ ΦΟΡΤΙΟΥ
        </label>
        <span style="font-size: 0.55rem; color: var(--accent); font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
            BTU vs Temp
        </span>
    </div>

    <div style="position: relative; height: 160px;">
        <svg viewBox="0 0 400 160" style="width: 100%; height: 100%; overflow: visible;">
            <?php 
                // Map results from Controller's scenario runner to X-coordinates
                $scenarios = [
                    ['btu' => $results['mild']['btu'] ?? 0,    'kw' => $results['mild']['kw'] ?? 0,    'x' => 40],
                    ['btu' => $results['btu'] ?? 0,            'kw' => $results['kw'] ?? 0,            'x' => 200],
                    ['btu' => $results['extreme']['btu'] ?? 0, 'kw' => $results['extreme']['kw'] ?? 0, 'x' => 360]
                ];

                // Scaling: Ensure the highest BTU fits with a 25% top margin
                $max_b = max($results['extreme']['btu'] ?? 1, $results['btu'] ?? 1, 1) * 1.25;
                $points = [];
                
                foreach($scenarios as $s) {
                    // Y-axis inversion (SVG 0 is top)
                    $y = 150 - ($s['btu'] / $max_b * 120);
                    $points[] = "{$s['x']},$y";
                }
            ?>
            
            <!-- Baseline Grid Line -->
            <line x1="20" y1="150" x2="380" y2="150" stroke="rgba(255,255,255,0.1)" stroke-width="1" stroke-dasharray="4" />
            
            <!-- Connection Polyline -->
            <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="var(--accent)" stroke-width="4" stroke-linejoin="round" />
            
            <!-- Data Points & Dynamic Labels -->
            <?php foreach($scenarios as $idx => $s): 
                $coords = explode(',', $points[$idx]); 
                $isStress = ($idx == 2);
                $pColor = $isStress ? '#ff453a' : 'var(--accent)'; 
            ?>
                <!-- Scenario Node -->
                <circle cx="<?= $s['x'] ?>" cy="<?= $coords[1] ?>" r="6" fill="<?= $pColor ?>" stroke="<?= $isCooling ? '#1c1c1e' : '#2c2c2e' ?>" stroke-width="2" />
                
                <!-- BTU Value Label -->
                <text x="<?= $s['x'] ?>" y="<?= $coords[1] - 28 ?>" fill="<?= $pColor ?>" font-size="11" text-anchor="middle" font-weight="900" font-family="sans-serif">
                    <?= number_format($s['btu'], 0) ?>
                </text>
                
                <!-- kW Value Label -->
                <text x="<?= $s['x'] ?>" y="<?= $coords[1] - 15 ?>" fill="<?= $pColor ?>" font-size="8" text-anchor="middle" opacity="0.8" font-family="sans-serif">
                    <?= number_format($s['kw'], 1) ?> kW
                </text>
            <?php endforeach; ?>
        </svg>
    </div>

    <!-- X-Axis Legend -->
    <div style="display: flex; justify-content: space-between; font-size: 0.6rem; margin-top: 15px; color: var(--label); font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
        <span style="width: 80px; text-align: left;">Ήπιο</span>
        <span style="color: var(--accent);">Standard</span>
        <span style="width: 80px; text-align: right; color: #ff453a;">Stress Test</span>
    </div>
</div>
