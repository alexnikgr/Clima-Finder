<?php
/**
 * src/ModelPhysicsTrait.php (V28.0 - Refactored)
 * Mathematical Foundation: Temperature Differentials & Transient Heat Flow (Lag).
 */
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

trait ModelPhysicsTrait {

    /**
     * Calculates the absolute temperature difference between indoors and outdoors.
     * Uses custom overrides if provided, otherwise defaults to climate zone data.
     */
    private function getDeltaT($p, $mode) {
        $zone = $p['zone'] ?? 'b';
        // Tin Override (Internal Setpoint)
        $tin = !empty($p['custom_tin']) ? floatval($p['custom_tin']) : ($this->c['T_IN_DEFAULT'][$mode] ?? 20);
        // Tout Override (Design Outdoor Temperature)
        $tout = !empty($p['custom_tout']) ? floatval($p['custom_tout']) : ($this->c['DESIGN_CONDITIONS'][$zone][$mode]['tdb'] ?? 35);
        
        return abs($tout - $tin);
    }

    /**
     * Calculates Thermal Lag (hours) for composite wall systems.
     * Logic: sqrt(R_total * C_total) to determine the phase shift of the heat wave.
     */
    private function calculateTimeLag($p, $dir, $etos) {
        // 1. Base Wall Geometry & Era Properties
        $build_type = $p["w_build_$dir"] ?? 'double';
        $L_wall = $this->c['U_WALL_TYPES'][$build_type]['thickness'] ?? 0.25;
        $wall_props = $this->c['THERMAL_PROPS'][$etos] ?? $this->c['THERMAL_PROPS']['legacy'];
        
        // Correct engineering calculation of material resistance factoring boundary film resistance (Rse + Rsi ~ 0.17)
        $baseU = $this->c['U_WALL_TYPES'][$build_type]['u'] ?? 1.80;
        $R_wall_material = max((1 / $baseU) - 0.17, 0.05); // Prevent negative numbers
        $k_wall = $L_wall / $R_wall_material;
        
        // 2. Additional Insulation Geometry
        $ins_mat = $p["ins_mat_$dir"] ?? 'none';
        $L_ins = floatval($p["ins_depth_$dir"] ?? 0) / 100; // cm to m
        
        // 3. Composite Thermal Circuit (R and C)
        $R_total = $R_wall_material;
        $C_total = ($L_wall * $wall_props['density'] * $wall_props['cp']);

        // Check against missing configuration keys to prevent fatal crashes
        if ($ins_mat !== 'none' && isset($this->c['THERMAL_PROPS'][$ins_mat])) {
            $ins_props = $this->c['THERMAL_PROPS'][$ins_mat];
            $k_ins = $this->c['LAMBDA'][$ins_mat]['lambda'] ?? 0.035;
            
            $R_total += ($k_ins > 0) ? ($L_ins / $k_ins) : 0;
            $C_total += ($L_ins * $ins_props['density'] * $ins_props['cp']);
        }

        // 4. Calculate Final Phase Shift
        // Based on ISO 13786 simplified periodic heat flow
        if ($R_total > 0 && $C_total > 0) {
            $lag = 1.38 * sqrt(($R_total * $C_total) / 3600);
            
            // Heavy masonry cycles rarely exceed 14 hours
            return round(min($lag, 14), 1);
        }
        
        return 0;
    }
}
