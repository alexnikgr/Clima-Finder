<?php 
/**
 * views/partials/form_wall_openings.php
 * Logic: UI toggle between Piece Counting (TEM) and Area measurements (m2).
 */
if (!defined('APP_RUNNING')) die('Direct access denied.'); 
?>
<div style="background: rgba(0,0,0,0.2); padding: 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05);">
    <div class="section-title" style="font-size: 0.6rem; margin-bottom: 10px;">ΑΝΟΙΓΜΑΤΑ (ΚΟΥΦΩΜΑΤΑ)</div>
    
    <!-- Area vs Pieces Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
        <div>
            <label style="font-size: 0.55rem;">
                🪟 <?= $is_c ? 'ΠΑΡΑΘΥΡΑ (m²)' : 'ΤΕΜ. ΠΑΡΑΘΥΡΑ' ?>
            </label>
            <input type="number" 
                   name="win_std_<?= $id ?>" 
                   step="<?= $is_c ? '0.01' : '1' ?>" 
                   value="<?= $inputs["win_std_$id"] ?? '0' ?>" 
                   style="text-align: center;"
                   placeholder="<?= $is_c ? '0.00' : '0' ?>">
        </div>
        <div>
            <label style="font-size: 0.55rem;">
                🚪 <?= $is_c ? 'ΜΠΑΛΚΟΝΟΠ. (m²)' : 'ΤΕΜ. ΜΠΑΛΚΟΝΟΠ.' ?>
            </label>
            <input type="number" 
                   name="win_patio_<?= $id ?>" 
                   step="<?= $is_c ? '0.01' : '1' ?>" 
                   value="<?= $inputs["win_patio_$id"] ?? '0' ?>" 
                   style="text-align: center;"
                   placeholder="<?= $is_c ? '0.00' : '0' ?>">
        </div>
    </div>

    <!-- Framing & Glazing Specification -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
        <select name="frame_<?= $id ?>" style="font-size: 0.7rem; height: 35px; margin-bottom:0;" onchange="this.form.submit()">
            <?php foreach($GLOBALS['CONSTANTS']['KOUFOMATA'] as $f_id => $f): ?>
                <option value="<?= $f_id ?>" <?= (($inputs["frame_$id"] ?? 'alum') == $f_id) ? 'selected' : '' ?>><?= $f['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <select name="glass_<?= $id ?>" style="font-size: 0.7rem; height: 35px; margin-bottom:0;" onchange="this.form.submit()">
            <?php foreach($GLOBALS['CONSTANTS']['TZAMI'] as $g_id => $g): ?>
                <option value="<?= $g_id ?>" <?= (($inputs["glass_$id"] ?? 'double') == $g_id) ? 'selected' : '' ?>><?= $g['label'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
