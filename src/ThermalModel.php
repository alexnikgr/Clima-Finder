<?php
/**
 * src/ThermalModel.php (Refactored)
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
require_once 'ModelPhysicsTrait.php';
require_once 'ModelEnvelopeTrait.php';
require_once 'ModelHorizontalTrait.php';

class ThermalModel {
    use ModelPhysicsTrait, ModelEnvelopeTrait, ModelHorizontalTrait;

    private $c;
    public function __construct($constants) { $this->c = $constants; }

    private function getU($base, $mat, $depth, $etos) {
        $u = $base;
        if ($mat !== 'none' && $depth > 0) {
            $lambda = $this->c['LAMBDA'][$mat]['lambda'] ?? 0;
            if ($lambda > 0) $u = 1 / ((1 / $base) + (($depth / 100) / $lambda));
        }
        return $u + ($this->c['THERMAL_BRIDGES'][$etos] ?? 0.10);
    }

    public function calculate(array $p): array {
        $mode = $p['mode'] ?? 'cooling'; $area = floatval($p['area'] ?? 0);
        $height = floatval($p['height'] ?? 0); $etos = $p['etos'] ?? 'legacy';
        if ($area <= 0 || $height <= 0) return ['btu' => 0, 'kw' => 0];
        
        $dt = $this->getDeltaT($p, $mode);
        $env = $this->processEnvelope($p, $mode, $dt, $etos, $area, $height);
        $u_roof = $this->calculateRoofU($p, $etos);
        $floor = $this->calculateFloor($p, $area, $env['perimeter'], $dt, $etos);
        
        $total_w = ($mode === 'heating') 
            ? ($env['tc'] + ($area * $u_roof * $dt) + $floor['q'] + $env['ts'] + (0.34 * ($area * $height) * $dt)) * 1.25 
            : ($env['tc'] + ($area * $u_roof * $dt) + $floor['q'] + $env['ts'] + ($area * 15)) * 1.1 * 1.3;

        return [
            'btu' => max($total_w * 3.412, 0), 'kw' => max($total_w / 1000, 0),
            'pe_estimate' => (($total_w/1000) * 1600 * 2.9) / $area,
            'u_roof_final' => $u_roof, 'u_floor_final' => $floor['u'],
            'wall_u_values' => $env['wall_u'], 'wall_lags' => $env['wall_lags'],
            'is_auto_square' => $env['is_auto'], 'side_length' => $env['side_len'], 'b_prime' => $floor['b_prime']
        ];
    }
}
