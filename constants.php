<?php
/**
 * THERMAL CALCULATOR - CONSTANTS (KENAK 2017 compliant)
 */
$CONSTANTS = [
    'CLIMATE_ZONES' => [
        'a' => ['label' => 'Ζώνη Α (Ηράκλειο)'],
        'b' => ['label' => 'Ζώνη Β (Αθήνα)'],
        'c' => ['label' => 'Ζώνη Γ (Θεσσαλονίκη)'],
        'd' => ['label' => 'Ζώνη Δ (Φλώρινα)']
    ],
    'KENAK_2017_LIMITS' => [
        'a' => ['wall' => 0.60, 'roof' => 0.50, 'floor' => 0.50, 'windows' => 3.20],
        'b' => ['wall' => 0.50, 'roof' => 0.45, 'floor' => 0.45, 'windows' => 3.00],
        'c' => ['wall' => 0.45, 'roof' => 0.40, 'floor' => 0.40, 'windows' => 2.80],
        'd' => ['wall' => 0.40, 'roof' => 0.35, 'floor' => 0.35, 'windows' => 2.60]
    ],
    'THERMAL_BRIDGES' => [
        'legacy' => 0.00, 
        'medium' => 0.20, 
        'new'    => 0.10  
    ],
    'PRIMARY_ENERGY_FACTORS' => [
        'electricity' => 2.90,
        'oil'         => 1.10
    ],
    'ETOS_LABELS' => [
        'legacy' => 'Πριν το 1980 (Χωρίς μόνωση)',
        'medium' => '1980–2010 (Κανονισμός 1979)',
        'new'    => 'Μετά το 2010 (ΚΕΝΑΚ 2017)'
    ],
    'LAMBDA' => [
        'none' => ['label' => 'Χωρίς μόνωση', 'lambda' => 0],
        'xps' => ['label' => 'XPS (εξηλασμένη)', 'lambda' => 0.035],
        'eps' => ['label' => 'EPS (διογκωμένη)', 'lambda' => 0.038],
        'rockwool' => ['label' => 'Πετροβάμβακας', 'lambda' => 0.040]
    ],
    'KOUFOMATA' => [
        'alum' => ['label' => 'Αλουμίνιο (απλό)', 'u' => 5.5],
        'alum_thermo' => ['label' => 'Αλουμίνιο (θερμο)', 'u' => 3.0],
        'pvc' => ['label' => 'PVC', 'u' => 2.8],
        'wood' => ['label' => 'Ξύλο', 'u' => 2.5]
    ],
    'TZAMI' => [
        'single' => ['label' => 'Μονό', 'u' => 5.8, 'g' => 0.85],
        'double' => ['label' => 'Διπλό', 'u' => 2.8, 'g' => 0.70],
        'double_low_e' => ['label' => 'Low‑E', 'u' => 1.6, 'g' => 0.55],
        'triple' => ['label' => 'Τριπλό', 'u' => 1.0, 'g' => 0.45]
    ],
    'DESIGN_CONDITIONS' => [
        'a' => ['cooling' => ['tdb' => 33], 'heating' => ['tdb' => 5]],
        'b' => ['cooling' => ['tdb' => 36], 'heating' => ['tdb' => 0]],
        'c' => ['cooling' => ['tdb' => 34], 'heating' => ['tdb' => -3]],
        'd' => ['cooling' => ['tdb' => 31], 'heating' => ['tdb' => -8]]
    ],
    'T_IN_DEFAULT' => ['cooling' => 26, 'heating' => 20],
    'U_WALL' => ['legacy' => 2.50, 'medium' => 1.20, 'new' => 0.50],
    'U_ROOF_BASE' => [
        'terrace' => ['legacy' => 1.20, 'medium' => 0.80, 'new' => 0.40],
        'pitched' => ['legacy' => 0.80, 'medium' => 0.55, 'new' => 0.30],
        'heated_above' => ['legacy' => 0.0, 'medium' => 0.0, 'new' => 0.0]
    ],
    'U_FLOOR_BASE' => [
        'ground' => 0.60,
        'pilotis' => 1.20,
        'heated_below' => 0.0
    ],
    'ROOF_MATERIALS' => [
        'concrete' => ['label' => 'Σκυρόδεμα', 'h_o' => 15.0],
        'tiles'    => ['label' => 'Κεραμίδια', 'h_o' => 12.0],
        'bitumen'  => ['label' => 'Ασφαλτόπανο', 'h_o' => 10.0],
        'metal'    => ['label' => 'Λαμαρίνα', 'h_o' => 18.0]
    ],
    'ROOF_COLORS' => [
        'cool_white' => ['label' => 'Λευκό / Ανακλαστικό', 'alpha' => 0.30],
        'light'      => ['label' => 'Ανοιχτό (Μπεζ)', 'alpha' => 0.45],
        'medium'     => ['label' => 'Μεσαίο (Κεραμιδί)', 'alpha' => 0.65],
        'dark'       => ['label' => 'Σκούρο (Μαύρο)', 'alpha' => 0.85]
    ],
    'SHADING_OPTIONS' => [
        'none'      => ['label' => 'Χωρίς σκίαση', 'factor' => 1.0],
        'blinds'    => ['label' => 'Εσωτ. στόρια', 'factor' => 0.5],
        'awning'    => ['label' => 'Εξωτ. τέντα', 'factor' => 0.4],
        'trees'     => ['label' => 'Δέντρα / Φυσική Σκίαση', 'factor' => 0.6],
        'overhang'  => ['label' => 'Στέγαστρο', 'factor' => 0.7]
    ],
    'I_ZONE' => [
        'a' => ['south_vert' => 750, 'horiz' => 750],
        'b' => ['south_vert' => 700, 'horiz' => 650],
        'c' => ['south_vert' => 650, 'horiz' => 600],
        'd' => ['south_vert' => 580, 'horiz' => 550]
    ],
    'PSYCHRO' => ['p_atm' => 101325, 'h_fg' => 2450000]
];
