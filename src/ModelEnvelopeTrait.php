<?php
/**
 * src/ModelEnvelopeTrait.php (V28.0 - Refactored)
 * Logic: Precise area calculation for Custom m2 vs. Unit counting and linear thermal bridge scaling.
 */
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

trait ModelEnvelopeTrait {
    private function processEnvelope($p, $mode, $dt, $etos, $area, $height) {
        $tc = 0; $ts = 0; $wall_u = []; $wall_lags = []; $total_p = 0;
        
        // Auto-Geometry Fallback
        $is_auto = (floatval($p['w_len_north'] ?? 0) <= 0);
        $side = $is_auto ? sqrt($area) : 0;

        foreach (['north', 'south', 'east', 'west'] as $dir) {
            $len = $is_auto ? $side : floatval($p["w_len_$dir"] ?? 0);
            if ($len <= 0) continue;
            $total_p += $len;

            // 1. Calculate Wall U-Value and Thermal Mass Lag
            $build_type = $p["w_build_$dir"] ?? 'double';
            $baseU = $this->c['U_WALL_TYPES'][$build_type]['u'] ?? 1.80;
            $u_w = $this->getU($baseU, $p["ins_mat_$dir"] ?? 'none', floatval($p["ins_depth_$dir"] ?? 0), $etos);
            $wall_u[$dir] = $u_w;
            $wall_lags[$dir] = $this->calculateTimeLag($p, $dir, $etos);

            // 2. Dynamic Temperature Adjustment (b-factors)
            $env_type = $p["w_env_$dir"] ?? 'external';
            $f_adj = $this->c['ADJACENT_FACTORS'][$env_type]['f'] ?? 1.0;
            $adj_dt = $dt * $f_adj;

            // 3. The m2 Correction Logic
            $is_custom = (($p["win_custom_$dir"] ?? 'no') === 'yes');
            if ($is_custom) {
                // Sum inputs directly as m2
                $awin = floatval($p["win_std_$dir"] ?? 0) + floatval($p["win_patio_$dir"] ?? 0);
            } else {
                // Standard pieces to m2 conversion
                $awin = (intval($p["win_std_$dir"] ?? 0) * 1.56) + (intval($p["win_patio_$dir"] ?? 0) * 3.08);
            }

            // 4. Calculate Opaque vs Glazed Conduction
            $gross_wall_area = $len * $height;
            $net_wall_area = max($gross_wall_area - $awin, 0);
            $tc += ($net_wall_area * $u_w * $adj_dt);

            // 5. Integrate True Linear Thermal Bridge Penalty (Psi) over Wall Perimeter Length
            $psi_penalty = $this->c['THERMAL_BRIDGES'][$etos] ?? 0.20;
            $tc += ($len * $psi_penalty * $adj_dt);

            if ($awin > 0) {
                // Composite U-Value for Windows (Frame + Glass)
                $u_frame = $this->c['KOUFOMATA'][$p["frame_$dir"] ?? 'alum']['u'] ?? 3.0;
                $u_glass = $this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['u'] ?? 2.9;
                $u_win_total = ($u_glass * 0.8) + ($u_frame * 0.2);
                
                $tc += ($awin * $u_win_total * $adj_dt);
                
                // 6. Calculate Solar Heat Gains
                $ts += $this->calculateShading($p, $dir, $awin, $mode);
            }
        }
        
        return [
            'tc' => $tc, 
            'ts' => $ts, 
            'wall_u' => $wall_u, 
            'wall_lags' => $wall_lags, 
            'is_auto' => $is_auto, 
            'side_len' => $side, 
            'perimeter' => $total_p
        ];
    }

    private function calculateShading($p, $dir, $awin, $mode) {
        $sid = $p["shading_$dir"] ?? 'none';
        $sf = $this->c['SHADING_OPTIONS'][$sid]['factor'] ?? 1.0;
        $g = $this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['g'] ?? 0.7;

        // Cooling: Solar intensity ~700W/m2 | Heating: Gain ~250W/m2
        if ($mode === 'cooling') return $awin * $g * 0.8 * $sf * 700;
        
        $is_ret = in_array($sid, ['shutter', 'awning', 'trees_dec', 'louvers_bio']);
        return -($awin * $g * 0.8 * ($is_ret ? 0.9 : $sf) * 250);
    }
}
