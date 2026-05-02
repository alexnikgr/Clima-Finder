<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ModelEnvelopeTrait.php
 * Διαχείριση Τοιχοποιίας: Υπολογισμός βάσει Τύπου (Μονός/Διπλός) & Σκίαση.
 */
trait ModelEnvelopeTrait {
    private function processEnvelope($p, $mode, $dt, $etos, $area, $height) {
        $tc = 0; $ts = 0; $wall_u = []; $wall_lags = []; $total_p = 0;
        $is_auto = (floatval($p['w_len_north'] ?? 0) <= 0);
        $side = $is_auto ? sqrt($area) : 0;

        foreach (['north', 'south', 'east', 'west'] as $dir) {
            $len = $is_auto ? $side : floatval($p["w_len_$dir"] ?? 0);
            if ($len <= 0) continue;
            $total_p += $len;

            // --- ΝΕΟ LOGIC: Υπολογισμός U βάσει Τύπου Κατασκευής ---
            $build_type = $p["w_build_$dir"] ?? 'double';
            $baseU = $this->c['U_WALL_TYPES'][$build_type]['u'] ?? 1.80;
            
            $u_w = $this->getU($baseU, $p["ins_mat_$dir"] ?? 'none', floatval($p["ins_depth_$dir"] ?? 0), $etos);
            $wall_u[$dir] = $u_w;
            $wall_lags[$dir] = $this->calculateTimeLag($p, $dir, $etos);

            // Έλεγχος Εσωτερικού/Εξωτερικού (w_env)
            $is_internal = (($p["w_env_$dir"] ?? 'external') === 'internal');
            $adj_dt = $is_internal ? ($dt * 0.6) : $dt;

            $awin = (floatval($p["win_std_$dir"] ?? 0) * 1.2) + (floatval($p["win_patio_$dir"] ?? 0) * 2.4);
            $tc += (max(($len * $height) - $awin, 0) * $u_w * $adj_dt);

            if ($awin > 0) {
                $uwin = ($this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['u'] * 0.8) + ($this->c['KOUFOMATA'][$p["frame_$dir"] ?? 'alum']['u'] * 0.2);
                $tc += $awin * $uwin * $adj_dt;
                $ts += $this->calculateShading($p, $dir, $awin, $mode);
            }
        }
        return ['tc' => $tc, 'ts' => $ts, 'wall_u' => $wall_u, 'wall_lags' => $wall_lags, 'is_auto' => $is_auto, 'side_len' => $side, 'perimeter' => $total_p];
    }

    private function calculateShading($p, $dir, $awin, $mode) {
        $sid = $p["shading_$dir"] ?? 'none';
        $sf = $this->c['SHADING_OPTIONS'][$sid]['factor'] ?? 1.0;
        $g = $this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['g'] ?? 0.7;
        if ($mode === 'cooling') return $awin * $g * 0.8 * $sf * 700;
        $is_ret = in_array($sid, ['shutter', 'awning', 'trees_dec', 'louvers_bio']);
        return -($awin * $g * 0.8 * ($is_ret ? 0.9 : $sf) * 250);
    }
}
