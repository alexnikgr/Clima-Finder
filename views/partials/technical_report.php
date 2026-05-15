<?php
/**
 * views/partials/technical_report.php (V27.0)
 * Technical Audit Terminal display for the Expanded Report.
 */
if (!defined('APP_RUNNING')) die('Direct access denied.');

// Ensure the expanded Helper is available
require_once 'src/ReportHelper.php'; 
?>
<div class="box" style="background: rgba(0,0,0,0.4); width: 680px; flex-shrink: 0; border: 1px solid var(--accent_low); box-shadow: inset 0 0 30px rgba(0,0,0,0.5);">
    
    <!-- Terminal Header -->
    <label style="color: #00ff41; font-weight: 800; display: flex; justify-content: space-between; font-size: 0.65rem; letter-spacing: 1px;">
        <span>📋 ΠΛΗΡΗΣ ΤΕΧΝΙΚΗ ΕΚΘΕΣΗ (FULL AUDIT LOG)</span>
        <span style="opacity: 0.5;">V27.0-STABLE</span>
    </label>
    
    <!-- The Terminal Output Area -->
    <!-- The generate() method now sends the full list of selections and b-factors -->
    <textarea readonly 
              style="width: 100%; height: 750px; background: transparent; border: none; color: #00ff41; font-family: 'Courier New', monospace; font-size: 0.72rem; resize: none; outline: none; line-height: 1.4; margin-top: 15px; white-space: pre; overflow-y: auto;" 
              onclick="this.select()"
              title="Κάντε κλικ για αυτόματη επιλογή όλου του κειμένου"><?= ReportHelper::generate($inputs, $results, $GLOBALS['CONSTANTS']) ?></textarea>
              
    <div style="font-size: 0.55rem; color: #00ff41; opacity: 0.4; text-align: right; margin-top: 5px; text-transform: uppercase;">
        System Output • Verified for M2 Custom Areas
    </div>
</div>
