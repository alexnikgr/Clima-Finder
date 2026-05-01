<?php
/**
 * config/climate.php
 * Design temperatures and climate zone parameters.
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
    'DESIGN_CONDITIONS' => [
        'a' => ['cooling' => ['tdb' => 33], 'heating' => ['tdb' => 5]],
        'b' => ['cooling' => ['tdb' => 36], 'heating' => ['tdb' => 0]],
        'c' => ['cooling' => ['tdb' => 34], 'heating' => ['tdb' => -3]],
        'd' => ['cooling' => ['tdb' => 31], 'heating' => ['tdb' => -8]]
    ],
    'T_IN_DEFAULT' => [
        'cooling' => 26, 
        'heating' => 20
    ]
];
