<?php
/**
 * views/partials/legal_disclaimer.php
 * UI-only legal notice for liability protection.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 12; background: rgba(255, 69, 58, 0.05); border-color: rgba(255, 69, 58, 0.1); margin-top: 10px;">
    <label style="color: #ff453a; font-weight: 800; font-size: 0.6rem; letter-spacing: 1px;">ΝΟΜΙΚΗ ΣΗΜΕΙΩΣΗ / DISCLAIMER</label>
    <p style="font-size: 0.72rem; color: #8e8e93; line-height: 1.5; margin: 5px 0 0 0;">
        Τα αποτελέσματα του παρόντος υπολογιστή είναι <strong>ενδεικτικά</strong> και βασίζονται σε απλοποιημένα θερμικά μοντέλα. 
        Δεν αποτελούν επίσημη μελέτη και δεν υποκαθιστούν την αυτοψία και τον υπολογισμό από διπλωματούχο μηχανικό. 
        Ο χρήστης αναλαμβάνει την πλήρη ευθύνη για τη χρήση των δεδομένων.
    </p>
</div>