<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ModelHorizontalTrait.php (V20.0)
 * Physics: ISO 13370 Ground Coupling & Solar-Infused Roof Loads.
 */
trait ModelHorizontalTrait {

    /**
     * Calculates the specialized load for the roof, 
     * accounting for extreme solar radiation on terraces.
     */
    private function calculateRoofLoad($p, $area, $u_r, $dt, $mode) {
        if ($u_r <= 0) return 0;

        $solar_impact = 1.0;
        if ($mode === 'cooling') {
            // Get solar absorptivity (alpha) from roof color
            $color_id = $p['roof_color'] ?? 'medium';
            $alpha = $this->c['ROOF_COLORS'][$color_id]['alpha'] ?? 0.65;
            
            /**
             * Sol-Air Correction: 
             * Uninsulated roofs can reach 65°C in Greece.
             * We multiply the DT effect to simulate this solar "pressure".
             */
            $solar_impact = 1 + ($alpha * 2.8); 
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
            
            // 0.7 factor for soil temperature stability
            return ['q' => $area * $u * ($dt * 0.7), 'u' => $u, 'b_prime' => $bp];
        }

        $baseU = $this->c['U_FLOOR_BASE']['pilotis'] ?? 1.20;
        $u = $this->getU($baseU, $p['floor_ins'] ?? 'none', floatval($p['floor_ins_depth'] ?? 0), $etos);
        return ['q' => $area * $u * $dt, 'u' => $u, 'b_prime' => 0];
    }
}
