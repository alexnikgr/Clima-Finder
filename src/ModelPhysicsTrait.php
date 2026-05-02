<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ModelPhysicsTrait.php (V19.5)
 * Αυτοματοποιημένος υπολογισμός Thermal Lag βάσει τυπικού πάχους κατασκευής.
 */
trait ModelPhysicsTrait {

    private function getDeltaT($p, $mode) {
        $zone = $p['zone'] ?? 'b';
        $tin = $this->c['T_IN_DEFAULT'][$mode];
        $tout = !empty($p['custom_tout']) ? floatval($p['custom_tout']) : $this->c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];
        return abs($tout - $tin);
    }

    private function calculateTimeLag($p, $dir, $etos) {
        // 1. Αυτόματη λήψη τυπικού πάθους βάσει επιλεγμένης κατασκευής
        $build_type = $p["w_build_$dir"] ?? 'double';
        $thick = $this->c['U_WALL_TYPES'][$build_type]['thickness'] ?? 0.25;
        
        $ins_depth = floatval($p["ins_depth_$dir"] ?? 0) / 100;
        $wall_props = $this->c['THERMAL_PROPS'][$etos] ?? $this->c['THERMAL_PROPS']['legacy'];
        
        // 2. Λήψη βάσης U (αφαιρώντας το penalty θερμογεφυρών για τη μάζα)
        $baseU = $this->c['U_WALL_TYPES'][$build_type]['u'] ?? 1.80;
        $k_wall = ($baseU - ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.1)) * $thick;
        
        // 3. Υπολογισμός Διάχυσης (alpha)
        $alpha = ($wall_props['density'] * $wall_props['cp'] > 0) 
                 ? $k_wall / ($wall_props['density'] * $wall_props['cp']) : 0;
        
        // 4. Φόρμουλα Lag (ώρες)
        $lag = ($alpha > 0) ? 1.38 * $thick * sqrt(1 / ($alpha * 3600)) : 0;
        
        // 5. Επίδραση πρόσθετης μόνωσης
        if (($p["ins_mat_$dir"] ?? 'none') !== 'none') {
            $ins_mat = $p["ins_mat_$dir"];
            $ins_props = $this->c['THERMAL_PROPS'][$ins_mat];
            $k_ins = $this->c['LAMBDA'][$ins_mat]['lambda'];
            $alpha_ins = $k_ins / ($ins_props['density'] * $ins_props['cp']);
            $lag += 1.38 * $ins_depth * sqrt(1 / ($alpha_ins * 3600));
        }
        
        return round($lag, 1);
    }
}
