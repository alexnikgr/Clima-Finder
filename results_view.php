<?php 
/**
 * THERMAL CALCULATOR - RESULTS VIEW (V10.2)
 * Full Technical Report including ALL User Selections, Stress Test, and Graph.
 */

if ($results && $results['btu'] > 0): 
    $isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling';
    $limits = $GLOBALS['CONSTANTS']['KENAK_2017_LIMITS'][$inputs['zone'] ?? 'b'];
    $final_tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : $GLOBALS['CONSTANTS']['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$inputs['mode'] ?? 'cooling']['tdb'];
    $tin = $GLOBALS['CONSTANTS']['T_IN_DEFAULT'][$inputs['mode'] ?? 'cooling'];
    $ach = (($inputs['etos'] ?? 'legacy') === 'legacy') ? 2.0 : ((($inputs['etos'] ?? '') === 'medium') ? 1.0 : 0.4);
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

            <!-- GRAPH SECTION -->
            <div style="margin-top: 30px; background: rgba(0,0,0,0.3); padding: 20px; border-radius: 20px; border: 1px solid var(--accent_low); max-width: 460px;">
                <label style="font-size: 0.65rem; margin-bottom: 20px; color: #fff; opacity: 0.7; display:block;">
                    ΑΝΑΛΥΣΗ ΕΥΑΙΣΘΗΣΙΑΣ (<?= $isCooling ? 'BTU vs Tout' : 'BTU vs Tcold' ?>)
                </label>
                <div style="position: relative; height: 160px;">
                    <svg viewBox="0 0 400 160" style="width: 100%; height: 100%; overflow: visible;">
                        <?php 
                            $scenarios = [
                                ['btu' => $results['mild']['btu'], 'kw' => $results['mild']['kw'], 'x' => 40],
                                ['btu' => $results['btu'], 'kw' => $results['kw'], 'x' => 200],
                                ['btu' => $results['extreme']['btu'], 'kw' => $results['extreme']['kw'], 'x' => 360]
                            ];
                            $max_btu = max($results['extreme']['btu'], $results['mild']['btu'], $results['btu']);
                            $min_btu = min($results['extreme']['btu'], $results['mild']['btu'], $results['btu']);
                            $range = max($max_btu - ($min_btu * 0.6), 1);
                            $points = [];
                            foreach($scenarios as $s) {
                                $y = 140 - (($s['btu'] - ($min_btu * 0.6)) / $range * 120);
                                $points[] = "{$s['x']},$y";
                            }
                        ?>
                        <polyline points="<?= implode(' ', $points) ?>" fill="none" stroke="var(--accent)" stroke-width="4" stroke-linejoin="round" />
                        <?php foreach($scenarios as $index => $s): 
                            $coord = explode(',', $points[$index]); $pY = $coord[1];
                            $color = ($index == 2) ? '#ff453a' : (($index == 1) ? 'var(--accent)' : 'white');
                        ?>
                            <circle cx="<?= $s['x'] ?>" cy="<?= $pY ?>" r="<?= ($index == 2) ? 6 : 5 ?>" fill="<?= $color ?>" />
                            <text x="<?= $s['x'] ?>" y="<?= $pY - 25 ?>" fill="<?= $color ?>" font-size="11" font-weight="800" text-anchor="middle"><?= number_format($s['btu'], 0) ?></text>
                            <text x="<?= $s['x'] ?>" y="<?= $pY - 12 ?>" fill="<?= $color ?>" font-size="9" opacity="0.8" text-anchor="middle"><?= number_format($s['kw'], 1) ?> kW</text>
                        <?php endforeach; ?>
                    </svg>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.65rem; color: var(--label); margin-top: 20px; padding: 0 10px;">
                    <span>Ήπιο (<?= $final_tout + ($isCooling ? -5 : 5) ?>°C)</span>
                    <span style="color: var(--accent);">Standard</span>
                    <span style="color: #ff453a;"><?= $isCooling ? 'Καύσωνας' : 'Παγετός' ?> (<?= $final_tout + ($isCooling ? 7 : -7) ?>°C)</span>
                </div>
            </div>
            
            <p style="font-size: 0.65rem; color: var(--label); margin-top: 25px; line-height: 1.4; max-width: 450px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <strong>ΝΟΜΙΚΗ ΣΗΜΕΙΩΣΗ:</strong> Ο παρών υπολογισμός αποτελεί τεχνική εκτίμηση και όχι επίσημη μελέτη.
            </p>

            <button onclick="window.print()" class="btn" style="width: auto; padding: 12px 25px; font-size: 0.85rem; background: var(--accent); margin-top: 15px;">
                ΕΚΤΥΠΩΣΗ ΑΝΑΦΟΡΑΣ 📄
            </button>
        </div>

        <!-- FULL TECHNICAL REPORT TEXTAREA -->
        <div class="box" style="background: rgba(0,0,0,0.4); padding: 20px; border-radius: 16px; width: 650px; border: 1px solid var(--accent_low); flex-shrink: 0;">
            <label style="margin-bottom: 8px; color: #00ff41; font-weight: 800;">📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ</label>
            <textarea readonly style="width: 100%; height: 600px; background: transparent; border: none; color: #00ff41; font-family: monospace; font-size: 0.72rem; resize: none; outline: none; line-height: 1.3;" onclick="this.select()"><?php
                echo "=== ΑΝΑΛΥΣΗ ΥΠΟΛΟΓΙΣΜΟΥ ΘΕΡΜΙΚΩΝ ΦΟΡΤΙΩΝ ===\n";
                echo "Ημερομηνία: " . date('d/m/Y H:i') . "\n";
                echo "Κατάσταση:   " . ($isCooling ? "ΨΥΞΗ" : "ΘΕΡΜΑΝΣΗ") . " | Ζώνη " . strtoupper($inputs['zone'] ?? 'B') . "\n";
                echo "Tout: " . $final_tout . " °C | Tin: " . $tin . " °C | ACH: " . number_format($ach, 1) . "\n";
                echo "--------------------------------------------------\n";
                
                echo "ΔΕΔΟΜΕΝΑ ΧΩΡΟΥ & ΚΑΝΟΝΙΣΜΟΥ:\n";
                echo "- Κανονισμός: " . ($GLOBALS['CONSTANTS']['ETOS_LABELS'][$inputs['etos'] ?? 'legacy']) . "\n";
                echo "- Γεωμετρία:  " . ($inputs['area'] ?? 0) . " m2 x " . ($inputs['height'] ?? 0) . " m\n";
                
                echo "\nΑΝΑΛΥΣΗ ΚΕΛΥΦΟΥΣ (U-Values):\n";
                if (($inputs['roof_type'] ?? '') !== 'heated_above') {
                    $u_r = $results['u_roof_final'];
                    $color_label = $GLOBALS['CONSTANTS']['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['label'];
                    echo "- Οροφή: U=" . number_format($u_r, 2) . " [Limit: " . $limits['roof'] . "]\n";
                    echo "  Τύπος: " . ($inputs['roof_type'] == 'terrace' ? 'Δώμα' : 'Στέγη') . " | Χρώμα: " . $color_label . "\n";
                    if ($inputs['roof_ins'] !== 'none') echo "  Μόνωση: " . $GLOBALS['CONSTANTS']['LAMBDA'][$inputs['roof_ins']]['label'] . " (" . $inputs['roof_ins_depth'] . "cm)\n";
                }

                echo "\nΑΝΑΛΥΣΗ ΑΝΑ ΠΡΟΣΑΝΑΤΟΛΙΣΜΟ:\n";
                foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
                    $len = $results['is_auto_square'] ? round($results['side_length'], 1) : ($inputs["w_len_$id"] ?? 0);
                    if ($len > 0) {
                        $u_val = $results['wall_u_values'][$id] ?? 0;
                        echo "- $label: L=$len m | U=" . number_format($u_val, 2) . " | " . ($inputs["w_type_$id"] == 'internal' ? 'ΕΣΩΤ.' : 'ΕΞΩΤ.') . "\n";
                        
                        $mat = $inputs["ins_mat_$id"] ?? 'none';
                        if ($mat !== 'none') echo "  Επιπλ. Μόνωση: " . $GLOBALS['CONSTANTS']['LAMBDA'][$mat]['label'] . " (" . ($inputs["ins_depth_$id"] ?? 0) . "cm)\n";
                        
                        if ($inputs["win_std_$id"] > 0 || $inputs["win_patio_$id"] > 0) {
                            echo "  Κούφωμα: " . $GLOBALS['CONSTANTS']['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['label'] . " / " . $GLOBALS['CONSTANTS']['TZAMI'][$inputs["glass_$id"] ?? 'double']['label'] . "\n";
                        }
                    }
                }

                echo "\n=== STRESS TEST (BTU STRETCHING) ===\n";
                echo "Σενάριο      | Tout  | Ισχύς (BTU) | Μεταβολή\n";
                echo "--------------------------------------------------\n";
                $m_btu = $results['mild']['btu']; $s_btu = $results['btu']; $e_btu = $results['extreme']['btu'];
                echo str_pad("Ήπιο", 12) . " | " . ($final_tout + ($isCooling ? -5 : 5)) . "°C  | " . str_pad(number_format($m_btu,0), 10) . "  | -" . round((1-($m_btu/$s_btu))*100) . "%\n";
                echo str_pad("Standard", 12) . " | " . $final_tout . "°C  | " . str_pad(number_format($s_btu,0), 10) . "  | ---\n";
                echo str_pad($isCooling ? "Καύσωνας" : "Παγετός", 12) . " | " . ($final_tout + ($isCooling ? 7 : -7)) . "°C  | " . str_pad(number_format($e_btu,0), 10) . "  | +" . round((($e_btu/$s_btu)-1)*100) . "%\n";
                
                echo "--------------------------------------------------\n";
                echo "ΤΕΛΙΚΗ ΙΣΧΥΣ ΣΧΕΔΙΑΣΜΟΥ: " . number_format($results['btu'], 0) . " BTU/h (" . number_format($results['kw'], 2) . " kW)\n";
                echo "ΠΡΩΤΟΓΕΝΗΣ ΕΝΕΡΓΕΙΑ:    " . number_format($results['pe_estimate'], 1) . " kWh/m2/y\n";
                echo "==================================================";
            ?></textarea>
        </div>
    </div>
</div>
<?php endif; ?>
