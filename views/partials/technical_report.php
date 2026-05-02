<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');
/**
 * views/partials/technical_report.php (V19.5)
 * Τελικό Audit: Εστίαση σε Τύπους Κατασκευής και Θερμική Υστέρηση.
 */
?>
<div class="box" style="background: rgba(0,0,0,0.4); width: 650px; flex-shrink: 0; border: 1px solid var(--accent_low);">
    <label style="color: #00ff41; font-weight: 800;">📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ & ΚΛΙΜΑΤΙΚΟΣ ΕΛΕΓΧΟΣ</label>
    <textarea readonly style="width: 100%; height: 580px; background: transparent; border: none; color: #00ff41; font-family: monospace; font-size: 0.72rem; resize: none; outline: none; line-height: 1.35;" onclick="this.select()"><?php
        $c = $GLOBALS['CONSTANTS'];
        $zone = $inputs['zone'] ?? 'b';
        $mode = $inputs['mode'] ?? 'cooling';
        $climate = $c['CLIMATE_FACTORS'][$zone];
        
        $tin = $c['T_IN_DEFAULT'][$mode];
        $tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : $c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];

        echo "=== ΑΝΑΛΥΣΗ ΘΕΡΜΙΚΩΝ ΦΟΡΤΙΩΝ & ΚΛΙΜΑΤΙΚΟΥ ΠΡΟΦΙΛ ===\n";
        echo "Ημερομηνία: " . date('d/m/Y H:i') . " | Ζώνη: " . strtoupper($zone) . "\n";
        echo "--------------------------------------------------\n";

        echo "1. ΠΑΡΑΜΕΤΡΟΙ ΣΧΕΔΙΑΣΜΟΥ (Design Conditions):\n";
        echo "- Εξωτερική (Tout): " . number_format($tout, 1) . " °C\n";
        echo "- Εσωτερική (Tin):  " . number_format($tin, 1) . " °C\n";
        echo "- Σχετική Υγρασία: " . $climate['rh'] . "% | Άνεμος: " . $climate['wind'] . " m/s\n\n";

        echo "2. ΣΕΝΑΡΙΑ ΕΥΑΙΣΘΗΣΙΑΣ (BTU / kW @ Tout):\n";
        foreach(['mild' => 'ΗΠΙΟ', 'btu' => 'ΤΥΠΙΚΟ', 'extreme' => 'ΑΚΡΑΙΟ'] as $key => $lbl) {
            $val = ($key == 'btu') ? $results : $results[$key];
            $t_scen = $tout + ($key == 'mild' ? ($mode == 'cooling' ? -5 : 5) : ($key == 'extreme' ? ($mode == 'cooling' ? 7 : -7) : 0));
            echo "- " . str_pad($lbl, 10) . " @ " . str_pad($t_scen . "°C:", 7) . " " . str_pad(number_format($val['btu'], 0) . " BTU", 12) . " | " . number_format($val['kw'], 2) . " kW\n";
        }

        echo "\n3. ΚΑΤΑΚΟΡΥΦΑ ΣΤΟΙΧΕΙΑ (Wall & Lag Audit):\n";
        foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
            $len = $results['is_auto_square'] ? round($results['side_length'], 2) : floatval($inputs["w_len_$id"] ?? 0);
            if ($len > 0) {
                $b_id = $inputs["w_build_$id"] ?? 'double';
                $build_label = $c['U_WALL_TYPES'][$b_id]['label'];
                $env_label = ($inputs["w_env_$id"] ?? 'external') == 'internal' ? 'ΕΣΩΤΕΡΙΚΟΣ' : 'ΕΞΩΤΕΡΙΚΟΣ';
                
                echo "- $label (L=" . number_format($len,1) . "m): $env_label / $build_label\n";
                echo "  U-Wall: " . number_format($results['wall_u_values'][$id], 3) . " | Lag: " . ($results['wall_lags'][$id] ?? 0) . "h\n";
                $u_win = ($c['TZAMI'][$inputs["glass_$id"] ?? 'double']['u'] * 0.8) + ($c['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['u'] * 0.2);
                echo "  Window: U=" . number_format($u_win, 2) . " | " . $c['SHADING_OPTIONS'][$inputs["shading_$id"] ?? 'none']['label'] . "\n";
                echo "  . . . . . . . . . . . . . . . . . . . . . . . . .\n";
            }
        }

        echo "\n4. ΟΡΙΖΟΝΤΙΑ ΣΤΟΙΧΕΙΑ & ΕΔΑΦΟΣ (ISO 13370):\n";
        echo "- ΟΡΟΦΗ:  U=" . number_format($results['u_roof_final'], 3) . " | " . $c['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['label'] . "\n";
        echo "- ΔΑΠΕΔΟ: U=" . number_format($results['u_floor_final'], 3) . " | B': " . number_format($results['b_prime'] ?? 0, 2) . "m\n";
        
        echo "\n5. ΤΕΛΙΚΗ ΕΚΤΙΜΗΣΗ (Final Sizing):\n";
        echo "DESIGN LOAD: " . number_format($results['btu'], 0) . " BTU (" . number_format($results['kw'], 2) . " kW)\n";
        echo "ENERGY PE:   " . number_format($results['pe_estimate'], 1) . " kWh/m2/y\n";
        echo "==================================================";
    ?></textarea>
</div>
