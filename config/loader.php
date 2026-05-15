<?php
/**
 * config/loader.php
 * Automatically merges specialized data files into one master array.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

// 1. Define configuration fragments to load
$files = ['climate', 'materials', 'shading', 'physics'];
$master = [];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file . '.php';
    if (file_exists($path)) {
        // array_replace_recursive ensures nested keys (like climate zones) 
        // are merged correctly rather than overwritten.
        $master = array_replace_recursive($master, require $path);
    }
}

// 2. Global Building Era Labels (Greek Market Localization)
$master['ETOS_LABELS'] = [
    'legacy' => 'Πριν το 1980 (Χωρίς μόνωση)',
    'medium' => '1980–2010 (Κανονισμός 1979)',
    'new'    => 'Μετά το 2010 (ΚΕΝΑΚ 2017)'
];

// 3. Inject into global scope for use in View Partials
$GLOBALS['CONSTANTS'] = $master;

return $master;
