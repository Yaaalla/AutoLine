<?php
// includes/functions.php

/**
 * Log an administrative action
 */
function log_activity($pdo, $admin_id, $action, $details = "") {
    // Disabled
}

function get_setting($pdo, $key, $default = "") {
    // Reverted to default values
    return $default;
}

function update_setting($pdo, $key, $value) {
    // Disabled
}
?>
