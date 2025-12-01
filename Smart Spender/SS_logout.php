<?php
// logout.php
session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Redirect back to main page
header("Location: SS_Main_Page_.php");
exit();
