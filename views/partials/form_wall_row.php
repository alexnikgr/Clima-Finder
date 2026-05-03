<?php
/**
 * views/partials/form_wall_row.php (V23.8)
 * FIXED: Restored missing 'selected' logic for all dropdown menus.
 */
 if (!defined('APP_RUNNING')) die('Direct access denied.');
 $is_c = (($inputs["win_custom_$id"] ?? '') == 'yes');
?>
<div class="box" style="flex: 1; min-width: 310px; display: flex; flex-direction: column; gap: 15px; border-top: 4px solid var(--accent); background: rgba(255,255,255,0.02); padding: 20px;">
    
    <!-- HEADER: ORIENTATION & ENVIRONMENT -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--accent_low); padding-bottom: 10px;">
        <div style="font-weight: 900; color: var(--accent); letter-spacing: 1px; font-size: 1rem; text-transform: uppercase;"><?= $label ?></div>
        
        <select name="w_env_<?= $id ?>" class="small-input" style="width: auto; height: 30px; font-size: 0.65rem;" >
            <?php foreach($GLOBALS['CONSTANTS']['ADJACENT_FACTORS'] as $env_id => $env): ?>
                <option value="<?= $env_id ?>" <?= (($inputs["w_env_$id"] ?? 'external') == $env_id) ? 'selected' : '' ?>>
                    <?= $env['label'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- CONSTRUCTION TYPE -->
    <div style="background: var(--accent_low); padding: 12px; border-radius: 12px; border: 1px solid var(--accent);">
        <label style="color: var(--accent); font-weight: 800; font-size: 0.6rem; margin-bottom: 5px;">ΤΥΠΟΣ ΚΑΤΑΣΚΕΥΗΣ</label>
        <select name="w_build_<?= $id ?>" style="margin-bottom:0; font-size: 0.75rem; height: 38px; background: rgba(0,0,0,0.4); border: none; color: #fff; font-weight: 700;" >
            <?php foreach($GLOBALS['CONSTANTS']['U_WALL_TYPES'] as $bt_id => $bt): ?>
                <option value="<?= $bt_id ?>" <?= (($inputs["w_build_$id"] ?? 'double') == $bt_id) ? 'selected' : '' ?>>
                    <?= strtoupper($bt['label']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- DIMENSIONS & INSULATION -->
    <div style="background: rgba(255,255,255,0.03); padding: 14px; border-radius: 14px;">
        <label>ΜΗΚΟΣ ΤΟΙΧΟΥ (m)</label>
        <input type="number" name="w_len_<?= $id ?>" step="0.1" value="<?= $inputs["w_len_$id"] ?? '' ?>" placeholder="0.0">
        
        <label>ΠΡΟΣΘΕΤΗ ΜΟΝΩΣΗ (cm)</label>
        <div style="display: flex; gap: 8px;">
            <select name="ins_mat_<?= $id ?>" style="flex: 2; margin-bottom:0;" >
                <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $m_id => $l): ?>
                    <option value="<?= $m_id ?>" <?= (($inputs["ins_mat_$id"] ?? 'none') == $m_id) ? 'selected' : '' ?>>
                        <?= $l['label'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="ins_depth_<?= $id ?>" value="<?= $inputs["ins_depth_$id"] ?? '' ?>" placeholder="0" style="width: 70px; margin-bottom:0; text-align: center;">
        </div>
    </div>

    <!-- OPENINGS -->
    <div style="background: rgba(0,0,0,0.2); padding: 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div class="section-title" style="font-size: 0.6rem; margin-bottom: 0;">ΑΝΟΙΓΜΑΤΑ</div>
            <label style="display: flex; align-items: center; gap: 5px; margin: 0; cursor: pointer; font-size: 0.55rem; color: var(--accent);">
                <input type="checkbox" name="win_custom_<?= $id ?>" value="yes" <?= $is_c ? 'checked' : '' ?>  style="width: auto; margin:0;"> CUSTOM m²
            </label>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
            <div>
                <label style="font-size: 0.55rem;">🪟 <?= $is_c ? 'ΠΑΡΑΘ. (m²)' : 'ΤΕΜ. ΠΑΡΑΘ.' ?></label>
                <input type="number" name="win_std_<?= $id ?>" step="<?= $is_c ? '0.1' : '1' ?>" value="<?= $inputs["win_std_$id"] ?? '0' ?>" style="text-align: center;">
            </div>
            <div>
                <label style="font-size: 0.55rem;">🚪 <?= $is_c ? 'ΜΠΑΛΚ. (m²)' : 'ΤΕΜ. ΜΠΑΛΚ.' ?></label>
                <input type="number" name="win_patio_<?= $id ?>" step="<?= $is_c ? '0.1' : '1' ?>" value="<?= $inputs["win_patio_$id"] ?? '0' ?>" style="text-align: center;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
            <select name="frame_<?= $id ?>" style="font-size: 0.7rem; height: 35px; margin-bottom:0;" >
                <?php foreach($GLOBALS['CONSTANTS']['KOUFOMATA'] as $f_id => $f): ?>
                    <option value="<?= $f_id ?>" <?= (($inputs["frame_$id"] ?? 'alum') == $f_id) ? 'selected' : '' ?>>
                        <?= $f['label'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="glass_<?= $id ?>" style="font-size: 0.7rem; height: 35px; margin-bottom:0;" >
                <?php foreach($GLOBALS['CONSTANTS']['TZAMI'] as $g_id => $g): ?>
                    <option value="<?= $g_id ?>" <?= (($inputs["glass_$id"] ?? 'double') == $g_id) ? 'selected' : '' ?>>
                        <?= $g['label'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- SHADING & LAG -->
    <div style="margin-top: auto;">
        <label>ΣΥΣΤΗΜΑ ΣΚΙΑΣΗΣ</label>
        <select name="shading_<?= $id ?>"  style="height: 40px; margin-bottom:15px;">
            <?php foreach($GLOBALS['CONSTANTS']['SHADING_OPTIONS'] as $s_id => $s): ?>
                <option value="<?= $s_id ?>" <?= (($inputs["shading_$id"] ?? 'none') == $s_id) ? 'selected' : '' ?>>
                    <?= $s['label'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if(isset($results['wall_lags'][$id])): ?>
            <div style="font-size: 0.65rem; color: var(--accent); font-weight: 800; text-align: center; background: var(--accent_low); padding: 10px; border-radius: 10px; border: 1px solid var(--accent); text-transform: uppercase;">
                ⏱ ΘΕΡΜΙΚΗ ΥΣΤΕΡΗΣΗ (R-C): <?= $results['wall_lags'][$id] ?> ΩΡΕΣ
            </div>
        <?php endif; ?>
    </div>
</div>
