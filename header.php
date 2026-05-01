<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Calculator Pro - MVC</title>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            <?php $isCooling = ($inputs['mode'] ?? 'cooling') === 'cooling'; ?>
            --accent: <?= $isCooling ? '#0a84ff' : '#FF9500' ?>;
            --accent_low: <?= $isCooling ? 'rgba(10, 132, 255, 0.1)' : 'rgba(255, 149, 0, 0.1)' ?>;
        }
        /* Custom scrollbar for the Green Report */
        textarea::-webkit-scrollbar { width: 6px; }
        textarea::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 10px; }
        
        /* Print adjustments */
        @media print {
            body { background: white; }
            .grid { display: block; }
            form, .btn { display: none !important; }
        }
    </style>
</head>
<body>
<div class="grid">
