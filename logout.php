<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header("Pragma: no-cache");
session_start();
$_SESSION = [];
session_unset();
session_destroy();
setcookie(session_name(), '', time() - 3600, '/'); // Clear session cookie
header("Location: login.php");
exit;
?>
