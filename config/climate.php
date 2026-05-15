<?php
/**
 * config/climate.php (V0.7 - Shading Upgrade)
 * Design temperatures, climate zone parameters, and orientation-weighted peak solar irradiance.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

return [
    'CLIMATE_ZONES' => [
        'a' => ['label' => 'Ζώνη Α (Ηράκλειο/Νότια Ελλάδα)'],
        'b' => ['label' => 'Ζώνη Β (Αθήνα/Κεντρική Ελλάδα)'],
        'c' => ['label' => 'Ζώνη Γ (Θεσσαλονίκη/Βόρεια Ελλάδα)'],
        'd' => ['label' => 'Ζώνη Δ (Φλώρινα/Ορεινά)']
    ],
    
    // Design Outdoor Dry Bulb Temperatures (Tdb) per Zone
    // Source: TEE-KENAK Technical Guidelines
    'DESIGN_CONDITIONS' => [
        'a' => ['cooling' => ['tdb' => 33], 'heating' => ['tdb' => 5]],
        'b' => ['cooling' => ['tdb' => 36], 'heating' => ['tdb' => 0]],
        'c' => ['cooling' => ['tdb' => 34], 'heating' => ['tdb' => -3]],
        'd' => ['cooling' => ['tdb' => 31], 'heating' => ['tdb' => -8]]
    ],
    
    // Default Internal Setpoints (T-in) 
    // Cooling: 26°C for energy saving | Heating: 20°C for comfort
    'T_IN_DEFAULT' => [
        'cooling' => 26, 
        'heating' => 20
    ],

    // Peak Solar Irradiance Design Matrix (W/m²) per Cardinal Direction
    // Source: TOTEE 20701-1 Peak Loads Engineering Technical Framework
    'SOLAR_IRRADIANCE' => [
        'cooling' => [
            'north' => 150, // Diffuse skies radiation profile
            'south' => 450, // High summer solar zenith path decreases vertical envelope strike pressure
            'east'  => 720, // Severe direct morning radiant horizontal line loads
            'west'  => 720  // Severe direct afternoon radiant horizontal line loads
        ],
        'heating' => [
            'north' => 80,
            'south' => 550, // Low winter tracking solar angle maximizes orthogonal strike on south glass
            'east'  => 300,
            'west'  => 300
        ]
    ]
];
