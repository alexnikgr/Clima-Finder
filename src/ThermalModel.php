<?php
/**
 * src/ThermalModel.php (V28.0 - Final Consolidated)
 * Core Orchestrator: Combines traits and coordinates horizontal/vertical heat calculations.
 * Fully leverages configuration constants to eliminate hardcoded HVAC magic numbers.
 */
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

class ThermalModel {
    use ModelPhysicsTrait, ModelEnvelopeTrait, ModelHorizontalTrait;

    private $c;
    
    public function __construct($constants) { 
        $this->c = $constants; 
    }

    /**
     * Calculates the true compound U-value of a construction element.
     * Thermal bridge penalties (Psi) are handled globally per unit length in the traits.
     */
    private function getU($base, $mat, $depth, $etos) {
        $u = $base;
        if ($mat !== 'none' && $depth > 0) {
            $lambda = $this->c['LAMBDA'][$mat]['lambda'] ?? 0;
            if ($lambda > 0) {
                // Formula: 1 / ( (1/U_base) + (Thickness_m / Lambda) )
                $u = 1 / ((1 / $base) + (($depth / 100) / $lambda));
            }
        }
        return $u;
    }

    public function calculate(array $p): array {
        $mode = $p['mode'] ?? 'cooling';
        $area = floatval($p['area'] ?? 0);
        $height = floatval($p['height'] ?? 0);
        $etos = $p['etos'] ?? 'legacy';

        if ($area <= 0 || $height <= 0) return ['btu' => 0, 'kw' => 0];
        
        // 1. Calculate Delta T (Indoor vs Outdoor)
        $dt = $this->getDeltaT($p, $mode);

        // 2. Process Vertical Elements (Walls/Windows & Linear Thermal Bridges)
        $env = $this->processEnvelope($p, $mode, $dt, $etos, $area, $height);

        // 3. Process Horizontal Elements (Roof/Floor via ISO 13370)
        $u_roof = $this->calculateRoofU($p, $etos);
        $q_roof = $this->calculateRoofLoad($p, $area, $u_roof, $dt, $mode);
        $floor = $this->calculateFloor($p, $area, $env['perimeter'], $dt, $etos);
        
        // 4. Dynamic Infiltration Load (Air Changes per Hour based on Building Era)
        $ach = ($etos === 'legacy') ? 1.5 : 0.8;
        
        // Dynamically compute the infiltration multiplier from physics config variables: (Density * Shc) / 3600
        $rho = floatval($this->c['PHYSICS']['air_density'] ?? 1.20);
        $cp  = floatval($this->c['PHYSICS']['air_shc'] ?? 1005);
        $infiltration_multiplier = ($rho * $cp) / 3600; // Yields exactly ~0.335 based on standards
        
        $q_inf = $infiltration_multiplier * $ach * ($area * $height) * $dt;

        // 5. Final Sizing Calculation with Safety Factors
        $sf_cool = floatval($p['m_sf_cool'] ?? 1.10);
        $sf_latent = floatval($p['m_latent'] ?? 1.18);
        $sf_heat = floatval($p['m_sf_heat'] ?? 1.20); 
        
        // Sizing Safeguard: Peak heating ignores daytime solar gains to ensure winter sizing stability
        if ($mode === 'heating') {
            $net_heating_losses = $env['tc'] + $q_roof + $floor['q'] + $q_inf;
            $total_w = max($net_heating_losses, 0) * $sf_heat;
        } else {
            // Cooling includes sensible envelope conduction, roof, floor, solar gains, infiltration, and internal gains (15W/m2)
            $total_w = ($env['tc'] + $q_roof + $floor['q'] + $env['ts'] + $q_inf + ($area * 15)) * $sf_cool * $sf_latent;
        }

        return $this->formatResults($total_w, $area, $u_roof, $floor, $env);
    }

    /**
     * Formats raw wattage into report-ready metrics and unit conversions.
     */
    private function formatResults($w, $area, $u_r, $f, $env) {
        return [
            'btu' => max($w * 3.412, 0), 
            'kw' => max($w / 1000, 0),
            'pe_estimate' => (($w/1000) * 1600 * 2.9) / max($area, 1),
            'u_roof_final' => $u_r, 
            'u_floor_final' => $f['u'],
            'wall_u_values' => $env['wall_u'], 
            'wall_lags' => $env['wall_lags'],
            'is_auto_square' => $env['is_auto'], 
            'side_length' => $env['side_len'], 
            'b_prime' => $f['b_prime'],
            'perimeter' => $env['perimeter']
        ];
    }
}
