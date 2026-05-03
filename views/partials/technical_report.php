<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * views/partials/technical_report.php (V25.0)
 * THE MASTER AUDIT: Surface Audit (Net/Glass) + Sensitivity (Stress/Soft) + Full Specs.
 */
?>
<div class="box" style="background: rgba(0,0,0,0.4); width: 680px; flex-shrink: 0; border: 1px solid var(--accent_low); box-shadow: inset 0 0 30px rgba(0,0,0,0.5);">
    <label style="color: #00ff41; font-weight: 800; display: flex; justify-content: space-between;">
        <span>📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ & ΕΛΕΓΧΟΣ ΜΟΝΤΕΛΟΥ</span>
        <span style="opacity: 0.5;">V25.0.0</span>
    </label>
    
    <textarea readonly style="width: 100%; height: 820px; background: transparent; border: none; color: #00ff41; font-family: 'SF Mono', 'Fira Code', monospace; font-size: 0.72rem; resize: none; outline: none; line-height: 1.4; margin-top: 15px;" onclick="this.select()"><?php
        $c = $GLOBALS['CONSTANTS'];
        $zone = $inputs['zone'] ?? 'b';
        $mode = $inputs['mode'] ?? 'cooling';
        $climate = $c['CLIMATE_FACTORS'][$zone];
        
        $tin = !empty($inputs['custom_tin']) ? floatval($inputs['custom_tin']) : $c['T_IN_DEFAULT'][$mode];
        $tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : $c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];
        
        // Scenario Loads
        $std_btu = $results['btu'];
        $extreme_btu = $results['extreme']['btu'] ?? 0;
        $mild_btu = $results['mild']['btu'] ?? 0;

        // Sensitivity Metrics
        $surge_pct = ($std_btu > 0) ? (($extreme_btu - $std_btu) / $std_btu * 100) : 0;
        $reduction_pct = ($std_btu > 0) ? (($std_btu - $mild_btu) / $std_btu * 100) : 0;
        $t_stress = $tout + ($mode == 'cooling' ? 7 : -7);
        $t_soft = $tout + ($mode == 'cooling' ? -5 : 5);

        echo "============================================================\n";
        echo "   THERMAL PRO PROPRIETARY REPORT - FULL AUDIT LOG\n";
        echo "============================================================\n";
        echo "DATE: " . date('d/m/Y H:i') . " | ID: " . strtoupper(uniqid()) . "\n";
        echo "------------------------------------------------------------\n\n";

        echo " 1. ΣΥΝΘΗΚΕΣ ΣΧΕΔΙΑΣΜΟΥ (DESIGN BOUNDARIES)\n";
        echo "- Λειτουργία:       " . ($mode == 'cooling' ? "ΨΥΞΗ" : "ΘΕΡΜΑΝΣΗ") . " | Ζώνη: " . strtoupper($zone) . "\n";
        echo "- Εξωτερική (Tout): " . number_format($tout, 1) . " °C | Εσωτερική (Tin): " . number_format($tin, 1) . " °C\n";
        echo "- Safety Factors:   Sens: " . ($inputs['m_sf_cool'] ?? '1.10') . " / Lat: " . ($inputs['m_latent'] ?? '1.18') . "\n\n";

        echo " 2. ΑΝΑΛΥΣΗ ΜΕΤΑΒΛΗΤΟΥ ΦΟΡΤΙΟΥ (SENSITIVITY)\n";
        echo "► ΗΠΙΕΣ (SOFT)   @ " . $t_soft . "°C: " . number_format($mild_btu, 0) . " BTU [-" . number_format($reduction_pct, 1) . "%]\n";
        echo "► ΑΚΡΑΙΕΣ (STRESS) @ " . $t_stress . "°C: " . number_format($extreme_btu, 0) . " BTU [+" . number_format($surge_pct, 1) . "%]\n\n";

        echo " 3. ΕΛΕΓΧΟΣ ΚΕΛΥΦΟΥΣ (ENVELOPE & OPENINGS)\n";
        foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
            $len = $results['is_auto_square'] ? round($results['side_length'], 2) : floatval($inputs["w_len_$id"] ?? 0);
            
            if ($len > 0) {
                $gross_wall = $len * floatval($inputs['height'] ?? 0);
                $is_c = (($inputs["win_custom_$id"] ?? 'no') === 'yes');
                $v_std = floatval($inputs["win_std_$id"] ?? 0);
                $v_patio = floatval($inputs["win_patio_$id"] ?? 0);
                
                if ($is_c) {
                    $a_win = $v_std + $v_patio;
                    $win_audit = "Manual Area: " . number_format($a_win, 2) . " m2";
                } else {
                    $a_win = ($v_std * 1.56) + ($v_patio * 3.08);
                    $win_audit = "Units: " . (int)$v_std . " Win, " . (int)$v_patio . " Patio (" . number_format($a_win, 2) . " m2)";
                }

                $env_id = $inputs["w_env_$id"] ?? 'external';
                $u_win = ($c['TZAMI'][$inputs["glass_$id"] ?? 'double']['u'] * 0.8) + ($c['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['u'] * 0.2);

                echo "► " . str_pad($label, 8) . " | L: " . number_format($len, 1) . "m | " . $c['ADJACENT_FACTORS'][$env_id]['label'] . "\n";
                echo "  Wall: " . $c['U_WALL_TYPES'][$inputs["w_build_$id"] ?? 'double']['label'] . " (U=" . number_format($results['wall_u_values'][$id], 3) . ") | Lag: " . ($results['wall_lags'][$id] ?? 0) . "h\n";
                echo "  Openings:  " . $win_audit . " | Spec U_win=" . number_format($u_win, 2) . "\n";
                echo "  Surface:   Net Wall: " . number_format(max($gross_wall - $a_win, 0), 2) . " m2 | Opening: " . number_format($a_win, 2) . " m2\n";
                echo "  Specs:     " . $c['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['label'] . " / " . $c['TZAMI'][$inputs["glass_$id"] ?? 'double']['label'] . "\n";
                echo "  - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n";
            }
        }

        echo "\n 4. ΟΡΙΖΟΝΤΙΑ ΣΤΟΙΧΕΙΑ (HORIZONTAL AUDIT)\n";
        echo "► ΟΡΟΦΗ:  " . str_pad($inputs['roof_type'] ?? 'terrace', 12) . " | U=" . number_format($results['u_roof_final'], 3) . " | " . $c['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['label'] . "\n";
        echo "► ΔΑΠΕΔΟ: " . str_pad($inputs['floor_type'] ?? 'ground', 12) . " | U=" . number_format($results['u_floor_final'], 3) . " | B'=" . number_format($results['b_prime'] ?? 0, 2) . "m\n\n";

        echo " 5. ΤΕΛΙΚΗ ΕΚΤΙΜΗΣΗ (LOAD VERDICT)\n";
        echo "► PEAK LOAD (BTU):  " . number_format($std_btu, 0) . " BTU/h\n";
        echo "► PEAK LOAD (kW):   " . number_format($results['kw'], 2) . " kW\n";
        echo "► ENERGY PE:        " . number_format($results['pe_estimate'], 1) . " kWh/m2/y\n";
        echo "============================================================\n";
        echo "  STATUS: VALIDATED | ENGINE: R-C_TRANSIENT_V25.0\n";
        echo "============================================================";
    ?></textarea>
</div>
