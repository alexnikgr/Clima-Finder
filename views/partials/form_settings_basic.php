<?php
/**
 * views/partials/form_settings_basic.php (V28.0 - Refactored)
 * Basic parameters: Mode, Climate Zone, Building Era, and Geometry.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 4;">
    <div class="section-title">Ρυθμίσεις Μελέτης</div>
    
    <!-- Mode: Cooling vs Heating -->
    <label>Λειτουργία Συστήματος</label>
    <select name="mode" onchange="this.form.submit()">
        <option value="cooling" <?= (($inputs['mode'] ?? 'cooling') == 'cooling') ? 'selected' : '' ?>>Ψύξη (Cooling Mode)</option>
        <option value="heating" <?= (($inputs['mode'] ?? '') == 'heating') ? 'selected' : '' ?>>Θέρμανση (Heating Mode)</option>
    </select>
    
    <!-- Climate Zone (Location) -->
    <label>Κλιματική Ζώνη (Τοποθεσία)</label>
    <select name="zone" onchange="this.form.submit()">
        <?php foreach($this->c['CLIMATE_ZONES'] as $id => $z): ?>
            <option value="<?= $id ?>" <?= (($inputs['zone'] ?? 'b') == $id) ? 'selected' : '' ?>><?= htmlspecialchars($z['label']) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Building Era (KENAK Category) -->
    <label>Κέλυφος (Παλαιότητα Κτιρίου)</label>
    <select name="etos" onchange="this.form.submit()">
        <?php foreach($this->c['ETOS_LABELS'] as $id => $label): ?>
            <option value="<?= $id ?>" <?= (($inputs['etos'] ?? 'legacy') == $id) ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
    </select>

    <!-- Space Geometry -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top: 5px;">
        <div>
            <label>Επιφάνεια m²</label>
            <input type="number" 
                   name="area" 
                   step="0.1" 
                   value="<?= htmlspecialchars($inputs['area'] ?? '') ?>" 
                   required 
                   placeholder="0.0"> <!-- Instant submit removed to secure fluid keyboard input loops -->
        </div>
        <div>
            <label>Καθαρό Ύψος m</label>
            <input type="number" 
                   name="height" 
                   step="0.1" 
                   value="<?= htmlspecialchars($inputs['height'] ?? '3.0') ?>" 
                   placeholder="3.0">
        </div>
    </div>
    
    <p style="font-size: 0.6rem; color: var(--label); margin-top: 10px; line-height: 1.2;">
        * Τα γεωμετρικά δεδομένα είναι απαραίτητα για τον υπολογισμό του όγκου και των απωλειών.
    </p>
</div>
