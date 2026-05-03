<?php
/**
 * views/partials/form_settings_basic.php (V22.0)
 * Basic parameters: Mode, Climate Zone, Building Era, and Geometry.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4;">
    <div class="section-title">Ρυθμίσεις Μελέτης</div>
    
    <label>Λειτουργία Συστήματος</label>
    <select name="mode" >
        <option value="cooling" <?= (($inputs['mode'] ?? 'cooling') == 'cooling') ? 'selected' : '' ?>>Ψύξη (Cooling Mode)</option>
        <option value="heating" <?= (($inputs['mode'] ?? '') == 'heating') ? 'selected' : '' ?>>Θέρμανση (Heating Mode)</option>
    </select>
    
    <label>Κλιματική Ζώνη (Τοποθεσία)</label>
    <select name="zone" >
        <?php foreach($GLOBALS['CONSTANTS']['CLIMATE_ZONES'] as $id => $z): ?>
            <option value="<?= $id ?>" <?= (($inputs['zone'] ?? 'b') == $id) ? 'selected' : '' ?>><?= $z['label'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Κέλυφος (Παλαιότητα Κτιρίου)</label>
    <select name="etos" >
        <?php foreach($GLOBALS['CONSTANTS']['ETOS_LABELS'] as $id => $label): ?>
            <option value="<?= $id ?>" <?= (($inputs['etos'] ?? 'legacy') == $id) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Geometry Section -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top: 5px;">
        <div>
            <label>Επιφάνεια m²</label>
            <input type="number" name="area" step="0.1" value="<?= $inputs['area'] ?? '' ?>" required placeholder="0.0">
        </div>
        <div>
            <label>Καθαρό Ύψος m</label>
            <input type="number" name="height" step="0.1" value="<?= $inputs['height'] ?? '3.0' ?>" placeholder="3.0">
        </div>
    </div>
    
    <p style="font-size: 0.6rem; color: var(--label); margin-top: 10px; line-height: 1.2;">
        * Η επιφάνεια είναι απαραίτητη για την εκκίνηση των υπολογισμών.
    </p>
</div>
