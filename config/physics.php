<?php
/**
 * config/physics.php (V28.0 - Refactored)
 * Regulatory constants, b-factors, and primary energy multipliers.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

return [
    // KENAK Primary Energy Factors (Regulatory Multipliers)
    'PRIMARY_ENERGY_FACTORS' => [
        'electricity' => 2.90,
        'oil'         => 1.10,
        'natural_gas' => 1.05
    ],

    // Linear thermal bridge penalties (W/mK) per unit length by era
    // Refactored label to prevent area-weighting engineering misconceptions
    'THERMAL_BRIDGES' => [
        'legacy' => 0.35, 
        'medium' => 0.20, 
        'new'    => 0.10  
    ],

    // Fundamental Physics & Soil Parameters
    'PHYSICS' => [
        'p_atm'           => 101325, 
        'soil_lambda'     => 2.0,
        'w_thick_default' => 0.30,
        'air_density'     => 1.20,      // kg/m3 - Critical structural constraint for ventilation loops
        'air_shc'         => 1005       // J/kgK - Specific heat capacity tracking constants
    ],

    // Temperature Correction Factors (b-factors based on TOTEE 20701-2)
    'ADJACENT_FACTORS' => [
        'external'   => ['label' => 'ΕΞΩΤΕΡΙΚΟΣ ΑΕΡΑΣ', 'f' => 1.0],
        'unheated'   => ['label' => 'ΜΗ ΘΕΡΜΑΙΝΟΜΕΝΟΣ (ΜΘΧ)', 'f' => 0.7],
        'semiheated' => ['label' => 'ΗΜΙΘΕΡΜΑΙΝΟΜΕΝΟΣ', 'f' => 0.4],
        'ground'     => ['label' => 'ΣΕ ΕΠΑΦΗ ΜΕ ΤΟ ΕΔΑΦΟΣ', 'f' => 0.5], // Patched regulatory simplified ground factor
        'heated'     => ['label' => 'ΘΕΡΜΑΙΝΟΜΕΝΟΣ (ΓΕΙΤΟΝΑΣ)', 'f' => 0.0] 
    ],

    // Regional Climate Factors (Supplementary Solar/Wind Indicators)
    'CLIMATE_FACTORS' => [
        'a' => ['rh' => 60, 'wind' => 4.2, 'solar_gain' => 1.8],
        'b' => ['rh' => 55, 'wind' => 3.8, 'solar_gain' => 1.6],
        'c' => ['rh' => 65, 'wind' => 3.5, 'solar_gain' => 1.4],
        'd' => ['rh' => 70, 'wind' => 4.5, 'solar_gain' => 1.2]
    ]
];
