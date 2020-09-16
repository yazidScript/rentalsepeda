<?php
//error_reporting(E_ALL);
//error_reporting(0);

if(!defined('_VALID_ACCESS')) die('Permintaan file ditolak!');

date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$db = "rentalsepeda";
$user = "root";
$pass = "";

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