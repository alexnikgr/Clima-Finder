<?php
/**
 * views/layout.php (V21.5)
 * The Master Template: Manages the dynamic UI theme and grid orchestration.
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
    <title>Thermal Pro MVC | V21.5</title>
    
    <!-- Link to our clean, iOS-style CSS -->
    <link rel="stylesheet" href="public/style.css">
    
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
                <a style="all: unset;" href = "">THERMAL<span style="color: var(--accent)">PRO</span> <span style="opacity: 0.3; font-weight: 400;">MVC</span></a>
            </div>
            <div style="font-size: 0.65rem; color: var(--label); font-weight: 800; text-transform: uppercase;">
                Mode: <?= $isCooling ? 'Σύστημα Ψύξης' : 'Σύστημα Θέρμανσης' ?>
            </div>
        </header>

        <!-- RESULTS DASHBOARD (Only shows if a calculation has been run) -->
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
            Physics Engine V21.5
        </p>
    </footer>

</body>
</html>
