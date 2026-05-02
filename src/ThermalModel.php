<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * src/ThermalModel.php (V20.1)
 * Fix: Added missing formatResults method.
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

    private function getU($base, $mat, $depth, $etos) {
        $u = $base;
        if ($mat !== 'none' && $depth > 0) {
            $lambda = $this->c['LAMBDA'][$mat]['lambda'] ?? 0;
            if ($lambda > 0) $u = 1 / ((1 / $base) + (($depth / 100) / $lambda));
        }
        return $u + ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.10);
    }

    public function calculate(array $p): array {
        $mode = $p['mode'] ?? 'cooling';
        $area = floatval($p['area'] ?? 0);
        $height = floatval($p['height'] ?? 0);
        $etos = $p['etos'] ?? 'legacy';

        if ($area <= 0 || $height <= 0) return ['btu' => 0, 'kw' => 0];
        
        $dt = $this->getDeltaT($p, $mode);
        $env = $this->processEnvelope($p, $mode, $dt, $etos, $area, $height);
        $u_roof = $this->calculateRoofU($p, $etos);
        $q_roof = $this->calculateRoofLoad($p, $area, $u_roof, $dt, $mode);
        $floor = $this->calculateFloor($p, $area, $env['perimeter'], $dt, $etos);
        
        $ach = ($etos === 'legacy') ? 1.5 : 0.8;
        $q_inf = 0.34 * $ach * ($area * $height) * $dt;

        $sf_cool = floatval($p['m_sf_cool'] ?? 1.10);
        $sf_latent = floatval($p['m_latent'] ?? 1.30);
        
        $total_w = ($mode === 'heating') 
            ? ($env['tc'] + $q_roof + $floor['q'] + $q_inf + $env['ts']) * 1.25 
            : ($env['tc'] + $q_roof + $floor['q'] + $env['ts'] + $q_inf + ($area * 15)) * $sf_cool * $sf_latent;

        return $this->formatResults($total_w, $area, $u_roof, $floor, $env);
    }

    /**
     * Μέθοδος μορφοποίησης αποτελεσμάτων (Αποκατάσταση)
     */
    private function formatResults($w, $area, $u_r, $f, $env) {
        return [
            'btu' => max($w * 3.412, 0), 
            'kw' => max($w / 1000, 0),
            'pe_estimate' => (($w/1000) * 1600 * 2.9) / $area,
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
