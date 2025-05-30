<?php
session_start();

// Clear the session
session_unset();
session_destroy();

// Redirect to homepage
header('Location: homepage.php');
exit();
?>
