<?php
/**
 * src/Controller.php
 * Handles input sanitization, strict validation, and MVC orchestration.
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

        // 2. Process POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $v) {
                // Basic sanitization
                $inputs[$key] = is_array($v) ? $v : htmlspecialchars(strip_tags($v));
            }
            
            // 3. STRICT VALIDATION
            // Ensure mandatory numeric inputs exist to prevent model warnings
            $area = floatval($inputs['area'] ?? 0);
            $height = floatval($inputs['height'] ?? 0);

            if ($area > 0 && $height > 0) {
                $model = new ThermalModel($this->c);
                
                // Primary Calculation
                $results = $model->calculate($inputs);
                
                // 4. SCENARIO RUNNER (Sensitivity Analysis)
                $mode = $inputs['mode'] ?? 'cooling';
                $std_tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) 
                    : $this->c['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$mode]['tdb'];

                // Extreme Scenario (+7/-7 degrees)
                $results['extreme'] = $model->calculate(array_merge($inputs, [
                    'custom_tout' => $std_tout + ($mode === 'cooling' ? 7 : -7)
                ]));
                
                // Mild Scenario (-5/+5 degrees)
                $results['mild'] = $model->calculate(array_merge($inputs, [
                    'custom_tout' => $std_tout + ($mode === 'cooling' ? -5 : 5)
                ]));
            } else {
                // If validation fails, $results remains null so the dashboard stays hidden
                $results = null;
            }
        }

        // 5. Orchestrate View
        include 'views/layout.php';
    }

    private function initializeInputs() {
        return [
            'mode' => 'cooling', 
            'zone' => 'b', 
            'etos' => 'legacy',
            'roof_type' => 'terrace', 
            'floor_type' => 'ground',
            'm_sf_cool' => '1.10', 
            'm_latent' => '1.18', 
            'm_sf_heat' => '1.20',
            'area' => '', 
            'height' => '3.0' 
        ];
    }
}
