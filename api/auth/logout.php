<?php
// api/auth/logout.php
session_start();
session_destroy();
header('Location: ../../cashier/login.php');
exit;
?>
