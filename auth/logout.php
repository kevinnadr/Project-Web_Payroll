<?php
session_start();
session_destroy();
// Redirect ke halaman login (sesama folder auth)
header("Location: index.php");
exit;
?>