<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ModelHorizontalTrait.php (V21.0)
 * Refined Roof Physics: Differentiates between exposed and shielded horizontal elements.
 */
trait ModelHorizontalTrait {

    /**
     * Calculates roof load with specific Sol-Air logic for different configurations.
     */
    private function calculateRoofLoad($p, $area, $u_r, $dt, $mode) {
        if ($u_r <= 0) return 0;

        $solar_impact = 1.0;
        if ($mode === 'cooling') {
            $type = $p['roof_type'] ?? 'terrace';
            $alpha = $this->c['ROOF_COLORS'][$p['roof_color'] ?? 'medium']['alpha'] ?? 0.65;
            
            if ($type === 'terrace') {
                // Fully exposed slab: High Sol-Air intensity
                $solar_impact = 1 + ($alpha * 2.8); 
            } elseif ($type === 'slab_under') {
                // Slab under roof: Shielded from direct radiation, limited air heat gain
                $solar_impact = 1 + ($alpha * 0.45); 
            } else {
                // Standard Pitched Roof (Ceramic tiles/Standard)
                $solar_impact = 1 + ($alpha * 1.4); 
            }
        }

        return $area * $u_r * $dt * $solar_impact;
    }

    private function calculateRoofU($p, $etos) {
        $type = $p['roof_type'] ?? 'terrace';
        if ($type === 'heated_above') return 0;
        
        $baseU = $this->c['U_ROOF_BASE'][$type][$etos] ?? 1.20;
        return $this->getU($baseU, $p['roof_ins'] ?? 'none', floatval($p['roof_ins_depth'] ?? 0), $etos);
    }

    private function calculateFloor($p, $area, $perimeter, $dt, $etos) {
        $type = $p['floor_type'] ?? 'ground';
        if ($type === 'heated_below') return ['q' => 0, 'u' => 0, 'b_prime' => 0];

        if ($type === 'ground') {
            $bp = ($perimeter > 0) ? $area / (0.5 * $perimeter) : sqrt($area);
            $rins = 0;
            if (($p['floor_ins'] ?? 'none') !== 'none') {
                $lambda = $this->c['LAMBDA'][$p['floor_ins']]['lambda'] ?? 0.035;
                $rins = (floatval($p['floor_ins_depth'] ?? 0) / 100) / $lambda;
            }
            $dt_eff = 0.3 + 2.0 * (0.17 + $rins);
            $u = ($dt_eff < $bp) ? (4 / (pi() * $bp + $dt_eff)) * log(pi() * $bp / $dt_eff + 1) : 2.0 / (0.457 * $bp + $dt_eff);
            return ['q' => $area * $u * ($dt * 0.7), 'u' => $u, 'b_prime' => $bp];
        }

        $baseU = $this->c['U_FLOOR_BASE']['pilotis'] ?? 1.20;
        $u = $this->getU($baseU, $p['floor_ins'] ?? 'none', floatval($p['floor_ins_depth'] ?? 0), $etos);
        return ['q' => $area * $u * $dt, 'u' => $u, 'b_prime' => 0];
    }
}
