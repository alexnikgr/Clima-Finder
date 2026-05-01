<?php
/**
 * THERMAL CALCULATOR - CONTROLLER (V9.9)
 * Handles Mode-Aware Stress Testing (Cooling: Heatwave / Heating: Frost).
 */
require_once 'constants.php';
require_once 'ThermalModel.php';

class Controller {
    public function handle() {
        global $CONSTANTS;
        
        $inputs = [];
        $results = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                $inputs[$key] = is_array($value) ? $value : htmlspecialchars(strip_tags($value));
            }
            
            $model = new ThermalModel($CONSTANTS);
            $results = $model->calculate($inputs);
            
            // Determine Design Tout based on current mode
            $mode = $inputs['mode'] ?? 'cooling';
            $std_tout = !empty($inputs['custom_tout']) 
                ? floatval($inputs['custom_tout']) 
                : $CONSTANTS['DESIGN_CONDITIONS'][$inputs['zone'] ?? 'b'][$mode]['tdb'];

            if ($mode === 'cooling') {
                // EXTREME COOLING = HOTTER (+7)
                $hw_inputs = $inputs;
                $hw_inputs['custom_tout'] = $std_tout + 7;
                $results['extreme'] = $model->calculate($hw_inputs);
                
                // MILD COOLING = COOLER (-5)
                $mild_inputs = $inputs;
                $mild_inputs['custom_tout'] = $std_tout - 5;
                $results['mild'] = $model->calculate($mild_inputs);
            } else {
                // EXTREME HEATING = COLDER (-7)
                $frost_inputs = $inputs;
                $frost_inputs['custom_tout'] = $std_tout - 7;
                $results['extreme'] = $model->calculate($frost_inputs);
                
                // MILD HEATING = WARMER (+5)
                $mild_heat_inputs = $inputs;
                $mild_heat_inputs['custom_tout'] = $std_tout + 5;
                $results['mild'] = $model->calculate($mild_heat_inputs);
            }

        } else {
            // Defaults for first load
            $inputs['mode'] = 'cooling';
            $inputs['zone'] = 'b';
            $inputs['etos'] = 'legacy';
        }

        include 'view.php';
    }
}
