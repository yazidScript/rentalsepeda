<?php
date_default_timezone_set('Asia/Jakarta');

define('_VALID_ACCESS', TRUE);
include "../middle/conn.php";
include "../middle/functions.php";

$arrToDelete = array();
$bookings = $gPDO->query("SELECT X.* FROM (SELECT *, (TO_DAYS( CURDATE() )-TO_DAYS(BOOK_TGL) ) AS DAYS_PASSED FROM mp_booking) AS X WHERE X.DAYS_PASSED > 1")->fetchAll(PDO::FETCH_OBJ);

foreach ($bookings as $aBooking) {
	echo "Booking #".$aBooking->{"BOOK_ID"}."/".$aBooking->{"BOOK_KODE"}." : ".$aBooking->{"DAYS_PASSED"}." hari\n";
	//cek pembayaran
	$pembayaran = $gPDO->query("SELECT IFNULL(SUM(BP_NOMINAL_REALISASI),0) AS JUMLAH FROM mp_booking_pembayaran WHERE BOOK_ID = '".$aBooking->{"BOOK_ID"}."'")->fetch(PDO::FETCH_OBJ);
	echo "- Pembayaran : ".number_format($pembayaran->{"JUMLAH"},0,',','.')."\n";
	if($pembayaran->{"JUMLAH"} <= 0) {
		$arrToDelete[] = $aBooking->{"BOOK_ID"};
		echo "=> HAPUS BOOKING\n";
	}
	else {
		echo "=> OK\n";
	}
}

foreach ($arrToDelete as $key => $value) {
	echo "\nHapus data booking #".$value." : ";
	$gPDO->prepare("DELETE FROM mp_booking WHERE BOOK_ID = ?")->execute([$value]);
	$gPDO->prepare("UPDATE mp_proyek_unit SET BOOK_ID = 0 WHERE BOOK_ID = ?")->execute([$value]);
	echo "OK\n";
}
?>