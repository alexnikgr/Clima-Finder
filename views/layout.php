<?php
/**
 * views/layout.php (V27.0)
 * The Master Template: Manages dynamic theme variables and UI structure.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}

// Logic: Determine if we are in Cooling or Heating mode for the CSS variables
$isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling';
$themeColor = $isCooling ? '#0a84ff' : '#FF9500'; // Blue for Cool, Orange for Heat
$themeLow   = $isCooling ? 'rgba(10, 132, 255, 0.15)' : 'rgba(255, 149, 0, 0.15)';
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClimaFinder | V0.7</title>
    
    <!-- CSS Assets -->
	<link rel="stylesheet" href="public/style-base.css">
	<link rel="stylesheet" href="public/style-ui.css">

    <!-- Dynamic Theme Overrides -->
    <style>
        :root {
            --accent: <?= $themeColor ?>;
            --accent_low: <?= $themeLow ?>;
        }
    </style>
</head>
<body class="<?= $isCooling ? 'theme-cool' : 'theme-heat' ?>">

    <div class="grid">
        
        <!-- HEADER / BRANDING -->
        <header style="grid-column: span 12; display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
            <div style="font-weight: 900; letter-spacing: -1px; font-size: 1.2rem;">
                <a style="all: unset; cursor: pointer;" href="index.php">CLIMA<span style="color: var(--accent)">FINDER</span> <span style="opacity: 0.3; font-weight: 400;">v0.7</span></a>
            </div>
            <div style="font-size: 0.65rem; color: var(--label); font-weight: 800; text-transform: uppercase;">
                Κατάσταση: <?= $isCooling ? 'Σύστημα Ψύξης' : 'Σύστημα Θέρμανσης' ?>
            </div>
        </header>

        <!-- RESULTS DASHBOARD: Only shows if a valid area/height was submitted -->
        <?php if ($results && isset($results['btu'])): ?>
            <?php include 'views/results.php'; ?>
        <?php endif; ?>

        <!-- THE MAIN INPUT FORM -->
        <?php include 'views/form.php'; ?>

    </div>

    <!-- FOOTER / LEGAL -->
    <footer style="max-width: 1400px; margin: 40px auto 20px; padding: 0 12px;">
        <?php include 'views/partials/legal_disclaimer.php'; ?>
        <p style="text-align: center; font-size: 0.6rem; color: var(--label); margin-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
            Physics Engine V27.0 • Refactored for M2 & State Precision
        </p>
    </footer>

</body>
</html>
