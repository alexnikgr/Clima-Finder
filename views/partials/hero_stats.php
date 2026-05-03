<?php
/**
 * views/partials/hero_stats.php (V22.0)
 * Logic: Primary result display and auto-geometry notification.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>

<!-- 1. NOTIFICATION: AUTO-SQUARE LOGIC -->
<?php if ($results['is_auto_square'] ?? false): ?>
    <div style="background: var(--accent_low); display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 30px; font-size: 0.7rem; margin-bottom: 25px; border: 1px solid var(--accent); color: #fff; font-weight: 600;">
        <span style="font-size: 1rem;">💡</span> 
        <span>ΑΥΤΟΜΑΤΗ ΠΑΡΑΔΟΧΗ: Υπολογισμός τετράγωνου χώρου (4 πλευρές x <?= round($results['side_length'], 2) ?>m).</span>
    </div>
<?php endif; ?>

<!-- 2. MAIN LOAD DISPLAY -->
<div style="margin-bottom: 5px;">
    <label style="color: var(--accent); font-weight: 900; letter-spacing: 2px; font-size: 0.7rem;">
        <?= $isCooling ? 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΨΥΞΗΣ' : 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΘΕΡΜΑΝΣΗΣ' ?>
    </label>
    
    <h1 style="color: var(--accent); margin: 0; line-height: 1;">
        <?= number_format($results['btu'], 0) ?> 
        <small style="font-size: 1.6rem; opacity: 0.5; font-weight: 400; letter-spacing: 0;">BTU/h</small>
    </h1>
</div>

<!-- 3. SECONDARY METRICS (kW & PE) -->
<div style="display: flex; align-items: center; gap: 15px; margin-top: 12px; font-weight: 700; font-size: 1.2rem; color: #fff; opacity: 0.9;">
    <span><?= number_format($results['kw'], 2) ?> <small style="font-size: 0.8rem; opacity: 0.6;">kW</small></span>
    
    <div style="width: 1px; height: 18px; background: rgba(255,255,255,0.2);"></div>
    
    <span style="font-size: 1rem; color: var(--label);">
        PE: <span style="color: #fff;"><?= number_format($results['pe_estimate'], 1) ?></span> 
        <small style="font-size: 0.65rem; opacity: 0.6; text-transform: none;">kWh/m²/y</small>
    </span>
</div>

<p style="font-size: 0.65rem; color: var(--label); margin-top: 10px; max-width: 400px; line-height: 1.4;">
    * Το φορτίο περιλαμβάνει προσαυξήσεις ασφαλείας (Sensible: <?= $inputs['m_sf_cool'] ?? '1.10' ?><?= $isCooling ? " / Latent: " . ($inputs['m_latent'] ?? '1.18') : "" ?>).
</p>
