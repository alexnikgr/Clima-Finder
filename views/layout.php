<?php
if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Pro MVC</title>
    <link rel="stylesheet" href="public/style.css">
    <style>
        :root {
            <?php $isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling'; ?>
            --accent: <?= $isCooling ? '#0a84ff' : '#FF9500' ?>;
            --accent_low: <?= $isCooling ? 'rgba(10, 132, 255, 0.1)' : 'rgba(255, 149, 0, 0.1)' ?>;
        }
    </style>
</head>
<body>
    <div class="grid">
        <?php if ($results && $results['btu'] > 0) include 'views/results.php'; ?>
        <?php include 'views/form.php'; ?>
    </div>
</body>
</html>
