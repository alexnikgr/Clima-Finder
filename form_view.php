<form action="index.php" method="POST" class="grid" style="grid-column: span 12; gap: 15px; margin-top:0;">
    <!-- COLUMN 1: ΒΑΣΙΚΑ -->
    <div class="box" style="grid-column: span 4;">
        <div class="section-title">Βασικές Παράμετροι</div>
        <label>Λειτουργία</label>
        <select name="mode" onchange="this.form.submit()">
            <option value="cooling" <?= (($inputs['mode'] ?? 'cooling') == 'cooling') ? 'selected' : '' ?>>Ψύξη</option>
            <option value="heating" <?= (($inputs['mode'] ?? '') == 'heating') ? 'selected' : '' ?>>Θέρμανση</option>
        </select>
        
        <label>Κλιματική Ζώνη</label>
        <select name="zone" onchange="this.form.submit()">
            <?php foreach($GLOBALS['CONSTANTS']['CLIMATE_ZONES'] as $id => $z): ?>
                <option value="<?= $id ?>" <?= (($inputs['zone'] ?? 'b') == $id) ? 'selected' : '' ?>><?= $z['label'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Παλαιότητα / Κανονισμός</label>
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

    <!-- COLUMN 2: ΟΡΟΦΗ & ΔΑΠΕΔΟ -->
    <div class="box" style="grid-column: span 4;">
        <div class="section-title">Οροφή & Δάπεδο</div>
        <label>Τύπος Οροφής</label>
        <select name="roof_type" onchange="this.form.submit()">
            <option value="terrace" <?= (($inputs['roof_type'] ?? 'terrace') == 'terrace') ? 'selected' : '' ?>>Δώμα</option>
            <option value="pitched" <?= (($inputs['roof_type'] ?? '') == 'pitched') ? 'selected' : '' ?>>Στέγη</option>
            <option value="heated_above" <?= (($inputs['roof_type'] ?? '') == 'heated_above') ? 'selected' : '' ?>>Θερμαινόμενος χώρος</option>
        </select>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
                <label>Μόνωση Οροφής</label>
                <select name="roof_ins">
                    <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $id => $l): ?>
                        <option value="<?= $id ?>" <?= (($inputs['roof_ins'] ?? 'none') == $id) ? 'selected' : '' ?>><?= $l['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Πάχος cm</label>
                <input type="number" name="roof_ins_depth" value="<?= $inputs['roof_ins_depth'] ?? '' ?>" placeholder="cm">
            </div>
        </div>

        <label>Χρώμα Οροφής (Απορροφητικότητα)</label>
        <select name="roof_color" onchange="this.form.submit()" style="margin-bottom:15px;">
            <?php foreach($GLOBALS['CONSTANTS']['ROOF_COLORS'] as $id => $c): ?>
                <option value="<?= $id ?>" <?= (($inputs['roof_color'] ?? 'medium') == $id) ? 'selected' : '' ?>><?= $c['label'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Τύπος Δαπέδου</label>
        <select name="floor_type" onchange="this.form.submit()">
            <option value="ground" <?= (($inputs['floor_type'] ?? 'ground') == 'ground') ? 'selected' : '' ?>>Επί εδάφους</option>
            <option value="pilotis" <?= (($inputs['floor_type'] ?? '') == 'pilotis') ? 'selected' : '' ?>>Pilotis</option>
            <option value="heated_below" <?= (($inputs['floor_type'] ?? '') == 'heated_below') ? 'selected' : '' ?>>Θερμαινόμενος χώρος</option>
        </select>
    </div>

    <!-- COLUMN 3: OVERRIDES & ACTION -->
    <div class="box" style="grid-column: span 4; display: flex; flex-direction: column;">
        <div class="section-title">Overrides & Ασφάλεια</div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
                <label>SF Ψύξης</label>
                <select name="m_sf_cool">
                    <?php for($i=1.0; $i<=1.5; $i+=0.05): $val = number_format($i,2); ?>
                        <option value="<?= $val ?>" <?= (($inputs['m_sf_cool'] ?? '1.10') == $val) ? 'selected' : '' ?>><?= $val ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label>SF Latent</label>
                <select name="m_latent">
                    <?php for($i=1.0; $i<=1.5; $i+=0.05): $val = number_format($i,2); ?>
                        <option value="<?= $val ?>" <?= (($inputs['m_latent'] ?? '1.30') == $val) ? 'selected' : '' ?>><?= $val ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <label>Custom Tout °C</label>
        <input type="number" name="custom_tout" placeholder="Auto" value="<?= $inputs['custom_tout'] ?? '' ?>" style="margin-bottom:10px;">
        
        <button type="submit" class="btn" style="flex-grow: 1; margin-top: auto; font-size: 1.4rem; background: var(--accent);">ΥΠΟΛΟΓΙΣΜΟΣ ⚡</button>
    </div>

    <!-- ORIENTATIONS: ONE LINE PER WALL -->
    <div style="grid-column: span 12; display: flex; flex-direction: column; gap: 8px;">
        <?php foreach(['north'=>'Βόρεια', 'south'=>'Νότια', 'east'=>'Ανατολική', 'west'=>'Δυτική'] as $id => $label): ?>
        <div class="box" style="display: flex; align-items: center; gap: 12px; padding: 12px 20px;">
            <div style="width: 80px; font-weight: 800; color: var(--accent); font-size: 0.75rem;"><?= $label ?></div>
            
            <select name="w_type_<?= $id ?>" onchange="this.form.submit()" style="width: 100px; margin-bottom:0;">
                <option value="external" <?= (($inputs["w_type_$id"] ?? 'external') == 'external') ? 'selected' : '' ?>>Εξωτ.</option>
                <option value="internal" <?= (($inputs["w_type_$id"] ?? '') == 'internal') ? 'selected' : '' ?>>Εσωτ.</option>
            </select>

            <input type="number" name="w_len_<?= $id ?>" step="0.1" value="<?= $inputs["w_len_$id"] ?? '' ?>" placeholder="m" style="width: 70px; margin-bottom:0;">

            <!-- ΕΠΙΠΛΕΟΝ ΜΟΝΩΣΗ ΑΝΑ ΤΟΙΧΟ -->
            <div style="display: flex; gap: 5px; align-items: flex-end; flex: 2; background: rgba(255,255,255,0.02); padding: 5px; border-radius: 10px;">
                <div style="flex: 2;">
                    <label style="font-size: 0.6rem;">Επιπλέον Μόνωση</label>
                    <select name="ins_mat_<?= $id ?>" style="margin-bottom:0; font-size: 0.8rem;">
                        <?php foreach($GLOBALS['CONSTANTS']['LAMBDA'] as $mat_id => $l): ?>
                            <option value="<?= $mat_id ?>" <?= (($inputs["ins_mat_$id"] ?? 'none') == $mat_id) ? 'selected' : '' ?>><?= $l['label'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="font-size: 0.6rem;">Πάχος cm</label>
                    <input type="number" name="ins_depth_<?= $id ?>" value="<?= $inputs["ins_depth_$id"] ?? '' ?>" placeholder="cm" style="margin-bottom:0; font-size: 0.8rem;">
                </div>
            </div>

            <!-- ΚΟΥΦΩΜΑΤΑ -->
            <div style="flex: 2; display: flex; gap: 5px;">
                <select name="frame_<?= $id ?>" style="margin-bottom:0; font-size: 0.8rem;">
                    <?php foreach($GLOBALS['CONSTANTS']['KOUFOMATA'] as $fid => $f): ?>
                        <option value="<?= $fid ?>" <?= (($inputs["frame_$id"] ?? 'alum') == $fid) ? 'selected' : '' ?>><?= $f['label'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="glass_<?= $id ?>" style="margin-bottom:0; font-size: 0.8rem;">
                    <?php foreach($GLOBALS['CONSTANTS']['TZAMI'] as $gid => $g): ?>
                        <option value="<?= $gid ?>" <?= (($inputs["glass_$id"] ?? 'double') == $gid) ? 'selected' : '' ?>><?= $g['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ΑΝΟΙΓΜΑΤΑ -->
            <div style="width: 140px; display: flex; gap: 5px;">
                <input type="number" name="win_std_<?= $id ?>" value="<?= $inputs["win_std_$id"] ?? '' ?>" placeholder="Std" style="margin-bottom:0;">
                <input type="number" name="win_patio_<?= $id ?>" value="<?= $inputs["win_patio_$id"] ?? '' ?>" placeholder="Μπαλκ." style="margin-bottom:0;">
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</form>
