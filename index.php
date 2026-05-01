<?php
/**
 * THERMAL CALCULATOR PRO - ENTRY POINT (V15.0)
 * Handles booting, configuration loading, and MVC orchestration.
 */
 define('APP_RUNNING', true); // Το "κλειδί" ασφαλείας


// 1. Error Reporting (Enable for development, disable for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Load Configuration
// The loader stiches together climate, materials, and physics config files.
$CONSTANTS = require_once 'config/loader.php';

// 3. Autoload Logic Classes
// In a larger app, you'd use a PSR-4 autoloader; here we require them manually.
require_once 'src/ModelPhysicsTrait.php';
require_once 'src/ModelEnvelopeTrait.php';
require_once 'src/ModelHorizontalTrait.php';
require_once 'src/ThermalModel.php';
require_once 'src/Controller.php';

// 4. Initialize and Run the Controller
// We inject the $CONSTANTS array into the Controller for use in the app.
$app = new Controller($CONSTANTS);
$app->handle();
