<?php
// logout.php
require 'includes/config.php';

session_unset();
session_destroy();
header("Location: auth.php");
exit();
?>