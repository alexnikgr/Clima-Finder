<?php
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4;">
    <div class="section-title">Οριζόντια Στοιχεία</div>
    
    <label>Τύπος Οροφής</label>
    <select name="roof_type" onchange="this.form.submit()">
        <option value="terrace" <?= (($inputs['roof_type'] ?? 'terrace') == 'terrace') ? 'selected' : '' ?>>Δώμα (Terrace)</option>
        <option value="pitched" <?= (($inputs['roof_type'] ?? '') == 'pitched') ? 'selected' : '' ?>>Στέγη (Pitched)</option>
        <option value="heated_above" <?= (($inputs['roof_type'] ?? '') == 'heated_above') ? 'selected' : '' ?>>Θερμαινόμενος χώρος πάνω</option>
    </select>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px; margin-bottom: 10px;">
        <div>
            <label>Μόνωση Οροφής</label>
            <select name="roof_ins">
                <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $id_l => $l): ?>
                    <option value="<?= $id_l ?>" <?= (($inputs['roof_ins'] ?? 'none') == $id_l) ? 'selected' : '' ?>><?= $l['label'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>cm</label><input type="number" name="roof_ins_depth" value="<?= $inputs['roof_ins_depth'] ?? '' ?>" placeholder="0"></div>
    </div>

    <label>Χρώμα Οροφής (Απορροφητικότητα)</label>
    <select name="roof_color" onchange="this.form.submit()" style="margin-bottom: 15px;">
        <?php foreach($GLOBALS['CONSTANTS']['ROOF_COLORS'] as $id_c => $c): ?>
            <option value="<?= $id_c ?>" <?= (($inputs['roof_color'] ?? 'medium') == $id_c) ? 'selected' : '' ?>><?= $c['label'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Τύπος Δαπέδου</label>
    <select name="floor_type" onchange="this.form.submit()">
        <option value="ground" <?= (($inputs['floor_type'] ?? 'ground') == 'ground') ? 'selected' : '' ?>>Επί εδάφους (Ground)</option>
        <option value="pilotis" <?= (($inputs['floor_type'] ?? '') == 'pilotis') ? 'selected' : '' ?>>Pilotis / Ανοικτός χώρος</option>
        <option value="heated_below" <?= (($inputs['floor_type'] ?? '') == 'heated_below') ? 'selected' : '' ?>>Θερμαινόμενος χώρος κάτω</option>
    </select>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:10px;">
        <div>
            <label>Μόνωση Δαπέδου</label>
            <select name="floor_ins">
                <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $id_l => $l): ?>
                    <option value="<?= $id_l ?>" <?= (($inputs['floor_ins'] ?? 'none') == $id_l) ? 'selected' : '' ?>><?= $l['label'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div><label>cm</label><input type="number" name="floor_ins_depth" value="<?= $inputs['floor_ins_depth'] ?? '' ?>" placeholder="0"></div>
    </div>
</div>
