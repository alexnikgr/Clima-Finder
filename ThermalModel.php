<?php
/**
 * THERMAL CALCULATOR - MODEL (V9.9)
 * Hardened for Heating (Night-time peak) and Cooling (Sol-Air Peak).
 */
class ThermalModel {
    private $c;

    public function __construct($constants) {
        $this->c = $constants;
    }

    private function calculateU(float $baseU, string $lambdaId, float $depthCm, string $etos): float {
        $u_calc = $baseU;
        if ($lambdaId !== 'none' && $depthCm > 0) {
            $lambda = $this->c['LAMBDA'][$lambdaId]['lambda'] ?? 0;
            if ($lambda > 0) {
                $u_calc = 1 / ((1 / $baseU) + (($depthCm / 100) / $lambda));
            }
        }
        // Thermal bridges based on building age
        $deltaU = ($etos === 'legacy') ? 0.35 : (($etos === 'medium') ? 0.25 : 0.10);
        return $u_calc + $deltaU;
    }

    public function calculate(array $p): array {
        $mode = $p['mode'] ?? 'cooling';
        $area = floatval($p['area'] ?? 0);
        $height = floatval($p['height'] ?? 0);
        $zone = $p['zone'] ?? 'b';
        $etos = $p['etos'] ?? 'legacy';

        // Security Factors
        $sf_cool = floatval($p['m_sf_cool'] ?? 1.10);
        $sf_latent = floatval($p['m_latent'] ?? 1.30);

        if ($area <= 0 || $height <= 0) return ['btu' => 0, 'kw' => 0, 'pe_estimate' => 0];
        
        $tin = $this->c['T_IN_DEFAULT'][$mode];
        $tout = (!empty($p['custom_tout']) ? floatval($p['custom_tout']) : $this->c['DESIGN_CONDITIONS'][$zone][$mode]['tdb']);
        $dt = abs($tout - $tin);
        
        $tc_w = 0; $ts_w = 0; $tc_surf = 0; 
        $wall_u_values = [];

        $total_manual_len = 0;
        foreach (['north', 'south', 'east', 'west'] as $dir) { $total_manual_len += floatval($p["w_len_$dir"] ?? 0); }
        $is_auto_square = ($total_manual_len <= 0);
        $side_length = $is_auto_square ? sqrt($area) : 0;
        
        $SOLAR_DIR = ['north'=>0.2, 'south'=>1.0, 'east'=>0.8, 'west'=>0.9];
        $I_vert = $this->c['I_ZONE'][$zone]['south_vert'];

        // --- WALLS & WINDOWS ---
        foreach (['north', 'south', 'east', 'west'] as $dir) {
            $len = $is_auto_square ? $side_length : floatval($p["w_len_$dir"] ?? 0);
            if ($len <= 0) continue;

            $is_internal = (($p["w_type_$dir"] ?? 'external') === 'internal');
            $adj_dt = $is_internal ? ($dt * 0.6) : $dt;
            
            // Wall-specific insulation check
            $wall_mat = $p["ins_mat_$dir"] ?? 'none';
            $wall_depth = floatval($p["ins_depth_$dir"] ?? 0);
            $u_wall = $this->calculateU($this->c['U_WALL'][$etos], $wall_mat, $wall_depth, $etos);
            $wall_u_values[$dir] = $u_wall;

            $A_win = (floatval($p["win_std_$dir"] ?? 0) * 1.2) + (floatval($p["win_patio_$dir"] ?? 0) * 2.4);
            $A_wall_opaque = max(($len * $height) - $A_win, 0);
            $tc_w += $A_wall_opaque * $u_wall * $adj_dt;

            if ($A_win > 0) {
                $u_win = ($this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['u'] * 0.8) + ($this->c['KOUFOMATA'][$p["frame_$dir"] ?? 'alum']['u'] * 0.2);
                $tc_w += $A_win * $u_win * $adj_dt;
                
                // Solar gains only for Cooling. Heating assumes night-time peak (worst case).
                if ($mode === 'cooling' && !$is_internal) {
                    $shade = $this->c['SHADING_OPTIONS'][$p["shading_$dir"] ?? 'none']['factor'] ?? 1.0;
                    $ts_w += $A_win * $this->c['TZAMI'][$p["glass_$dir"] ?? 'double']['g'] * $SOLAR_DIR[$dir] * $shade * $I_vert;
                }
            }
        }

        // --- ROOF & FLOOR ---
        if (($p['roof_type'] ?? 'terrace') !== 'heated_above') {
            $u_r = $this->calculateU($this->c['U_ROOF_BASE'][$p['roof_type'] ?? 'terrace'][$etos], $p['roof_ins'] ?? 'none', floatval($p['roof_ins_depth'] ?? 0), $etos);
            if ($mode === 'cooling') {
                $alpha = $this->c['ROOF_COLORS'][$p['roof_color'] ?? 'medium']['alpha'] ?? 0.65;
                $sol_air_dt = max($dt + (($alpha * ($this->c['I_ZONE'][$zone]['horiz'] ?? 700)) / 17.0) - 4.0, $dt);
                $tc_surf += $area * $u_r * $sol_air_dt;
            } else {
                $tc_surf += $area * $u_r * $dt; // Heating: no solar credit
            }
        }

        // --- INFILTRATION ---
        $ach = ($etos === 'legacy') ? 2.0 : 1.0; 
        $infiltration = 0.34 * $ach * ($area * $height) * $dt;

        // --- AGGREGATION BY MODE ---
        if ($mode === 'heating') {
            // Heating ignores internal gains for peak sizing
            $total_watts = ($tc_w + $tc_surf + $infiltration) * 1.25; 
        } else {
            // Cooling includes sensible internal gains + latent/safety factors
            $internal_gains = ($area * 15);
            $total_watts = ($tc_w + $tc_surf + $ts_w + $infiltration + $internal_gains) * $sf_cool * $sf_latent;
        }

        return [
            'btu' => $total_watts * 3.41214,
            'kw'  => $total_watts / 1000,
            'pe_estimate' => (($total_watts/1000) * 1600 * 2.9) / $area,
            'u_roof_final' => $u_r ?? 0,
            'wall_u_values' => $wall_u_values,
            'is_auto_square' => $is_auto_square,
            'side_length' => $side_length
        ];
    }
}
