<?php
/**
 * iPark - Logout Page
 */

require_once(__DIR__ . '/config/db.php');
require_once(__DIR__ . '/includes/auth.php');

// Logout the user
logout();

// Redirect to login
header('Location: ' . SITE_URL . '/index.php');
exit();

?>
