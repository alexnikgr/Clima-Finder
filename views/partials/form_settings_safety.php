<?php
/**
 * views/partials/form_settings_safety.php (V28.0 - Refactored)
 * Control panel for safety buffers and manual temperature overrides.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

$isCoolingMode = ($inputs['mode'] ?? 'cooling') === 'cooling';
?>
<div class="box" style="grid-column: span 4; display: flex; flex-direction: column;">
    <div class="section-title">Ασφάλεια & Overrides</div>
    
    <!-- Safety Factors Row (Sensible) -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
        <div>
            <label>SF Ψύξης (Sens)</label>
            <input type="number" name="m_sf_cool" step="0.05" value="<?= htmlspecialchars($inputs['m_sf_cool'] ?? '1.10') ?>">
        </div>
        <div>
            <label>SF Θέρμανσης</label>
            <input type="number" name="m_sf_heat" step="0.05" value="<?= htmlspecialchars($inputs['m_sf_heat'] ?? '1.20') ?>">
        </div>
    </div>
    
    <!-- Latent Load & Internal Temperature Override -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
        <div>
            <?php if ($isCoolingMode): ?>
                <label>SF Latent (Υγρασία)</label>
                <input type="number" name="m_latent" step="0.01" value="<?= htmlspecialchars($inputs['m_latent'] ?? '1.18') ?>">
            <?php else: ?>
                <label>SF Latent (Υγρασία)</label>
                <!-- Render a static placeholder block to keep structural spacing grid aligned -->
                <div style="background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid var(--border); padding: 12px; height: 46px; opacity: 0.35; font-size: 0.75rem; text-align: center; display: flex; align-items: center; justify-content: center;">
                    N/A (Μόνο Ψύξη)
                </div>
            <?php endif; ?>
        </div>
        <div>
            <label>Custom Tin °C</label>
            <input type="number" name="custom_tin" placeholder="Auto" value="<?= htmlspecialchars($inputs['custom_tin'] ?? '') ?>">
        </div>
    </div>

    <!-- Outdoor Temperature Override -->
    <label>Custom Tout °C (Εξωτερική)</label>
    <input type="number" name="custom_tout" placeholder="Auto" value="<?= htmlspecialchars($inputs['custom_tout'] ?? '') ?>" style="margin-bottom: 20px;">
    
    <!-- Primary Calculation Trigger -->
    <button type="submit" class="btn" style="flex-grow: 1; margin-top: auto; font-size: 1.4rem; background: var(--accent); padding: 18px; border-radius: 16px; box-shadow: 0 4px 15px var(--accent_low);">
        ΥΠΟΛΟΓΙΣΜΟΣ ⚡
    </button>
</div>
