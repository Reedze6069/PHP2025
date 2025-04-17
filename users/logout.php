<?php
// logout.php - Log the user out by destroying the session

session_start();        // Continue the session to access session data
session_unset();        // Unset all session variables
session_destroy();      // Destroy the session entirely

// After this, the user is effectively logged out.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Logout</title>
</head>
<body>
    <p>You have been logged out.</p>
    <p><a href="login.php">Login again</a></p>
</body>
</html>
