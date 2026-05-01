<?php
/**
 * views/partials/hero_stats.php
 * Displays the main load results and PE estimate.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<?php if ($results['is_auto_square']): ?>
    <div style="background: rgba(255,255,255,0.08); display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 0.75rem; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.15);">
        💡 <strong>Αυτόματη Παραδοχή:</strong> Υπολογισμός τετράγωνου χώρου (4 πλευρές x <?= round($results['side_length'], 2) ?>m).
    </div>
<?php endif; ?>

<label style="color: var(--accent); font-weight: 800; letter-spacing: 1px;">
    <?= $isCooling ? 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΨΥΞΗΣ' : 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΘΕΡΜΑΝΣΗΣ' ?>
</label>
<h1 style="color: var(--accent); margin: 5px 0; font-size: 4.5rem; letter-spacing: -3px;">
    <?= number_format($results['btu'], 0) ?> <small style="font-size:1.5rem; opacity:0.6;">BTU/h</small>
</h1>
<div style="margin-top: 10px; font-weight: 600; opacity: 0.9; font-size: 1.1rem;">
    <?= number_format($results['kw'], 2) ?> kW | PE: <?= number_format($results['pe_estimate'], 1) ?> kWh/m²/y
</div>
