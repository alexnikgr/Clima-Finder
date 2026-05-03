<?php
/**
 * views/partials/form_settings_envelope.php (V22.0)
 * Logic: Horizontal heat transfer for Roof and Floor elements.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4;">
    <div class="section-title">Οριζόντια Στοιχεία</div>
    
    <!-- ROOF TYPE -->
    <label>Τύπος Οροφής / Στέγης</label>
    <select name="roof_type" >
        <option value="terrace" <?= (($inputs['roof_type'] ?? 'terrace') == 'terrace') ? 'selected' : '' ?>>Δώμα (Εκτεθειμένο)</option>
        <option value="pitched" <?= (($inputs['roof_type'] ?? '') == 'pitched') ? 'selected' : '' ?>>Στέγη (Κεραμίδια)</option>
        <option value="slab_under" <?= (($inputs['roof_type'] ?? '') == 'slab_under') ? 'selected' : '' ?>>Πλάκα κάτω από στέγη</option>
        <option value="heated_above" <?= (($inputs['roof_type'] ?? '') == 'heated_above') ? 'selected' : '' ?>>Θερμαινόμενος χώρος πάνω</option>
    </select>

    <!-- ROOF INSULATION -->
    <label>Μόνωση Οροφής & Πάχος (cm)</label>
    <div style="display: flex; gap: 8px; margin-bottom: 10px;">
        <select name="roof_ins" style="flex: 2; margin-bottom:0;">
            <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $id_l => $l): ?>
                <option value="<?= $id_l ?>" <?= (($inputs['roof_ins'] ?? 'none') == $id_l) ? 'selected' : '' ?>><?= $l['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="roof_ins_depth" value="<?= $inputs['roof_ins_depth'] ?? '' ?>" placeholder="cm" style="width: 70px; margin-bottom:0; text-align: center;">
    </div>

    <!-- ROOF COLOR (Albedo Factor) -->
    <label>Χρώμα Οροφής (Ακτινοβολία)</label>
    <select name="roof_color" >
        <?php foreach($GLOBALS['CONSTANTS']['ROOF_COLORS'] as $id_c => $c): ?>
            <option value="<?= $id_c ?>" <?= (($inputs['roof_color'] ?? 'medium') == $id_c) ? 'selected' : '' ?>><?= $c['label'] ?></option>
        <?php endforeach; ?>
    </select>

    <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 15px 0; padding-top: 15px;"></div>

    <!-- FLOOR TYPE -->
    <label>Τύπος Δαπέδου</label>
    <select name="floor_type" >
        <option value="ground" <?= (($inputs['floor_type'] ?? 'ground') == 'ground') ? 'selected' : '' ?>>Επί εδάφους (ISO 13370)</option>
        <option value="pilotis" <?= (($inputs['floor_type'] ?? '') == 'pilotis') ? 'selected' : '' ?>>Pilotis / Ανοικτός χώρος</option>
        <option value="heated_below" <?= (($inputs['floor_type'] ?? '') == 'heated_below') ? 'selected' : '' ?>>Θερμαινόμενος χώρος κάτω</option>
    </select>

    <!-- FLOOR INSULATION -->
    <label>Μόνωση Δαπέδου & Πάχος (cm)</label>
    <div style="display: flex; gap: 8px;">
        <select name="floor_ins" style="flex: 2; margin-bottom:0;">
            <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $id_l => $l): ?>
                <option value="<?= $id_l ?>" <?= (($inputs['floor_ins'] ?? 'none') == $id_l) ? 'selected' : '' ?>><?= $l['label'] ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="floor_ins_depth" value="<?= $inputs['floor_ins_depth'] ?? '' ?>" placeholder="cm" style="width: 70px; margin-bottom:0; text-align: center;">
    </div>
</div>
