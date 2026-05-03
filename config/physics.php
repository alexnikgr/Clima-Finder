<?php
/**
 * config/physics.php (V23.5)
 * Updated with Adjacent Factors (b-factors) for professional environment audits.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

return [
    'PRIMARY_ENERGY_FACTORS' => [
        'electricity' => 2.90,
        'oil'         => 1.10,
        'natural_gas' => 1.05
    ],
    'THERMAL_BRIDGES' => [
        'legacy' => 0.35, 
        'medium' => 0.20, 
        'new'    => 0.10  
    ],
    'PHYSICS' => [
        'p_atm' => 101325, 
        'h_fg'  => 2450000,
        'soil_lambda' => 2.0,
        'w_thick_default' => 0.30 
    ],
    // NEW: Adjacent Space b-factors
    'ADJACENT_FACTORS' => [
        'external'   => ['label' => 'ΕΞΩΤΕΡΙΚΟΣ (ΑΕΡΑΣ)', 'f' => 1.0],
        'unheated'   => ['label' => 'ΜΗ ΘΕΡΜΑΙΝΟΜΕΝΟΣ (ΜΘΧ)', 'f' => 0.7],
        'semiheated' => ['label' => 'ΗΜΙΘΕΡΜΑΙΝΟΜΕΝΟΣ', 'f' => 0.4],
        'heated'     => ['label' => 'ΘΕΡΜΑΙΝΟΜΕΝΟΣ (ΓΕΙΤΟΝΑΣ)', 'f' => 0.0] 
    ],
    'CLIMATE_FACTORS' => [
        'a' => ['rh' => 60, 'wind' => 4.2, 'solar_gain' => 1.8, 'desc' => 'Θερμό/Παραθαλάσσιο'],
        'b' => ['rh' => 55, 'wind' => 3.8, 'solar_gain' => 1.6, 'desc' => 'Εύκρατο/Μεσογειακό'],
        'c' => ['rh' => 65, 'wind' => 3.5, 'solar_gain' => 1.4, 'desc' => 'Ηπειρωτικό'],
        'd' => ['rh' => 70, 'wind' => 4.5, 'solar_gain' => 1.2, 'desc' => 'Ορεινό/Ψυχρό']
    ]
];
