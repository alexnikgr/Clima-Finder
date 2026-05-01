<?php
/**
 * src/Controller.php
 * Handles routing, stress-test logic, and input sanitization.
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
        $inputs = $this->initializeInputs();
        $results = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $v) {
                $inputs[$key] = is_array($v) ? $v : htmlspecialchars(strip_tags($v));
            }
            
            $model = new ThermalModel($this->c);
            $results = $model->calculate($inputs);
            
            // Standard Design Temp
            $mode = $inputs['mode'] ?? 'cooling';
            $std_tout = !empty($inputs['custom_tout']) ? floatval($inputs['custom_tout']) 
                : $this->c['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$mode]['tdb'];

            // Run Scenarios
            $results['extreme'] = $model->calculate(array_merge($inputs, [
                'custom_tout' => $std_tout + ($mode === 'cooling' ? 7 : -7)
            ]));
            $results['mild'] = $model->calculate(array_merge($inputs, [
                'custom_tout' => $std_tout + ($mode === 'cooling' ? -5 : 5)
            ]));
        }

        // Pass variables to the view orchestrator
        include 'views/layout.php';
    }

    private function initializeInputs() {
        return [
            'mode' => 'cooling', 'zone' => 'b', 'etos' => 'legacy',
            'roof_type' => 'terrace', 'floor_type' => 'ground',
            'm_sf_cool' => '1.10', 'm_latent' => '1.30'
        ];
    }
}
