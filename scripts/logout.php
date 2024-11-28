<?php
// Start the session before destroying it
session_start();

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect to the homepage
header("Location: ../index.html");
exit;
?>
