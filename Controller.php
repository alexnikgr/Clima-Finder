<?php
/**
 * THERMAL CALCULATOR - CONTROLLER (V9)
 * Full persistence for all fields including Overrides and Glazing types.
 */
require_once 'constants.php';
require_once 'ThermalModel.php';

class Controller {
    /**
     * Handles the request and maintains the state of the form.
     */
    public function handle() {
        global $CONSTANTS;
        
        // Initialize an empty array for state persistence
        $inputs = [];
        $results = null;

        // If the request is POST, capture every single field sent by the form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $key => $value) {
                // Sanitize and preserve the exact state
                $inputs[$key] = is_array($value) ? $value : htmlspecialchars(strip_tags($value));
            }
            
            // Execute calculation with the current inputs
            $model = new ThermalModel($CONSTANTS);
            $results = $model->calculate($inputs);
        } else {
            // Optional: Hardcode starting values ONLY for the first load ever
            $inputs['mode'] = 'cooling';
            $inputs['zone'] = 'b';
            $inputs['etos'] = 'legacy';
        }

        // The master view now has access to the full, updated $inputs array
        include 'view.php';
    }
}
