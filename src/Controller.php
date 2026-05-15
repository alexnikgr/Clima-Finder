<?php
/**
 * src/Controller.php (V28.0 - Refactored)
 * Handles input sanitization, strict filtering, and MVC orchestration.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

class Controller {
    private $c;

    public function __construct($constants) {
        $this->c = $constants;
    }

    public function handle() {
        // 1. Initialize default values
        $inputs = $this->initializeInputs();
        $results = null;

        // 2. Process POST request with type-safe filtering
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Distinct numeric filtering
            $inputs['area']   = filter_input(INPUT_POST, 'area', FILTER_VALIDATE_FLOAT) ?: 0.0;
            $inputs['height'] = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_FLOAT) ?: 3.0;
            
            // Core parameter mapping
            $inputs['mode']       = filter_input(INPUT_POST, 'mode', FILTER_DEFAULT);
            $inputs['zone']       = filter_input(INPUT_POST, 'zone', FILTER_DEFAULT) ?: 'b';
            $inputs['etos']       = filter_input(INPUT_POST, 'etos', FILTER_DEFAULT) ?: 'legacy';
            $inputs['roof_type']  = filter_input(INPUT_POST, 'roof_type', FILTER_DEFAULT) ?: 'terrace';
            $inputs['floor_type'] = filter_input(INPUT_POST, 'floor_type', FILTER_DEFAULT) ?: 'ground';
            $inputs['roof_ins']   = filter_input(INPUT_POST, 'roof_ins', FILTER_DEFAULT) ?: 'none';
            $inputs['floor_ins']  = filter_input(INPUT_POST, 'floor_ins', FILTER_DEFAULT) ?: 'none';
            $inputs['roof_color'] = filter_input(INPUT_POST, 'roof_color', FILTER_DEFAULT) ?: 'medium';

            // Safety factor matrices
            $inputs['m_sf_cool']  = filter_input(INPUT_POST, 'm_sf_cool', FILTER_VALIDATE_FLOAT) ?: 1.10;
            $inputs['m_sf_heat']  = filter_input(INPUT_POST, 'm_sf_heat', FILTER_VALIDATE_FLOAT) ?: 1.20;
            $inputs['m_latent']   = filter_input(INPUT_POST, 'm_latent', FILTER_VALIDATE_FLOAT) ?: 1.18;

            // Optional custom temperature overrides
            $inputs['custom_tin']  = isset($_POST['custom_tin']) && $_POST['custom_tin'] !== '' ? floatval($_POST['custom_tin']) : null;
            $inputs['custom_tout'] = isset($_POST['custom_tout']) && $_POST['custom_tout'] !== '' ? floatval($_POST['custom_tout']) : null;

            // Loop and bind directional properties dynamically
            foreach (['north', 'south', 'east', 'west'] as $dir) {
                $inputs["win_custom_$dir"] = filter_input(INPUT_POST, "win_custom_$dir", FILTER_DEFAULT) ?: 'no';
                $inputs["w_env_$dir"]      = filter_input(INPUT_POST, "w_env_$dir", FILTER_DEFAULT) ?: 'external';
                $inputs["w_build_$dir"]    = filter_input(INPUT_POST, "w_build_$dir", FILTER_DEFAULT) ?: 'double';
                $inputs["w_len_$dir"]      = filter_input(INPUT_POST, "w_len_$dir", FILTER_VALIDATE_FLOAT) ?: 0.0;
                $inputs["ins_mat_$dir"]    = filter_input(INPUT_POST, "ins_mat_$dir", FILTER_DEFAULT) ?: 'none';
                $inputs["ins_depth_$dir"]  = filter_input(INPUT_POST, "ins_depth_$dir", FILTER_VALIDATE_FLOAT) ?: 0.0;
                $inputs["win_std_$dir"]    = filter_input(INPUT_POST, "win_std_$dir", FILTER_VALIDATE_FLOAT) ?: 0.0;
                $inputs["win_patio_$dir"]  = filter_input(INPUT_POST, "win_patio_$dir", FILTER_VALIDATE_FLOAT) ?: 0.0;
                $inputs["frame_$dir"]      = filter_input(INPUT_POST, "frame_$dir", FILTER_DEFAULT) ?: 'alum';
                $inputs["glass_$dir"]      = filter_input(INPUT_POST, "glass_$dir", FILTER_DEFAULT) ?: 'double';
                $inputs["shading_$dir"]    = filter_input(INPUT_POST, "shading_$dir", FILTER_DEFAULT) ?: 'none';
            }

            $inputs['roof_ins_depth']  = filter_input(INPUT_POST, 'roof_ins_depth', FILTER_VALIDATE_FLOAT) ?: 0.0;
            $inputs['floor_ins_depth'] = filter_input(INPUT_POST, 'floor_ins_depth', FILTER_VALIDATE_FLOAT) ?: 0.0;

            // 3. Strict structural checks
            if ($inputs['area'] > 0 && $inputs['height'] > 0) {
                $model = new ThermalModel($this->c);
                
                // Primary calculation run
                $results = $model->calculate($inputs);
                
                // 4. Sensitivity scenario builder
                $mode = $inputs['mode'] ?? 'cooling';
                $std_tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) 
                    : ($this->c['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$mode]['tdb'] ?? 35);

                // Stress conditions (+7 / -7 offset structural shifts)
                $results['extreme'] = $model->calculate(array_merge($inputs, [
                    'custom_tout' => $std_tout + ($mode === 'cooling' ? 7 : -7)
                ]));
                
                // Mild conditions (-5 / +5 mitigation profiles)
                $results['mild'] = $model->calculate(array_merge($inputs, [
                    'custom_tout' => $std_tout + ($mode === 'cooling' ? -5 : 5)
                ]));
            }
        }

        // 5. Build view canvas
        include 'views/layout.php';
    }

    private function initializeInputs() {
        return [
            'mode' => 'cooling', 
            'zone' => 'b', 
            'etos' => 'legacy',
            'roof_type' => 'terrace', 
            'floor_type' => 'ground',
            'm_sf_cool' => 1.10, 
            'm_latent' => 1.18, 
            'm_sf_heat' => 1.20,
            'area' => '', 
            'height' => 3.0,
            'roof_ins' => 'none',
            'floor_ins' => 'none',
            'roof_color' => 'medium',
            'roof_ins_depth' => '',
            'floor_ins_depth' => ''
        ];
    }
}
