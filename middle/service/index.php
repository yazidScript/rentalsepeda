<?php
define('_VALID_ACCESS',TRUE);
include("../conn.php");
include("../functions.php");

$act = "";
if(isset($_GET["act"]))		$act = $_GET["act"];
if(isset($_POST["act"]))	$act = $_POST["act"];

$result = "";

if(isset($act) && trim($act) != "") {
	if($act == "tes") {
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');

		function sendTestMsg($id, $msg) {
		  echo "id: $id" . PHP_EOL;
		  echo "data: $msg" . PHP_EOL;
		  echo PHP_EOL;
		  ob_flush();
		  flush();
		}
		while(true) {
		  $serverTime = time();
		  sendTestMsg($serverTime, 'server time: ' . date("h:i:s", time()));
		  sleep(1);
		}
	}

	if($act == "cek") {
		header("Content-Type: text/event-stream");
		header("Cache-Control: no-cache");
		header("Connection: keep-alive");

		/**
		 * Constructs the SSE data format and flushes that data to the client.
		 *
		 * @param string $id Timestamp/id of this connection.
		 * @param string $msg Line of text that should be transmitted.
		 */
		function sendMsg($id , $msg) {
		  echo "id: $id" . PHP_EOL;
		  echo "event: sipgdt" . PHP_EOL;
		  echo "data: {\n";
		  echo "data: \"msg\": \"$msg\", \n";
		  echo "data: \"id\": $id\n";
		  echo "data: }\n";
		  echo PHP_EOL;
		  ob_flush();
		  flush();
		}

		$startedAt = time();

		$lastEventId = floatval(isset($_SERVER["HTTP_LAST_EVENT_ID"]) ? $_SERVER["HTTP_LAST_EVENT_ID"] : 0);
		if ($lastEventId == 0)  $lastEventId = floatval(isset($_GET["lastEventId"]) ? $_GET["lastEventId"] : 0);

		do {
		  // Cap connections at 10 seconds. The browser will reopen the connection on close
		  if ((time() - $startedAt) > 10) die();
		  
		  $msg = mysql_query("SELECT * FROM gdt_darurat WHERE D_TIPE_RESPON = 'RESP_PENDING'",$gConn);
		  while ($rs_msg = mysql_fetch_array($msg)) {
		    sendMsg($rs_msg['D_ID'], "[".getReference("KATEGORI_DARURAT",$rs_msg['D_KATEGORI'],$gConn)."] ".substr($rs_msg['D_LOKASI_GEOCODE_ALAMAT'], 0, 65));
		  }
		  $lastEventId = $rs_msg['D_ID'];

		  sleep(10);
		  // If we didn't use a while loop, the browser would essentially do polling
		  // every ~3seconds. Using the while, we keep the connection open and only make
		  // one request.
		} 
		while(true);
	}
}
else {
  echo composeReply("ERROR", "[Routing ERROR] Terjadi kesalahan internal.");
  exit;
}
?>