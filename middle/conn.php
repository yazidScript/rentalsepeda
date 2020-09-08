<?php
//error_reporting(E_ALL);
//error_reporting(0);

if(!defined('_VALID_ACCESS')) die('Permintaan file ditolak!');

date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$db = "rentalsepeda";
$user = "root";
$pass = "";

// $host = "149.28.145.136";
// $db = "markpro";
// $user = "development";
// $pass = "qaz1WSX2cde3";

// $gFirebaseAPIKey = "AAAAfVpyYwo:APA91bF7RztNb_AXVArFgmrh5IHZkx0gbfYfCSWacpDRbK6OemZtd8q6XTtcfDFmxKevMKWAqaV0iJtWezzHNHZFEuYEWU2hA1q4JgAiDoMruEWVrYaz__iH82RC7K3vCM7His9sUOaVRcsMEcoRVquTsS_7qpviUA";
// $gFirebaseAPIKey = "AIzaSyCEmvHdpHVPQ3TDefAfRrxLNiFoEq07JRo";

$gClientProjectId = "RENTALSEPEDA";
$gBaseUrl = "http://".$host."/rentalsepeda/";
$gDownloadUrl = $gBaseUrl."downloads/";
$gExportUrl = $gBaseUrl."exports/index.php?";
$gUploadUrl = $gBaseUrl."uploads/";

$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
  PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
  $gPDO = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>