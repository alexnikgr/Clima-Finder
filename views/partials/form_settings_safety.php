<?php
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4; display: flex; flex-direction: column;">
    <div class="section-title">Ασφάλεια & Overrides</div>
    
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
        <div>
            <label>SF Ψύξης</label>
            <input type="number" name="m_sf_cool" step="0.05" value="<?= $inputs['m_sf_cool'] ?? '1.10' ?>">
        </div>
        <div>
            <label>SF Latent</label>
            <input type="number" name="m_latent" step="0.05" value="<?= $inputs['m_latent'] ?? '1.30' ?>">
        </div>
    </div>
    
    <label style="margin-top:10px;">Custom Tout °C (Override)</label>
    <input type="number" name="custom_tout" placeholder="Auto" value="<?= $inputs['custom_tout'] ?? '' ?>">
    
    <button type="submit" class="btn" style="flex-grow: 1; margin-top: auto; font-size: 1.4rem; background: var(--accent); padding: 18px; border-radius: 16px;">
        ΥΠΟΛΟΓΙΣΜΟΣ ⚡
    </button>
</div>
