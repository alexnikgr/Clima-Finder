<?php
/**
 * CLIMAFINDER - ENTRY POINT (V0.7)
 * Handles booting, configuration loading, and MVC orchestration.
 */
define('APP_RUNNING', true);

// 1. Error Reporting (Enable for development, disable for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Load Configuration
$CONSTANTS = require_once 'config/loader.php';

// 3. Autoload Logic Classes
require_once 'src/ModelPhysicsTrait.php';
require_once 'src/ModelEnvelopeTrait.php';
require_once 'src/ModelHorizontalTrait.php';
require_once 'src/ThermalModel.php';
require_once 'src/Controller.php';

// 4. Initialize and Run the Controller
$app = new Controller($CONSTANTS);
$app->handle();
