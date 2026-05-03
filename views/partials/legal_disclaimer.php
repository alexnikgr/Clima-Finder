<?php
/**
 * views/partials/legal_disclaimer.php (V22.0)
 * Legal notice and liability protection for engineering software.
 */
 if (!defined('APP_RUNNING')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access denied.");
}
?>
<div class="box" style="grid-column: span 12; background: rgba(255, 69, 58, 0.04); border-color: rgba(255, 69, 58, 0.15); margin-top: 15px; padding: 20px;">
    
    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
        <div style="background: #ff453a; color: #fff; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 900;">!</div>
        <label style="color: #ff453a; font-weight: 900; font-size: 0.65rem; letter-spacing: 1.5px; margin: 0;">
            ΝΟΜΙΚΗ ΣΗΜΕΙΩΣΗ & ΠΕΡΙΟΡΙΣΜΟΣ ΕΥΘΥΝΗΣ
        </label>
    </div>

    <p style="font-size: 0.72rem; color: #8e8e93; line-height: 1.6; margin: 0;">
        Τα αποτελέσματα του <strong>Thermal Pro MVC</strong> είναι αποκλειστικά προϊόν θερμικής προσομοίωσης και παρέχονται για <strong>ενδεικτική χρήση</strong>. 
        Οι υπολογισμοί βασίζονται σε παραδοχές των προτύπων ISO 13370 και Sol-Air και ενδέχεται να αποκλίνουν από την πραγματική κατανάλωση. 
        Η παρούσα αναφορά <u>δεν αποτελεί επίσημη μελέτη</u> και δεν υποκαθιστά την υπογεγραμμένη μελέτη από διπλωματούχο Μηχανικό. 
        Ο πάροχος του λογισμικού δεν φέρει καμία ευθύνη για τυχόν αστοχίες στην επιλογή εξοπλισμού ή οικονομικές απώλειες.
    </p>
</div>
