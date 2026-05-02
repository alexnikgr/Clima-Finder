<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');
/**
 * views/partials/form_wall_row.php (V19.5)
 * Vertical Audit Card: Αφαίρεση χειροκίνητου πάχους - Αυτοματοποιημένη επιλογή κατασκευής.
 */
?>
<div class="box" style="flex: 1; min-width: 310px; display: flex; flex-direction: column; gap: 15px; border-top: 4px solid var(--accent); background: rgba(255,255,255,0.02);">
    
    <!-- HEADER: ΠΡΟΣΑΝΑΤΟΛΙΣΜΟΣ & ΤΟΠΟΘΕΣΙΑ -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--accent_low); padding-bottom: 10px;">
        <div style="font-weight: 900; color: var(--accent); letter-spacing: 1px; font-size: 1rem;"><?= $label ?></div>
        
        <select name="w_env_<?= $id ?>" style="width: auto; margin-bottom:0; font-size: 0.65rem; height: 40px; background: transparent; border: 1px solid var(--border);" onchange="this.form.submit()">
            <option value="external" <?= (($inputs["w_env_$id"] ?? 'external') == 'external') ? 'selected' : '' ?>>ΕΞΩΤΕΡΙΚΟΣ</option>
            <option value="internal" <?= (($inputs["w_env_$id"] ?? '') == 'internal') ? 'selected' : '' ?>>ΕΣΩΤΕΡΙΚΟΣ</option>
        </select>
    </div>

    <!-- ΕΠΙΛΟΓΗ ΚΑΤΑΣΚΕΥΗΣ (ΟΡΙΖΕΙ ΤΟ ΠΑΧΟΣ ΑΥΤΟΜΑΤΑ) -->
    <div style="background: var(--accent_low); padding: 10px; border-radius: 10px; border: 1px solid var(--accent);">
        <label style="color: var(--accent); font-weight: 800; font-size: 0.6rem; margin-bottom: 5px;">ΤΥΠΟΣ ΚΑΤΑΣΚΕΥΗΣ</label>
        <select name="w_build_<?= $id ?>" style="margin-bottom:0; font-size: 0.75rem; height: 40px; background: rgba(0,0,0,0.3); border: none; color: #fff; font-weight: 700;" onchange="this.form.submit()">
            <?php foreach($GLOBALS['CONSTANTS']['U_WALL_TYPES'] as $bt_id => $bt): ?>
                <option value="<?= $bt_id ?>" <?= (($inputs["w_build_$id"] ?? 'double') == $bt_id) ? 'selected' : '' ?>>
                    <?= strtoupper($bt['label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- SECTION 1: ΔΙΑΣΤΑΣΕΙΣ & ΠΡΟΣΘΕΤΗ ΜΟΝΩΣΗ -->
    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 12px;">
        <div class="section-title" style="font-size: 0.6rem; margin-bottom: 10px;">ΔΙΑΣΤΑΣΕΙΣ & ΠΡΟΣΘΕΤΗ ΜΟΝΩΣΗ</div>
        <label>ΜΗΚΟΣ M</label>
        <input type="number" name="w_len_<?= $id ?>" step="0.1" value="<?= $inputs["w_len_$id"] ?? '' ?>" placeholder="0.0" style="margin-bottom: 12px;">
        
        <label>ΥΛΙΚΟ ΠΡΟΣΘΕΤΗΣ ΜΟΝΩΣΗΣ</label>
        <select name="ins_mat_<?= $id ?>">
            <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $m_id => $l): ?>
                <option value="<?= $m_id ?>" <?= (($inputs["ins_mat_$id"] ?? 'none') == $m_id) ? 'selected' : '' ?>><?= $l['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="ins_depth_<?= $id ?>" value="<?= $inputs["ins_depth_$id"] ?? '' ?>" placeholder="Πάχος cm" style="margin-top: 5px;">
    </div>

    <!-- SECTION 2: ΑΝΟΙΓΜΑΤΑ -->
    <div style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
        <div class="section-title" style="font-size: 0.6rem; margin-bottom: 10px;">ΑΝΟΙΓΜΑΤΑ (U-Win)</div>
        <label>ΚΟΥΦΩΜΑ / ΤΖΑΜΙ</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
            <select name="frame_<?= $id ?>" style="font-size: 0.7rem;">
                <?php foreach($GLOBALS['CONSTANTS']['KOUFOMATA'] as $f_id => $f): ?>
                    <option value="<?= $f_id ?>" <?= (($inputs["frame_$id"] ?? 'alum') == $f_id) ? 'selected' : '' ?>><?= $f['label'] ?></option>
                <?php endforeach; ?>
            </select>
            <select name="glass_<?= $id ?>" style="font-size: 0.7rem;">
                <?php foreach($GLOBALS['CONSTANTS']['TZAMI'] as $g_id => $g): ?>
                    <option value="<?= $g_id ?>" <?= (($inputs["glass_$id"] ?? 'double') == $g_id) ? 'selected' : '' ?>><?= $g['label'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <input type="number" name="win_std_<?= $id ?>" value="<?= $inputs["win_std_$id"] ?? '' ?>" placeholder="🪟 Παρ." style="text-align: center;">
            <input type="number" name="win_patio_<?= $id ?>" value="<?= $inputs["win_patio_$id"] ?? '' ?>" placeholder="🚪 Μπαλκ." style="text-align: center;">
        </div>
    </div>

    <!-- SECTION 3: ΣΚΙΑΣΗ & LAG -->
    <div style="padding: 5px;">
        <label>ΣΥΣΤΗΜΑ ΣΚΙΑΣΗΣ</label>
        <select name="shading_<?= $id ?>" style="margin-bottom:0; font-size: 0.75rem;">
            <?php foreach($GLOBALS['CONSTANTS']['SHADING_OPTIONS'] as $s_id => $s): ?>
                <option value="<?= $s_id ?>" <?= (($inputs["shading_$id"] ?? 'none') == $s_id) ? 'selected' : '' ?>><?= $s['label'] ?></option>
            <?php endforeach; ?>
        </select>
        
        <?php if(isset($results['wall_lags'][$id])): ?>
        <div style="margin-top: 15px; font-size: 0.65rem; color: var(--accent); font-weight: 800; text-align: center; background: var(--accent_low); padding: 8px; border-radius: 8px; border: 1px solid var(--accent);">
            ⏱ HEAT LAG: <?= $results['wall_lags'][$id] ?> HOURS
        </div>
        <?php endif; ?>
    </div>
</div>
