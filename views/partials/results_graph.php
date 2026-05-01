<?php
/**
 * views/partials/results_graph.php
 * SVG chart showing BTU/kW vs Outdoor Temperature.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div style="margin-top: 30px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 20px; border: 1px solid var(--accent_low); max-width: 460px;">
    <label style="font-size: 0.65rem; margin-bottom: 20px; color: #fff; opacity: 0.7;">
        ΑΝΑΛΥΣΗ ΕΥΑΙΣΘΗΣΙΑΣ (BTU & kW)
    </label>
    <div style="position: relative; height: 160px;">
        <svg viewBox="0 0 400 160" style="width: 100%; height: 100%; overflow: visible;">
            <?php 
                $scenarios = [
                    ['btu' => $results['mild']['btu'], 'kw' => $results['mild']['kw'], 'x' => 40],
                    ['btu' => $results['btu'], 'kw' => $results['kw'], 'x' => 200],
                    ['btu' => $results['extreme']['btu'], 'kw' => $results['extreme']['kw'], 'x' => 360]
                ];
                $max_b = max($results['extreme']['btu'], $results['btu'], 1) * 1.2;
                $points = [];
                foreach($scenarios as $s) {
                    $y = 150 - ($s['btu'] / $max_b * 120);
                    $points[] = "{$s['x']},$y";
                }
            ?>
            <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="var(--accent)" stroke-width="4" stroke-linejoin="round" />
            <?php foreach($scenarios as $idx => $s): 
                $c = explode(',',$points[$idx]); 
                $color = ($idx == 2) ? '#ff453a' : 'var(--accent)';
            ?>
                <circle cx="<?= $s['x'] ?>" cy="<?= $c[1] ?>" r="5" fill="<?= $color ?>" />
                <text x="<?= $s['x'] ?>" y="<?= $c[1] - 25 ?>" fill="<?= $color ?>" font-size="10" text-anchor="middle" font-weight="800"><?= number_format($s['btu'], 0) ?></text>
                <text x="<?= $s['x'] ?>" y="<?= $c[1] - 12 ?>" fill="<?= $color ?>" font-size="8" text-anchor="middle"><?= number_format($s['kw'], 1) ?> kW</text>
            <?php endforeach; ?>
        </svg>
    </div>
    <div style="display: flex; justify-content: space-between; font-size: 0.65rem; margin-top: 15px; color: var(--label);">
        <span>Ήπιο</span><span style="color: var(--accent);">Standard</span><span style="color: #ff453a;">Stress Test</span>
    </div>
</div>
