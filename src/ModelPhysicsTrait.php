<?php
/**
 * src/ModelPhysicsTrait.php
 * Core heat penetration and Delta T logic.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
trait ModelPhysicsTrait {
    private function getDeltaT($p, $mode) {
        $zone = $p['zone'] ?? 'b';
        $tin = $this->c['T_IN_DEFAULT'][$mode];
        $tout = !empty($p['custom_tout']) ? floatval($p['custom_tout']) : $this->c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];
        return abs($tout - $tin);
    }

    private function calculateTimeLag($p, $dir, $etos) {
        $thick = floatval($p["w_thick_$dir"] ?? $this->c['PHYSICS']['w_thick_default']);
        $ins_depth = floatval($p["ins_depth_$dir"] ?? 0) / 100;
        $wall_props = $this->c['THERMAL_PROPS'][$etos];
        
        $k_wall = ($this->c['U_WALL'][$etos] - ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.1)) * $thick;
        $alpha = $k_wall / ($wall_props['density'] * $wall_props['cp']);
        $lag = ($alpha > 0) ? 1.38 * $thick * sqrt(1 / ($alpha * 3600)) : 0;
        
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
