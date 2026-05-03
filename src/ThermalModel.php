<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ThermalModel.php (V22.0)
 * Core Orchestrator: Combines traits and applies final safety factors.
 */
require_once 'ModelPhysicsTrait.php';
require_once 'ModelEnvelopeTrait.php';
require_once 'ModelHorizontalTrait.php';

class ThermalModel {
    use ModelPhysicsTrait, ModelEnvelopeTrait, ModelHorizontalTrait;

    private $c;
    public function __construct($constants) { 
        $this->c = $constants; 
    }

    /**
     * Calculates the U-value of a construction element including insulation.
     */
    private function getU($base, $mat, $depth, $etos) {
        $u = $base;
        if ($mat !== 'none' && $depth > 0) {
            $lambda = $this->c['LAMBDA'][$mat]['lambda'] ?? 0;
            if ($lambda > 0) {
                // R_total = R_base + R_insulation
                $u = 1 / ((1 / $base) + (($depth / 100) / $lambda));
            }
        }
        // Apply thermal bridge penalty based on building era
        return $u + ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.10);
    }

    public function calculate(array $p): array {
        $mode = $p['mode'] ?? 'cooling';
        $area = floatval($p['area'] ?? 0);
        $height = floatval($p['height'] ?? 0);
        $etos = $p['etos'] ?? 'legacy';

        // Gatekeeper: Should already be handled by Controller, but kept for safety.
        if ($area <= 0 || $height <= 0) return ['btu' => 0, 'kw' => 0];
        
        // 1. Calculate Delta T (Temperature Difference)
        $dt = $this->getDeltaT($p, $mode);

        // 2. Process Vertical Elements (Walls/Windows)
        $env = $this->processEnvelope($p, $mode, $dt, $etos, $area, $height);

        // 3. Process Horizontal Elements (Roof/Floor)
        $u_roof = $this->calculateRoofU($p, $etos);
        $q_roof = $this->calculateRoofLoad($p, $area, $u_roof, $dt, $mode);
        $floor = $this->calculateFloor($p, $area, $env['perimeter'], $dt, $etos);
        
        // 4. Infiltration (Air Changes)
        $ach = ($etos === 'legacy') ? 1.5 : 0.8;
        $q_inf = 0.34 * $ach * ($area * $height) * $dt;

        // 5. Apply Safety Factors (SF)
        $sf_cool = floatval($p['m_sf_cool'] ?? 1.10);
        $sf_latent = floatval($p['m_latent'] ?? 1.18);
        $sf_heat = floatval($p['m_sf_heat'] ?? 1.20); 
        
        // Final Wattage Calculation
        $total_w = ($mode === 'heating') 
            ? ($env['tc'] + $q_roof + $floor['q'] + $q_inf + $env['ts']) * $sf_heat 
            : ($env['tc'] + $q_roof + $floor['q'] + $env['ts'] + $q_inf + ($area * 15)) * $sf_cool * $sf_latent;

        return $this->formatResults($total_w, $area, $u_roof, $floor, $env);
    }

    /**
     * Formats the raw wattage into displayable units and estimates.
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
