<?php
/**
 * views/partials/form_wall_row.php (V16.9 - Vertical Audit Card)
 * High-density vertical layout for structural and bioclimatic inputs.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 15px; border-top: 4px solid var(--accent);">
    
    <!-- HEADER: ORIENTATION & TYPE -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--accent_low); padding-bottom: 10px;">
        <div style="font-weight: 900; color: var(--accent); letter-spacing: 1px;"><?= $label ?></div>
        <select name="w_type_<?= $id ?>" style="width: auto; margin-bottom:0; font-size: 0.7rem; height: 40px;" onchange="this.form.submit()">
            <option value="external" <?= (($inputs["w_type_$id"] ?? 'external') == 'external') ? 'selected' : '' ?>>Εξωτερικός</option>
            <option value="internal" <?= (($inputs["w_type_$id"] ?? '') == 'internal') ? 'selected' : '' ?>>Εσωτερικός</option>
        </select>
    </div>

    <!-- SECTION 1: STRUCTURE -->
    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 12px;">
        <div class="section-title" style="font-size: 0.6rem; margin-bottom: 10px;">ΔΟΜΗ & ΜΟΝΩΣΗ</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
            <div>
                <label>Μήκος m</label>
                <input type="number" name="w_len_<?= $id ?>" step="0.1" value="<?= $inputs["w_len_$id"] ?? '' ?>" placeholder="0.0">
            </div>
            <div>
                <label>Πάχος m</label>
                <input type="number" name="w_thick_<?= $id ?>" step="0.01" value="<?= $inputs["w_thick_$id"] ?? $GLOBALS['CONSTANTS']['PHYSICS']['w_thick_default'] ?>">
            </div>
        </div>
        <label>Υλικό Μόνωσης</label>
        <select name="ins_mat_<?= $id ?>">
            <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $m_id => $l): ?>
                <option value="<?= $m_id ?>" <?= (($inputs["ins_mat_$id"] ?? 'none') == $m_id) ? 'selected' : '' ?>><?= $l['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <label>Πάχος Μόνωσης (cm)</label>
        <input type="number" name="ins_depth_<?= $id ?>" value="<?= $inputs["ins_depth_$id"] ?? '' ?>" placeholder="0">
    </div>

    <!-- SECTION 2: GLAZING -->
    <div style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
        <div class="section-title" style="font-size: 0.6rem; margin-bottom: 10px;">ΑΝΟΙΓΜΑΤΑ (U-Win)</div>
        <label>Κούφωμα</label>
        <select name="frame_<?= $id ?>">
            <?php foreach($GLOBALS['CONSTANTS']['KOUFOMATA'] as $f_id => $f): ?>
                <option value="<?= $f_id ?>" <?= (($inputs["frame_$id"] ?? 'alum') == $f_id) ? 'selected' : '' ?>><?= $f['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <label>Υαλοπίνακας</label>
        <select name="glass_<?= $id ?>">
            <?php foreach($GLOBALS['CONSTANTS']['TZAMI'] as $g_id => $g): ?>
                <option value="<?= $g_id ?>" <?= (($inputs["glass_$id"] ?? 'double') == $g_id) ? 'selected' : '' ?>><?= $g['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
            <div>
                <label>🪟 Παράθυρα</label>
                <input type="number" name="win_std_<?= $id ?>" value="<?= $inputs["win_std_$id"] ?? '' ?>" placeholder="0" style="text-align: center;">
            </div>
            <div>
                <label>🚪 Μπαλκ/τες</label>
                <input type="number" name="win_patio_<?= $id ?>" value="<?= $inputs["win_patio_$id"] ?? '' ?>" placeholder="0" style="text-align: center;">
            </div>
        </div>
    </div>

    <!-- SECTION 3: BIOCLIMATIC -->
    <div style="padding: 5px;">
        <label>Σύστημα Σκίασης</label>
        <select name="shading_<?= $id ?>" style="margin-bottom:0;">
            <?php foreach($GLOBALS['CONSTANTS']['SHADING_OPTIONS'] as $s_id => $s): ?>
                <option value="<?= $s_id ?>" <?= (($inputs["shading_$id"] ?? 'none') == $s_id) ? 'selected' : '' ?>><?= $s['label'] ?></option>
            <?php endforeach; ?>
        </select>
        
        <?php if(isset($results['wall_lags'][$id])): ?>
        <div style="margin-top: 15px; font-size: 0.65rem; color: var(--accent); font-weight: 800; text-align: center; background: var(--accent_low); padding: 8px; border-radius: 8px;">
            ⏱ HEAT DELAY: <?= $results['wall_lags'][$id] ?> HOURS
        </div>
        <?php endif; ?>
    </div>
</div>
