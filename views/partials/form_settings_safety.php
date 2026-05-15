<?php
/**
 * views/partials/form_settings_safety.php (V27.0)
 * Control panel for safety buffers and manual temperature overrides.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4; display: flex; flex-direction: column;">
    <div class="section-title">Ασφάλεια & Overrides</div>
    
    <!-- Safety Factors Row (Sensible) -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
        <div>
            <label>SF Ψύξης (Sens)</label>
            <input type="number" name="m_sf_cool" step="0.05" value="<?= $inputs['m_sf_cool'] ?? '1.10' ?>" onchange="this.form.submit()">
        </div>
        <div>
            <label>SF Θέρμανσης</label>
            <input type="number" name="m_sf_heat" step="0.05" value="<?= $inputs['m_sf_heat'] ?? '1.20' ?>" onchange="this.form.submit()">
        </div>
    </div>
    
    <!-- Latent Load & Internal Temperature Override -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
        <div>
            <label>SF Latent (Υγρασία)</label>
            <input type="number" name="m_latent" step="0.01" value="<?= $inputs['m_latent'] ?? '1.18' ?>" onchange="this.form.submit()">
        </div>
        <div>
            <label>Custom Tin °C</label>
            <input type="number" name="custom_tin" placeholder="Auto" value="<?= $inputs['custom_tin'] ?? '' ?>" onchange="this.form.submit()">
        </div>
    </div>

    <!-- Outdoor Temperature Override -->
    <label>Custom Tout °C (Εξωτερική)</label>
    <input type="number" name="custom_tout" placeholder="Auto" value="<?= $inputs['custom_tout'] ?? '' ?>" style="margin-bottom: 20px;" onchange="this.form.submit()">
    
    <!-- Primary Calculation Trigger -->
    <button type="submit" class="btn" style="flex-grow: 1; margin-top: auto; font-size: 1.4rem; background: var(--accent); padding: 18px; border-radius: 16px; box-shadow: 0 4px 15px var(--accent_low);">
        ΥΠΟΛΟΓΙΣΜΟΣ ⚡
    </button>
</div>
