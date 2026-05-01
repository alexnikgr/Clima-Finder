<?php 
/**
 * THERMAL CALCULATOR - RESULTS VIEW (V10.1)
 * Full Technical Report with Sensitivity Graph and Legal Disclaimer.
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
            
            <!-- UI DISCLAIMER -->
            <p style="font-size: 0.65rem; color: var(--label); margin-top: 25px; line-height: 1.4; max-width: 450px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                <strong>ΝΟΜΙΚΗ ΣΗΜΕΙΩΣΗ:</strong> Οι υπολογισμοί αποτελούν τεχνική εκτίμηση και όχι επίσημη μελέτη. Η τελική επιλογή εξοπλισμού απαιτεί αυτοψία από εξειδικευμένο μηχανικό.
            </p>

            <button onclick="window.print()" class="btn" style="width: auto; padding: 12px 25px; font-size: 0.85rem; background: var(--accent); margin-top: 15px;">
                ΕΚΤΥΠΩΣΗ ΑΝΑΦΟΡΑΣ 📄
            </button>
        </div>

        <!-- TECHNICAL REPORT TEXTAREA -->
        <div class="box" style="background: rgba(0,0,0,0.4); padding: 20px; border-radius: 16px; width: 620px; border: 1px solid var(--accent_low); flex-shrink: 0;">
            <label style="margin-bottom: 8px; color: #00ff41; font-weight: 800;">📋 ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ & STRESS TEST</label>
            <textarea readonly style="width: 100%; height: 500px; background: transparent; border: none; color: #00ff41; font-family: monospace; font-size: 0.75rem; resize: none; outline: none; line-height: 1.4;" onclick="this.select()"><?php
                echo "=== ΑΝΑΛΥΣΗ ΥΠΟΛΟΓΙΣΜΟΥ ΘΕΡΜΙΚΩΝ ΦΟΡΤΙΩΝ ===\n";
                echo "Ημερομηνία: " . date('d/m/Y H:i') . "\n";
                echo "Κατάσταση:   " . ($isCooling ? "ΨΥΞΗ" : "ΘΕΡΜΑΝΣΗ") . "\n";
                echo "Tout: " . $final_tout . " °C | Tin: " . $tin . " °C | ACH: " . number_format($ach, 1) . "\n";
                echo "--------------------------------------------------\n";
                
                echo "ΕΛΕΓΧΟΣ ΚΕΛΥΦΟΥΣ (U-Values):\n";
                $u_w = $results['wall_u_values']['north'] ?? 0;
                echo "- Τοιχοποιία (Β): U=" . number_format($u_w, 2) . " (Όριο: " . $limits['wall'] . ")\n";
                if ($results['u_roof_final'] > 0) {
                    echo "- Οροφή:         U=" . number_format($results['u_roof_final'], 2) . " (Όριο: " . $limits['roof'] . ")\n";
                }

                echo "\n=== STRESS TEST (BTU STRETCHING) ===\n";
                echo "Σενάριο      | Tout  | Ισχύς (BTU) | Μεταβολή\n";
                echo "--------------------------------------------------\n";
                $m_btu = $results['mild']['btu']; $s_btu = $results['btu']; $e_btu = $results['extreme']['btu'];
                echo str_pad("Ήπιο", 12) . " | " . ($final_tout + ($isCooling ? -5 : 5)) . "°C  | " . str_pad(number_format($m_btu,0), 10) . "  | -" . round((1-($m_btu/$s_btu))*100) . "%\n";
                echo str_pad("Standard", 12) . " | " . $final_tout . "°C  | " . str_pad(number_format($s_btu,0), 10) . "  | ---\n";
                echo str_pad($isCooling ? "Καύσωνας" : "Παγετός", 12) . " | " . ($final_tout + ($isCooling ? 7 : -7)) . "°C  | " . str_pad(number_format($e_btu,0), 10) . "  | +" . round((($e_btu/$s_btu)-1)*100) . "%\n";
                
                echo "--------------------------------------------------\n";
                echo "ΠΡΟΣΟΧΗ: Ο υπολογισμός αποτελεί τεχνική εκτίμηση.\n";
                echo "Δεν υποκαθιστά επίσημη μελέτη ή ΠΕΑ.\n";
                echo "Απαιτείται αυτοψία για την τελική επιλογή.\n";
                echo "==================================================";
            ?></textarea>
        </div>
    </div>
</div>
<?php endif; ?>
