<?php 
/**
 * THERMAL CALCULATOR - RESULTS VIEW (V9.7)
 * Full Technical Report with Roof Color & Per-Wall Extra Insulation.
 */

if ($results && $results['btu'] > 0): 
    $isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling';
    $limits = $GLOBALS['CONSTANTS']['KENAK_2017_LIMITS'][$inputs['zone'] ?? 'b'];
?>
<div class="hero box" style="grid-column: span 12; margin-bottom: 20px; border-color: var(--accent);">
    <div style="display: flex; justify-content: space-between; align-items: stretch; gap: 25px;">
        <div style="flex: 1;">
            
            <?php if ($results['is_auto_square']): ?>
                <div style="background: rgba(255,255,255,0.08); display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 0.75rem; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.15);">
                    💡 <strong>Αυτόματη Παραδοχή:</strong> Υπολογισμός τετράγωνου χώρου (4 πλευρές x <?= round($results['side_length'], 2) ?>m).
                </div>
            <?php endif; ?>

            <label style="color: var(--accent); font-weight: 800; font-size: 0.8rem; letter-spacing: 1px;">
                <?= $isCooling ? 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΨΥΞΗΣ' : 'ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ ΘΕΡΜΑΝΣΗΣ' ?>
            </label>
            <h1 style="color: var(--accent); margin: 5px 0;">
                <?= number_format($results['btu'], 0) ?> <small style="font-size:1.5rem; opacity:0.6;">BTU/h</small>
            </h1>
            <div style="margin-top: 10px; font-weight: 600; opacity: 0.9; font-size: 1.1rem;">
                <?= number_format($results['kw'], 2) ?> kW | PE: <?= number_format($results['pe_estimate'], 1) ?> kWh/m²/y
            </div>
            
            <button onclick="window.print()" class="btn" style="width: auto; padding: 12px 25px; font-size: 0.85rem; background: var(--accent); margin-top: 25px;">
                ΕΚΤΥΠΩΣΗ ΑΝΑΦΟΡΑΣ 📄
            </button>

            <p style="font-size: 0.7rem; color: var(--label); margin-top: 25px; line-height: 1.5; max-width: 450px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <strong>ΝΟΜΙΚΗ ΣΗΜΕΙΩΣΗ:</strong> Ο παρών υπολογισμός αποτελεί τεχνική εκτίμηση βάσει παραδοχών και δεν υποκαθιστά επίσημη μηχανολογική μελέτη ή ΠΕΑ.
            </p>
        </div>

        <div class="box" style="background: rgba(0,0,0,0.4); padding: 20px; border-radius: 16px; width: 620px; border: 1px solid var(--accent_low); flex-shrink: 0;">
            <label style="margin-bottom: 8px; color: #00ff41; font-weight: 800;">📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ (V9.7)</label>
            <textarea readonly style="width: 100%; height: 480px; background: transparent; border: none; color: #00ff41; font-family: 'Courier New', monospace; font-size: 0.75rem; resize: none; outline: none; line-height: 1.4;" onclick="this.select()"><?php
                echo "=== ΑΝΑΛΥΣΗ ΥΠΟΛΟΓΙΣΜΟΥ ΘΕΡΜΙΚΩΝ ΦΟΡΤΙΩΝ ===\n";
                echo "Ημερομηνία: " . date('d/m/Y H:i') . "\n";
                echo "Κανονισμός:  " . ($GLOBALS['CONSTANTS']['ETOS_LABELS'][$inputs['etos'] ?? 'legacy']) . "\n";
                echo "------------------------------------------\n";
                
                echo "ΣΥΝΘΗΚΕΣ ΣΧΕΔΙΑΣΜΟΥ:\n";
                $final_tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : $GLOBALS['CONSTANTS']['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$inputs['mode'] ?? 'cooling']['tdb'];
                echo "- Tout: " . $final_tout . " °C | Tin: " . $GLOBALS['CONSTANTS']['T_IN_DEFAULT'][$inputs['mode'] ?? 'cooling'] . " °C\n";
                
                $ach = (($inputs['etos'] ?? 'legacy') === 'legacy') ? 2.0 : ((($inputs['etos'] ?? '') === 'medium') ? 1.0 : 0.4);
                echo "- Εναλλαγές Αέρα (ACH): " . number_format($ach, 1) . " /h\n";
                
                echo "\nΑΝΑΛΥΣΗ ΚΕΛΥΦΟΥΣ (U-Values):\n";
                if (($inputs['roof_type'] ?? '') !== 'heated_above') {
                    $u_r = $results['u_roof_final'];
                    $check_r = ($u_r <= $limits['roof']) ? "PASS ✅" : "FAIL ❌";
                    $color_label = $GLOBALS['CONSTANTS']['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['label'];
                    echo "- Οροφή: U=" . number_format($u_r, 2) . " (Όριο: " . $limits['roof'] . ") [" . $check_r . "]\n";
                    echo "  Χρώμα: " . $color_label . " (a=" . ($GLOBALS['CONSTANTS']['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['alpha']) . ")\n";
                }

                echo "\nΑΝΑΛΥΣΗ ΑΝΑ ΠΡΟΣΑΝΑΤΟΛΙΣΜΟ:\n";
                foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
                    $len = $results['is_auto_square'] ? round($results['side_length'], 1) : ($inputs["w_len_$id"] ?? 0);
                    if ($len > 0) {
                        $u_val = $results['wall_u_values'][$id] ?? 0;
                        $check = ($u_val <= $limits['wall']) ? "PASS" : "FAIL";
                        $is_int = ($inputs["w_type_$id"] ?? 'external') === 'internal';
                        
                        echo "- $label: L=$len m | U=" . number_format($u_val, 2) . " [$check]\n";
                        if ($is_int) echo "  (Εσωτερική Τοιχοποιία)\n";
                        
                        $mat = $inputs["ins_mat_$id"] ?? 'none';
                        $depth = floatval($inputs["ins_depth_$id"] ?? 0);
                        if ($mat !== 'none' && $depth > 0) {
                            echo "  Επιπλέον Μόνωση: " . $GLOBALS['CONSTANTS']['LAMBDA'][$mat]['label'] . " (" . $depth . "cm)\n";
                        }
                    }
                }

                echo "\nΤΕΛΙΚΑ ΑΠΟΤΕΛΕΣΜΑΤΑ:\n";
                echo "------------------------------------------\n";
                echo "ΑΠΑΙΤΗΣΗ ΙΣΧΥΟΣ: " . number_format($results['btu'], 0) . " BTU/h\n";
                echo "ΑΠΑΙΤΗΣΗ ΙΣΧΥΟΣ: " . number_format($results['kw'], 2) . " kW\n";
                echo "ΠΡΩΤ. ΕΝΕΡΓΕΙΑ:  " . number_format($results['pe_estimate'], 1) . " kWh/m2/y\n";
                echo "==========================================";
            ?></textarea>
        </div>
    </div>
</div>
<?php endif; ?>
