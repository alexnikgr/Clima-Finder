<?php
/**
 * config/shading.php
 * Bio-climatic shading factors and roof solar properties.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
return [
    'SHADING_OPTIONS' => [
        'none'        => ['label' => 'Χωρίς σκίαση', 'factor' => 1.0],
         'neighbor'    => ['label' => 'Σκιασμένο από γειτονικά', 'factor' => 0.30], // Μόνιμη σκιά
        'shutter'     => ['label' => 'Εξωτερικό Ρολό', 'factor' => 0.15],
        'blinds'      => ['label' => 'Εσωτ. στόρια', 'factor' => 0.50],
        'awning'      => ['label' => 'Εξωτ. τέντα', 'factor' => 0.25],
        'overhang'    => ['label' => 'Στέγαστρο (Fixed)', 'factor' => 0.70],
        'pergola_slat'=> ['label' => 'Πέργκολα (Slats)', 'factor' => 0.60],
        'pergola_sol' => ['label' => 'Πέργκολα (Solid)', 'factor' => 0.35],
        'louvers_bio' => ['label' => 'Βιοκλιματικές Περσίδες', 'factor' => 0.20],
        'trees_dec'   => ['label' => 'Φυλλοβόλα Δέντρα', 'factor' => 0.40],
        'trees_evg'   => ['label' => 'Αειθαλή Δέντρα', 'factor' => 0.50]
    ],
    
    'ROOF_COLORS' => [
        'cool_white' => ['label' => 'Λευκό / Ανακλαστικό', 'alpha' => 0.30],
        'light'      => ['label' => 'Ανοιχτό (Μπεζ)', 'alpha' => 0.45],
        'medium'     => ['label' => 'Μεσαίο (Κεραμιδί)', 'alpha' => 0.65],
        'dark'       => ['label' => 'Σκούρο (Μαύρο)', 'alpha' => 0.85]
    ]
];
