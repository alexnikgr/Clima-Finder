<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');
/**
 * views/partials/technical_report.php (V18.2)
 * Περιλαμβάνει: BTU, kW και τις αντίστοιχες Θερμοκρασίες για όλα τα σενάρια.
 */
?>
<div class="box" style="background: rgba(0,0,0,0.4); width: 650px; flex-shrink: 0; border: 1px solid var(--accent_low);">
    <label style="color: #00ff41; font-weight: 800;">📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ & ΚΛΙΜΑΤΙΚΟΣ ΕΛΕΓΧΟΣ</label>
    <textarea readonly style="width: 100%; height: 580px; background: transparent; border: none; color: #00ff41; font-family: monospace; font-size: 0.72rem; resize: none; outline: none; line-height: 1.35;" onclick="this.select()"><?php
        $c = $GLOBALS['CONSTANTS'];
        $zone = $inputs['zone'] ?? 'b';
        $mode = $inputs['mode'] ?? 'cooling';
        $climate = $c['CLIMATE_FACTORS'][$zone];
        $limits = $c['KENAK_2017_LIMITS'][$zone];
        
        // Καθορισμός Standard Tin / Tout
        $tin = $c['T_IN_DEFAULT'][$mode];
        $tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : $c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];

        echo "=== ΑΝΑΛΥΣΗ ΘΕΡΜΙΚΩΝ ΦΟΡΤΙΩΝ & ΚΛΙΜΑΤΙΚΟΥ ΠΡΟΦΙΛ ===\n";
        echo "Ημερομηνία: " . date('d/m/Y H:i') . "\n";
        echo "Τοποθεσία:  " . $c['CLIMATE_ZONES'][$zone]['label'] . " (" . $climate['desc'] . ")\n";
        echo "--------------------------------------------------\n";

        echo "1. ΣΥΝΘΗΚΕΣ ΣΧΕΔΙΑΣΜΟΥ (Standard Design):\n";
        echo "- Εξωτερική Θερμ. (Tout): " . number_format($tout, 1) . " °C\n";
        echo "- Εσωτερική Θερμ. (Tin):  " . number_format($tin, 1) . " °C\n";
        echo "- Σχετ. Υγρασία (RH):     " . $climate['rh'] . "%\n";
        echo "- Ταχύτητα Ανέμου:        " . $climate['wind'] . " m/s\n\n";

        echo "2. ΣΕΝΑΡΙΑ ΕΥΑΙΣΘΗΣΙΑΣ (Load vs Temperature):\n";
        // Mild Calculation
        $tout_mild = $tout + ($mode == 'cooling' ? -5 : 5);
        echo "- ΗΠΙΟ (Mild)    @ " . str_pad($tout_mild . "°C:", 7) . " " . str_pad(number_format($results['mild']['btu'], 0) . " BTU", 12) . " | " . number_format($results['mild']['kw'], 2) . " kW\n";
        
        // Standard Calculation
        echo "- ΤΥΠΙΚΟ (Std)   @ " . str_pad($tout . "°C:", 7) . " " . str_pad(number_format($results['btu'], 0) . " BTU", 12) . " | " . number_format($results['kw'], 2) . " kW\n";
        
        // Extreme Calculation
        $tout_extreme = $tout + ($mode == 'cooling' ? 7 : -7);
        echo "- ΑΚΡΑΙΟ (Extr)  @ " . str_pad($tout_extreme . "°C:", 7) . " " . str_pad(number_format($results['extreme']['btu'], 0) . " BTU", 12) . " | " . number_format($results['extreme']['kw'], 2) . " kW\n\n";

        echo "3. ΕΛΕΓΧΟΣ ΟΡΙΖΟΝΤΙΩΝ ΣΤΟΙΧΕΙΩΝ (ISO 13370):\n";
        echo "- ΟΡΟΦΗ:  U=" . number_format($results['u_roof_final'], 3) . " W/m2K [Limit: ".$limits['roof']."]\n";
        echo "- ΔΑΠΕΔΟ: U=" . number_format($results['u_floor_final'], 3) . " W/m2K [Limit: ".$limits['floor']."]\n";
        echo "  B' Dim: " . number_format($results['b_prime'] ?? 0, 2) . " m\n\n";
        
        echo "4. ΚΑΤΑΚΟΡΥΦΑ ΣΤΟΙΧΕΙΑ (Structural Audit):\n";
        foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
            $len = $results['is_auto_square'] ? round($results['side_length'], 2) : floatval($inputs["w_len_$id"] ?? 0);
            if ($len > 0) {
                $thick = $inputs["w_thick_$id"] ?? $c['PHYSICS']['w_thick_default'];
                $lag = $results['wall_lags'][$id] ?? 0;
                echo "- $label: L=" . number_format($len, 1) . "m | Πάχος: " . $thick . "m\n";
                echo "  Wall U: " . number_format($results['wall_u_values'][$id] ?? 0, 3) . " | Lag: " . $lag . "h\n";
                $u_win = ($c['TZAMI'][$inputs["glass_$id"] ?? 'double']['u'] * 0.8) + ($c['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['u'] * 0.2);
                echo "  Window: U=" . number_format($u_win, 2) . " | Shade: " . $c['SHADING_OPTIONS'][$inputs["shading_$id"] ?? 'none']['label'] . "\n";
                echo "  . . . . . . . . . . . . . . . . . . . . . . . . .\n";
            }
        }

        echo "\n5. ΤΕΛΙΚΗ ΕΚΤΙΜΗΣΗ ΙΣΧΥΟΣ (Summary):\n";
        echo "MAX PEAK:       " . number_format($results['extreme']['btu'], 0) . " BTU (" . number_format($results['extreme']['kw'], 2) . " kW)\n";
        echo "DESIGN LOAD:    " . number_format($results['btu'], 0) . " BTU (" . number_format($results['kw'], 2) . " kW)\n";
        echo "ENERGY CLASS PE: " . number_format($results['pe_estimate'], 1) . " kWh/m2/y\n";
        echo "==================================================";
    ?></textarea>
</div>
