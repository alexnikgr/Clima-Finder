<?php
/**
 * views/partials/form_wall_row.php (V27.0 - Refactored)
 * Handles individual wall orientation settings and m2 toggle state persistence.
 */
 if (!defined('APP_RUNNING')) die('Direct access denied.');
 
 // Logic: Identify if this specific orientation is in Custom m2 mode
 $is_c = (($inputs["win_custom_$id"] ?? 'no') === 'yes');
?>
<div class="box" style="flex: 1; min-width: 310px; display: flex; flex-direction: column; gap: 15px; border-top: 4px solid var(--accent); background: rgba(255,255,255,0.02); padding: 20px;">
    
    <!-- Orientation Header & m2 Toggle Fix -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--accent_low); padding-bottom: 10px;">
        <div style="font-weight: 900; color: var(--accent); letter-spacing: 1px; font-size: 1rem; text-transform: uppercase;">
            <?= $label ?>
        </div>
        
        <!-- FIX: Hidden input ensures state persists even when unchecked -->
        <label style="display: flex; align-items: center; gap: 5px; cursor: pointer; font-size: 0.55rem; color: var(--accent); font-weight: 800;">
            <input type="hidden" name="win_custom_<?= $id ?>" value="no">
            <input type="checkbox" 
                   name="win_custom_<?= $id ?>" 
                   value="yes" 
                   <?= $is_c ? 'checked' : '' ?> 
                   onchange="this.form.submit()" 
                   style="width: auto; margin:0;"> CUSTOM m²
        </label>
    </div>

    <!-- Adjacent Factor Selector -->
    <label style="font-size: 0.6rem; color: var(--label); margin-bottom: -10px;">ΕΠΑΦΗ ΤΟΙΧΟΥ</label>
    <select name="w_env_<?= $id ?>" class="small-input" style="width: 100%;" onchange="this.form.submit()">
        <?php foreach($GLOBALS['CONSTANTS']['ADJACENT_FACTORS'] as $env_id => $env): ?>
            <option value="<?= $env_id ?>" <?= (($inputs["w_env_$id"] ?? 'external') == $env_id) ? 'selected' : '' ?>><?= $env['label'] ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Wall Build Type -->
    <div style="background: var(--accent_low); padding: 12px; border-radius: 12px; border: 1px solid var(--accent);">
        <label style="color: var(--accent); font-weight: 800; font-size: 0.6rem; margin-bottom: 5px;">ΤΥΠΟΣ ΚΑΤΑΣΚΕΥΗΣ</label>
        <select name="w_build_<?= $id ?>" style="margin-bottom:0; font-size: 0.75rem; height: 38px; background: rgba(0,0,0,0.4); border: none; color: #fff; font-weight: 700;" onchange="this.form.submit()">
            <?php foreach($GLOBALS['CONSTANTS']['U_WALL_TYPES'] as $bt_id => $bt): ?>
                <option value="<?= $bt_id ?>" <?= (($inputs["w_build_$id"] ?? 'double') == $bt_id) ? 'selected' : '' ?>><?= strtoupper($bt['label']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Wall Length & Insulation -->
    <div style="background: rgba(255,255,255,0.03); padding: 14px; border-radius: 14px;">
        <label>ΜΗΚΟΣ ΤΟΙΧΟΥ (m)</label>
        <input type="number" name="w_len_<?= $id ?>" step="0.1" value="<?= $inputs["w_len_$id"] ?? '' ?>" placeholder="0.0">
        
        <label>ΠΡΟΣΘΕΤΗ ΜΟΝΩΣΗ (cm)</label>
        <div style="display: flex; gap: 8px;">
            <select name="ins_mat_<?= $id ?>" style="flex: 2; margin-bottom:0;" onchange="this.form.submit()">
                <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $m_id => $l): ?>
                    <option value="<?= $m_id ?>" <?= (($inputs["ins_mat_$id"] ?? 'none') == $m_id) ? 'selected' : '' ?>><?= $l['label'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="ins_depth_<?= $id ?>" value="<?= $inputs["ins_depth_$id"] ?? '' ?>" placeholder="0" style="width: 70px; margin-bottom:0; text-align: center;">
        </div>
    </div>

    <!-- Openings Section (Windows/Doors) -->
    <?php include 'views/partials/form_wall_openings.php'; ?>

    <!-- Shading & Lag Result -->
    <div style="margin-top: auto;">
        <label>ΣΥΣΤΗΜΑ ΣΚΙΑΣΗΣ</label>
        <select name="shading_<?= $id ?>" onchange="this.form.submit()" style="height: 40px; margin-bottom:15px;">
            <?php foreach($GLOBALS['CONSTANTS']['SHADING_OPTIONS'] as $s_id => $s): ?>
                <option value="<?= $s_id ?>" <?= (($inputs["shading_$id"] ?? 'none') == $s_id) ? 'selected' : '' ?>><?= $s['label'] ?></option>
            <?php endforeach; ?>
        </select>
        
        <?php if(isset($results['wall_lags'][$id])): ?>
            <div style="font-size: 0.65rem; color: var(--accent); font-weight: 800; text-align: center; background: var(--accent_low); padding: 10px; border-radius: 10px; border: 1px solid var(--accent); text-transform: uppercase;">
                ⏱ ΘΕΡΜΙΚΗ ΥΣΤΕΡΗΣΗ: <?= $results['wall_lags'][$id] ?> ΩΡΕΣ
            </div>
        <?php endif; ?>
    </div>
</div>
