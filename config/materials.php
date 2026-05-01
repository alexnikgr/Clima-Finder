<?php
/**
 * config/materials.php
 * Thermal properties, U-Values, and Material Mass for Time Lag calculations.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
return [
    'KENAK_2017_LIMITS' => [
        'a' => ['wall' => 0.60, 'roof' => 0.50, 'floor' => 0.50, 'windows' => 3.20],
        'b' => ['wall' => 0.50, 'roof' => 0.45, 'floor' => 0.45, 'windows' => 3.00],
        'c' => ['wall' => 0.45, 'roof' => 0.40, 'floor' => 0.40, 'windows' => 2.80],
        'd' => ['wall' => 0.40, 'roof' => 0.35, 'floor' => 0.35, 'windows' => 2.60]
    ],
    // Density (kg/m3) and Specific Heat Cp (J/kgK)
    'THERMAL_PROPS' => [
        'legacy'   => ['density' => 1700, 'cp' => 840],  // Solid Brick/Stone
        'medium'   => ['density' => 1200, 'cp' => 900],  // Hollow Brick
        'new'      => ['density' => 900,  'cp' => 1000], // Lightweight Concrete/Ytong
        'xps'      => ['density' => 35,   'cp' => 1450],
        'eps'      => ['density' => 20,   'cp' => 1450],
        'rockwool' => ['density' => 100,  'cp' => 1030],
        'none'     => ['density' => 1,    'cp' => 1]
    ],
    'LAMBDA' => [
        'none'     => ['label' => 'Χωρίς μόνωση', 'lambda' => 0],
        'xps'      => ['label' => 'XPS (εξηλασμένη)', 'lambda' => 0.035],
        'eps'      => ['label' => 'EPS (διογκωμένη)', 'lambda' => 0.038],
        'rockwool' => ['label' => 'Πετροβάμβακας', 'lambda' => 0.040]
    ],
    'U_WALL' => ['legacy' => 2.50, 'medium' => 1.20, 'new' => 0.50],
    'U_ROOF_BASE' => [
        'terrace'      => ['legacy' => 1.20, 'medium' => 0.80, 'new' => 0.40],
        'pitched'      => ['legacy' => 0.80, 'medium' => 0.55, 'new' => 0.30],
        'heated_above' => ['legacy' => 0.0, 'medium' => 0.0, 'new' => 0.0]
    ],
    'U_FLOOR_BASE' => [
        'ground'       => 0.60,
        'pilotis'      => 1.20,
        'heated_below' => 0.0
    ],
    'KOUFOMATA' => [
        'alum'        => ['label' => 'Αλουμίνιο', 'u' => 5.5],
        'alum_thermo' => ['label' => 'Αλουμίνιο (θερμο)', 'u' => 3.0],
        'pvc'         => ['label' => 'PVC', 'u' => 2.8],
        'wood'        => ['label' => 'Ξύλο', 'u' => 2.5]
    ],
    'TZAMI' => [
        'single'       => ['label' => 'Μονό', 'u' => 5.8, 'g' => 0.85],
        'double'       => ['label' => 'Διπλό', 'u' => 2.8, 'g' => 0.70],
        'double_low_e' => ['label' => 'Low‑E (Ενεργειακό)', 'u' => 1.6, 'g' => 0.55]
    ]
];
