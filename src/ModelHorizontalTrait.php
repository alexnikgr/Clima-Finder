<?php
/**
 * src/ModelHorizontalTrait.php
 * Roof and ISO 13370 Ground heat transfer.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
trait ModelHorizontalTrait {
    private function calculateFloor($p, $area, $perimeter, $dt, $etos) {
        $type = $p['floor_type'] ?? 'ground';
        if ($type === 'heated_below') return ['q' => 0, 'u' => 0, 'b_prime' => 0];
        if ($type === 'ground') {
            $bp = ($perimeter > 0) ? $area / (0.5 * $perimeter) : sqrt($area);
            $rins = 0;
            if (($p['floor_ins'] ?? 'none') !== 'none') {
                $rins = (floatval($p['floor_ins_depth'] ?? 0) / 100) / ($this->c['LAMBDA'][$p['floor_ins']]['lambda'] ?? 0.035);
            }
            $dt_eff = 0.3 + 2.0 * (0.17 + $rins);
            $u = ($dt_eff < $bp) ? (4 / (pi() * $bp + $dt_eff)) * log(pi() * $bp / $dt_eff + 1) : 2.0 / (0.457 * $bp + $dt_eff);
            return ['q' => $area * $u * ($dt * 0.7), 'u' => $u, 'b_prime' => $bp];
        }
        $u = $this->getU($this->c['U_FLOOR_BASE']['pilotis'], $p['floor_ins'] ?? 'none', floatval($p['floor_ins_depth'] ?? 0), $etos);
        return ['q' => $area * $u * $dt, 'u' => $u, 'b_prime' => 0];
    }

    private function calculateRoofU($p, $etos) {
        $type = $p['roof_type'] ?? 'terrace';
        if ($type === 'heated_above') return 0;
        return $this->getU($this->c['U_ROOF_BASE'][$type][$etos], $p['roof_ins'] ?? 'none', floatval($p['roof_ins_depth'] ?? 0), $etos);
    }
}
