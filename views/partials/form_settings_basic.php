<?php
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>

<div class="box" style="grid-column: span 4;">
    <div class="section-title">Ρυθμίσεις Μελέτης</div>
    <label>Λειτουργία</label>
    <select name="mode" onchange="this.form.submit()">
        <option value="cooling" <?= (($inputs['mode'] ?? 'cooling') == 'cooling') ? 'selected' : '' ?>>Ψύξη (Cooling)</option>
        <option value="heating" <?= (($inputs['mode'] ?? '') == 'heating') ? 'selected' : '' ?>>Θέρμανση (Heating)</option>
    </select>
    
    <label>Κλιματική Ζώνη</label>
    <select name="zone" onchange="this.form.submit()">
        <?php foreach($GLOBALS['CONSTANTS']['CLIMATE_ZONES'] as $id => $z): ?>
            <option value="<?= $id ?>" <?= (($inputs['zone'] ?? 'b') == $id) ? 'selected' : '' ?>><?= $z['label'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Κέλυφος (Παλαιότητα)</label>
    <select name="etos" onchange="this.form.submit()">
        <?php foreach($GLOBALS['CONSTANTS']['ETOS_LABELS'] as $id => $label): ?>
            <option value="<?= $id ?>" <?= (($inputs['etos'] ?? 'legacy') == $id) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
        <div><label>Επιφάνεια m²</label><input type="number" name="area" step="0.1" value="<?= $inputs['area'] ?? '' ?>"></div>
        <div><label>Ύψος m</label><input type="number" name="height" step="0.1" value="<?= $inputs['height'] ?? '' ?>"></div>
    </div>
</div>
