<?php
if (!defined('APP_RUNNING')) die('Direct access denied.');

/**
 * config/materials.php (V21.0)
 */
return [
    'U_WALL_TYPES' => [
        'single'    => ['label' => 'Μονός (Δρομικός 10cm)', 'u' => 3.20, 'thickness' => 0.10],
        'double'    => ['label' => 'Διπλός (Μπατικός 25cm)', 'u' => 1.80, 'thickness' => 0.25],
        'insulated' => ['label' => 'Με Μόνωση (Προϋπάρχουσα)', 'u' => 0.60, 'thickness' => 0.30]
    ],

    'U_ROOF_BASE' => [
        'terrace'      => ['legacy' => 3.10, 'medium' => 1.10, 'new' => 0.40],
        'pitched'      => ['legacy' => 2.50, 'medium' => 0.90, 'new' => 0.35],
        'slab_under'   => ['legacy' => 2.10, 'medium' => 0.85, 'new' => 0.32], // Πλάκα κάτω από στέγη
        'heated_above' => ['legacy' => 0.0, 'medium' => 0.0, 'new' => 0.0]
    ],

    'LAMBDA' => [
        'none'     => ['label' => 'Χωρίς μόνωση', 'lambda' => 0],
        'xps'      => ['label' => 'XPS (εξηλασμένη)', 'lambda' => 0.035],
        'eps'      => ['label' => 'EPS (διογκωμένη)', 'lambda' => 0.038],
        'rockwool' => ['label' => 'Πετροβάμβακας', 'lambda' => 0.040]
    ],

    'U_WALL' => ['legacy' => 2.80, 'medium' => 1.20, 'new' => 0.50],
    'U_FLOOR_BASE' => [
        'ground'       => 0.80,
        'pilotis'      => 1.50,
        'heated_below' => 0.0
    ],

    'KOUFOMATA' => [
        'alum'        => ['label' => 'Αλουμίνιο (απλό)', 'u' => 5.8],
        'alum_thermo' => ['label' => 'Αλουμίνιο (θερμο)', 'u' => 3.0],
        'pvc'         => ['label' => 'PVC', 'u' => 2.5],
        'wood'        => ['label' => 'Ξύλο', 'u' => 2.2]
    ],

    'TZAMI' => [
        'single'       => ['label' => 'Μονό', 'u' => 5.8, 'g' => 0.85],
        'double'       => ['label' => 'Διπλό', 'u' => 2.9, 'g' => 0.75],
        'double_low_e' => ['label' => 'Low‑E (Ενεργειακό)', 'u' => 1.6, 'g' => 0.55]
    ],

    'THERMAL_PROPS' => [
        'legacy'   => ['density' => 1700, 'cp' => 840],
        'medium'   => ['density' => 1200, 'cp' => 900],
        'new'      => ['density' => 900,  'cp' => 1000],
        'xps'      => ['density' => 35,   'cp' => 1450],
        'eps'      => ['density' => 20,   'cp' => 1450],
        'rockwool' => ['density' => 100,  'cp' => 1030],
        'none'     => ['density' => 1,    'cp' => 1]
    ]
];
