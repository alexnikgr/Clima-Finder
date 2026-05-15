<?php
/**
 * src/ReportHelper.php (V28.0)
 * Logic: Generates a full technical audit log with actual temperature scenarios.
 */
class ReportHelper {
    public static function generate($inputs, $results, $c) {
        // 1. Core Environment Logic
        $mode = $inputs['mode'] ?? 'cooling';
        $zone = $inputs['zone'] ?? 'b';
        $isCool = ($mode === 'cooling');
        
        // Temperature Variables
        $tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) : ($c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'] ?? 0);
        $tin = !empty($inputs['custom_tin']) ? floatval($inputs['custom_tin']) : ($c['T_IN_DEFAULT'][$mode] ?? 0);
        $dt = abs($tout - $tin);

        // Scenario Actual Temperatures
        $tout_extreme = $tout + ($isCool ? 7 : -7);
        $tout_mild    = $tout + ($isCool ? -5 : 5);

        // 2. Build the Report String
        $out = "============================================================\n";
        $out .= "   THERMAL PRO V28.0 - FULL ENGINEERING AUDIT LOG\n";
        $out .= "============================================================\n";
        $out .= "DATE: " . date('d/m/Y H:i') . " | ID: " . strtoupper(uniqid()) . "\n\n";

        $out .= " 1. ΣΥΝΘΗΚΕΣ ΣΧΕΔΙΑΣΜΟΥ (DESIGN CONDITIONS)\n";
        $out .= "------------------------------------------------------------\n";
        $out .= "► Κατάσταση:    " . ($isCool ? "ΨΥΞΗ (Cooling)" : "ΘΕΡΜΑΝΣΗ (Heating)") . "\n";
        $out .= "► Ζώνη:         " . ($c['CLIMATE_ZONES'][$zone]['label'] ?? strtoupper($zone)) . "\n";
        $out .= "► Παλαιότητα:   " . ($c['ETOS_LABELS'][$inputs['etos'] ?? 'legacy']) . "\n";
        $out .= "► Τ-Εσωτερική:  " . number_format($tin, 1) . " °C (Setpoint)\n";
        $out .= "► Τ-Εξωτερική:  " . number_format($tout, 1) . " °C (Base Design)\n";
        $out .= "► Delta T:      " . number_format($dt, 1) . " K\n\n";

        $out .= " 2. ΓΕΩΜΕΤΡΙΑ & ΟΡΙΖΟΝΤΙΑ ΣΤΟΙΧΕΙΑ\n";
        $out .= "------------------------------------------------------------\n";
        $out .= "► Επιφάνεια:    " . number_format($inputs['area'], 2) . " m²\n";
        $out .= "► Καθαρό Ύψος:  " . number_format($inputs['height'], 2) . " m\n";
        $out .= "► Περίμετρος:   " . number_format($results['perimeter'] ?? 0, 2) . " m\n";
        
        $roof_label = $inputs['roof_type'] ?? 'terrace';
        $out .= "► Τύπος Οροφής: " . $roof_label . " (U_fin: " . number_format($results['u_roof_final'] ?? 0, 3) . ")\n";
        $out .= "  • Χρώμα (a):  " . ($c['ROOF_COLORS'][$inputs['roof_color'] ?? 'medium']['label']) . "\n";
        
        $floor_label = $inputs['floor_type'] ?? 'ground';
        $out .= "► Τύπος Δαπέδου:" . $floor_label . " (U_fin: " . number_format($results['u_floor_final'] ?? 0, 3) . ")\n";
        if (($results['b_prime'] ?? 0) > 0) {
            $out .= "  • B-Prime:    " . number_format($results['b_prime'], 2) . " (ISO 13370 Ground coupling)\n";
        }
        $out .= "\n";

        $out .= " 3. ΑΝΑΛΥΣΗ ΚΕΛΥΦΟΥΣ (ENVELOPE BREAKDOWN)\n";
        $out .= "------------------------------------------------------------\n";
        foreach(['north'=>'ΒΟΡΡΑΣ', 'south'=>'ΝΟΤΟΣ', 'east'=>'ΑΝΑΤΟΛΗ', 'west'=>'ΔΥΣΗ'] as $id => $label) {
            $len = ($results['is_auto_square'] ?? false) ? $results['side_length'] : floatval($inputs["w_len_$id"] ?? 0);
            if ($len > 0) {
                $is_c = (($inputs["win_custom_$id"] ?? 'no') === 'yes');
                $out .= "► " . str_pad($label, 10) . " | L: " . number_format($len, 1) . "m\n";
                $out .= "  • Κατασκευή: " . ($c['U_WALL_TYPES'][$inputs["w_build_$id"] ?? 'double']['label']) . "\n";
                $out .= "  • U-Value:   " . number_format($results['wall_u_values'][$id], 3) . " W/m²K | Lag: " . ($results['wall_lags'][$id] ?? 0) . "h\n";
                $out .= "  • Επαφή:     " . ($c['ADJACENT_FACTORS'][$inputs["w_env_$id"] ?? 'external']['label']) . "\n";
                $out .= "  • Σκίαση:    " . ($c['SHADING_OPTIONS'][$inputs["shading_$id"] ?? 'none']['label']) . "\n";
                $out .= "  • Ανοίγματα: " . ($is_c ? "CUSTOM m²" : "STANDARD TEM") . " | Frame: " . ($c['KOUFOMATA'][$inputs["frame_$id"] ?? 'alum']['label']) . "\n";
                $out .= "  - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\n";
            }
        }

        $out .= "\n 4. ΣΥΝΤΕΛΕΣΤΕΣ & ΑΝΑΛΥΣΗ ΕΥΑΙΣΘΗΣΙΑΣ\n";
        $out .= "------------------------------------------------------------\n";
        $out .= "► SF Sensible: " . ($inputs['m_sf_cool'] ?? '1.10') . "\n";
        if ($isCool) {
            $out .= "► SF Latent:   " . ($inputs['m_latent'] ?? '1.18') . "\n";
        }
        $out .= "► SF Heating:  " . ($inputs['m_sf_heat'] ?? '1.20') . "\n";
        
        $out .= "► ΣΕΝΑΡΙΟ ΗΠΙΟ    (@ " . number_format($tout_mild, 1) . " °C): " . number_format($results['mild']['btu'] ?? 0, 0) . " BTU/h\n";
        $out .= "► ΣΕΝΑΡΙΟ STANDARD (@ " . number_format($tout, 1) . " °C): " . number_format($results['btu'] ?? 0, 0) . " BTU/h\n";
        $out .= "► ΣΕΝΑΡΙΟ STRESS   (@ " . number_format($tout_extreme, 1) . " °C): " . number_format($results['extreme']['btu'] ?? 0, 0) . " BTU/h\n\n";

        $out .= " 5. ΤΕΛΙΚΑ ΑΠΟΤΕΛΕΣΜΑΤΑ\n";
        $out .= "------------------------------------------------------------\n";
        $out .= "► ΜΕΓΙΣΤΟ ΦΟΡΤΙΟ: " . number_format($results['btu'] ?? 0, 0) . " BTU/h\n";
        $out .= "► ΙΣΧΥΣ ΣΕ kW:    " . number_format($results['kw'] ?? 0, 2) . " kW\n";
        $out .= "► PRIMARY ENERGY: " . number_format($results['pe_estimate'] ?? 0, 1) . " kWh/m²/y\n";
        $out .= "============================================================";
        
        return $out;
    }
}
