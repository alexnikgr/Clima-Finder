<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ModelPhysicsTrait.php (V23.5)
 * Corrected Transient Heat Flow: Uses combined R-C properties for composite layers.
 */
trait ModelPhysicsTrait {

    private function getDeltaT($p, $mode) {
        $zone = $p['zone'] ?? 'b';
        // Tin Override support
        $tin = !empty($p['custom_tin']) ? floatval($p['custom_tin']) : $this->c['T_IN_DEFAULT'][$mode];
        // Tout Override support
        $tout = !empty($p['custom_tout']) ? floatval($p['custom_tout']) : $this->c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'];
        return abs($tout - $tin);
    }

    private function calculateTimeLag($p, $dir, $etos) {
        // 1. Base Wall Properties (Thickness and Mass)
        $build_type = $p["w_build_$dir"] ?? 'double';
        $L_wall = $this->c['U_WALL_TYPES'][$build_type]['thickness'] ?? 0.25;
        $wall_props = $this->c['THERMAL_PROPS'][$etos] ?? $this->c['THERMAL_PROPS']['legacy'];
        
        $baseU = $this->c['U_WALL_TYPES'][$build_type]['u'] ?? 1.80;
        $k_wall = ($baseU - ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.1)) * $L_wall;
        
        // 2. Insulation Properties
        $ins_mat = $p["ins_mat_$dir"] ?? 'none';
        $L_ins = floatval($p["ins_depth_$dir"] ?? 0) / 100; // convert cm to meters
        
        // 3. Composite System Calculation (The Physics Correction)
        // R_total (Resistance) and C_total (Heat Capacity)
        $R_total = ($k_wall > 0) ? ($L_wall / $k_wall) : 0.1;
        $C_total = ($L_wall * $wall_props['density'] * $wall_props['cp']);

        if ($ins_mat !== 'none') {
            $ins_props = $this->c['THERMAL_PROPS'][$ins_mat];
            $k_ins = $this->c['LAMBDA'][$ins_mat]['lambda'] ?? 0.035;
            
            $R_total += ($k_ins > 0) ? ($L_ins / $k_ins) : 0;
            $C_total += ($L_ins * $ins_props['density'] * $ins_props['cp']);
        }

        // 4. Calculate Lag (Phase Shift) in Hours
        // Formula uses the square root of (R * C) to determine the time delay
        if ($R_total > 0 && $C_total > 0) {
            $lag = 1.38 * sqrt(($R_total * $C_total) / 3600);
            
            // Heavy masonry rarely exceeds 14h phase shift in a 24h cycle
            return round(min($lag, 14), 1);
        }
        
        return 0;
    }
}
