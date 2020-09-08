<?php 
define('_VALID_ACCESS',TRUE);
include "middle/conn.php";
include "middle/functions.php";

header("Content-type: application/json");
echo composeReply("SUCCESS", "Sistem Marketing Properti");
exit;
?>