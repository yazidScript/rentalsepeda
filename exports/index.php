<?php
date_default_timezone_set('Asia/Jakarta');

define('_VALID_ACCESS', TRUE);
include "../middle/conn.php";
include "../middle/functions.php";

$act = "";
if(isset($_GET["act"]))		$act = trim($_GET["act"]);
if(isset($_POST["act"]))	$act = trim($_POST["act"]);

$export = "";
if(isset($_GET["export"]))		$export = trim($_GET["export"]);
if(isset($_POST["export"]))		$export = trim($_POST["export"]);

if($export == "excel") {
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	ini_set('memory_limit', '2048M');
	require_once("../middle/phpexcel/Classes/PHPExcel.php");
	error_reporting(E_ALL);
}

if(strtolower($act) == "bookings_download") {
	if($export == "excel") {
		if(isset($_GET["download"]))		$download = trim($_GET["download"]);
		if(isset($_POST["download"]))		$download = trim($_POST["download"]);
		//default value
		if(!isset($download) || trim($download) == "")	$download = "Y";

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Grha Mulia Asri V")
									 ->setLastModifiedBy("Grha Mulia Asri V")
									 ->setTitle("Laporan Booking")
									 ->setSubject("Laporan Booking")
									 ->setDescription("Laporan Booking")
									 ->setKeywords("Laporan Booking")
									 ->setCategory("Laporan Booking");
		//dari template
		$objPHPExcel = PHPExcel_IOFactory::load("templates/template_booking.xlsx");
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setCellValue('B3', tglIndo(date("Y-m-d H:i:s"), "SHORT"));

		$startRow = 6;
		$xlsRow = $startRow;

		$bookings = $gPDO->query("SELECT A.*, B.*, C.U_NAME, C.U_EMAIL, C.U_PHONE, C.U_REFERAL FROM mp_booking AS A INNER JOIN mp_proyek_unit AS B ON A.UNIT_ID = B.UNIT_ID INNER JOIN _users AS C ON A.U_ID = C.U_ID WHERE A.PRO_ID = '".$gClientProjectId."'")->fetchAll(PDO::FETCH_OBJ);
		
		foreach($bookings as $rs_bookings) {
			$sheet->setCellValue('A'.$xlsRow, $rs_bookings->{"BOOK_KODE"});
			$sheet->setCellValue('B'.$xlsRow, tglIndo($rs_bookings->{"BOOK_TGL"}, "SHORT"));
			$sheet->setCellValue('C'.$xlsRow, $rs_bookings->{"UNIT_KODE"}." (".$rs_bookings->{"UNIT_TIPE"}.")");
			$sheet->setCellValue('D'.$xlsRow, $rs_bookings->{"U_NAME"});
			$sheet->setCellValue('E'.$xlsRow, $rs_bookings->{"U_PHONE"});
			$sheet->setCellValue('F'.$xlsRow, $rs_bookings->{"U_REFERAL"});
			$sheet->setCellValue('G'.$xlsRow, $rs_bookings->{"BOOK_CATATAN"});
			$sheet->setCellValue('H'.$xlsRow, $rs_bookings->{"BOOK_METODE_PEMBAYARAN"});
			$sheet->setCellValue('I'.$xlsRow, $rs_bookings->{"BOOK_HARGA_TOTAL"});
			$sheet->setCellValue('J'.$xlsRow, $rs_bookings->{"BOOK_PAJAK_PPN"});

			$tagihan = $gPDO->query("SELECT IFNULL(SUM(BP_NOMINAL),0) AS JUMLAH FROM mp_booking_pembayaran WHERE BOOK_ID = '".$rs_bookings->{"BOOK_ID"}."'")->fetch(PDO::FETCH_OBJ);
			$sheet->setCellValue('K'.$xlsRow, $tagihan->{"JUMLAH"});

			$pembayaran = $gPDO->query("SELECT IFNULL(SUM(BP_NOMINAL_REALISASI),0) AS JUMLAH FROM mp_booking_pembayaran WHERE BOOK_ID = '".$rs_bookings->{"BOOK_ID"}."'")->fetch(PDO::FETCH_OBJ);
			$sheet->setCellValue('L'.$xlsRow, $pembayaran->{"JUMLAH"});
			
			$xlsRow++;
		}
		$sheet->mergeCells('A'.$xlsRow.':H'.$xlsRow); 
		$sheet->setCellValue('A'.$xlsRow, ' T O T A L');
		$sheet->setCellValue('I'.$xlsRow, '=SUM(I'.$startRow.':I'.($xlsRow - 1).')');
		$sheet->setCellValue('J'.$xlsRow, '=SUM(J'.$startRow.':J'.($xlsRow - 1).')');
		$sheet->setCellValue('K'.$xlsRow, '=SUM(K'.$startRow.':K'.($xlsRow - 1).')');
		$sheet->setCellValue('L'.$xlsRow, '=SUM(L'.$startRow.':L'.($xlsRow - 1).')');
		
		$sheet->getStyle('A'.$xlsRow.':L'.$xlsRow)->getFont()->setSize(13);
		$sheet->getStyle('A'.$xlsRow.':L'.$xlsRow)->getFont()->setBold(true);

		$sheet->getStyle('A'.$xlsRow)->applyFromArray(
			array(
				'font' => array('bold' => true),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			)
		);

		//set default sheet on opening file
		$objPHPExcel->setActiveSheetIndex(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		//OUTPUT section
		if(strtoupper($download) == "Y") {
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Laporan Booking Penjualan ('.date("YmdHis").').xls"');
			header('Cache-Control: max-age=0');

			$objWriter->save('php://output');
		}
		else {
			$saveFile = '../downloads/laporan-booking-penjualan-'.date("YmdHis").'.xls';
			$objWriter->save($saveFile);
			//header("Content-type: application/json");
			//echo composeReply("SUCCESS", "Laporan telah dibuat");
			echo "<a href='".$saveFile."' target='_blank'>Download Laporan Booking Penjualan</a>";
		}
	}
}

if(strtolower($act) == "kpr_download") {
	$bKprId = "";
	if(isset($_GET["bKprId"]))		$bKprId = trim($_GET["bKprId"]);
	if(isset($_POST["bKprId"]))		$bKprId = trim($_POST["bKprId"]);

	$bookId = "";
	if(isset($_GET["bookId"]))		$bookId = trim($_GET["bookId"]);
	if(isset($_POST["bookId"]))		$bookId = trim($_POST["bookId"]);

	if(trim($bKprId) == "" && trim($bookId) == "") {
		header("Content-Type: application/json");
		echo composeReply("ERROR", "Parameter tidak lengkap");	
		exit;
	}

	$arrParameters = [];
    $qry = "SELECT A.*,B.*,C.UNIT_TIPE,C.UNIT_KODE,D.U_NAME,D.U_PHONE,D.U_EMAIL,D.U_ADDRESS FROM mp_booking_kpr AS A 
        INNER JOIN mp_booking AS B ON A.BOOK_ID = B.BOOK_ID
        INNER JOIN mp_proyek_unit AS C ON B.UNIT_ID = C.UNIT_ID
        INNER JOIN _users AS D ON B.U_ID = D.U_ID ";
    if($bKprId != "") {
        $qry .= " WHERE A.BKPR_ID = ?";
        $arrParameters[] = $bKprId;
    }
    if($bookId != "") {
        $qry .= " WHERE A.BOOK_ID = ?";
        $arrParameters[] = $bookId;
    }
    $stmt = $gPDO->prepare($qry);
    $stmt->execute($arrParameters);
    $kprData = $stmt->fetch(PDO::FETCH_OBJ);
    if(!$kprData) {
    	header("Content-Type: application/json");
        echo composeReply("ERROR", "Data pengajuan KPR dan/atau booking tidak ditemukan");
        exit;
    }
	
	if($export == "excel") {
		if(isset($_GET["download"]))		$download = trim($_GET["download"]);
		if(isset($_POST["download"]))		$download = trim($_POST["download"]);
		//default value
		if(!isset($download) || trim($download) == "")	$download = "Y";

		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Grha Mulia Asri V")
									 ->setLastModifiedBy("Grha Mulia Asri V")
									 ->setTitle("Laporan Pengajuan KPR GMA 5")
									 ->setSubject("Laporan Pengajuan KPR GMA 5")
									 ->setDescription("Laporan Pengajuan KPR GMA 5")
									 ->setKeywords("Laporan Pengajuan KPR GMA 5")
									 ->setCategory("Laporan Pengajuan KPR GMA 5");
		//dari template
		$objPHPExcel = PHPExcel_IOFactory::load("templates/template_kpr.xlsx");
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		$sheet->setCellValue('B3', tglIndo(date("Y-m-d H:i:s"), "SHORT"));

		$xlsRow = 5;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BOOK_KODE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"U_NAME"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"U_PHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"U_ADDRESS"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BOOK_HARGA_TOTAL"});

	//DATA DIRI
		$xlsRow = 13;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NAMA_LENGKAP"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NO_KTP"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NAMA_GADIS_IBU_KANDUNG"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_RUMAH"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_RT"}."/".$kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_RW"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_KELURAHAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_KECAMATAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_KOTA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_KODE_POS"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NO_TELEPHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NO_HANDPHONE_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NO_HANDPHONE_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_DPT_DIHUBUNGI_JAM"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_EMAIL"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_TEMPAT_LAHIR"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_TANGGAL_LAHIR"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_PENDIDIKAN_TERAKHIR"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_JENIS_PEKERJAAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_NAMA_USAHA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_BENTUK_BADAN_USAHA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA_KOTA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA_KODE_POS"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA_NO_TELEPHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA_EXT"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_ALAMAT_USAHA_NO_FAX"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_LAMA_KERJA_TAHUN"}." Tahun, ".$kprData->{"BKPR_FORM_DATA_DIRI_LAMA_KERJA_BULAN"}." Bulan");
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_STATUS_PEKERJAAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_PENDAPATAN_PER_BULAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_DIRI_BIDANG_USAHA"});
	//------------

	//KONTAK EMERGENCY
		$xlsRow = 45;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_NAMA_LENGKAP"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_HUBUNGAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_NO_TELEPHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_NO_HANDPHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_JENIS_KELAMIN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_JENIS_TEMPAT_TINGGAL"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_RT"}."/".$kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_RW"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_KELURAHAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_KECAMATAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_KOTA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_ALAMAT_RUMAH_KODE_POS"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_KONTAK_EMERGENCY_DPT_DIHUBUNGI_JAM"});
	//------------
		
	//DATA AGUNAN
		$xlsRow = 61;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NAMA_PROPERTI"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_RT"}."/".$kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_RW"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_KELURAHAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_KECAMATAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_KOTA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ALAMAT_KODE_POS"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NO_TELEPON_PENJUAL"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NO_HANDPHONE"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_JENIS_AGUNAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_STATUS_KEPEMILIKAN"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NO_SERTIFIKAT"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_ATAS_NAMA"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_TANGGAL_TERBIT"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_LB"}."/".$kprData->{"BKPR_FORM_DATA_AGUNAN_LT"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NO_IMB"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_AGUNAN_NO_PKS"});
	//------------

	//DATA ASET
		$xlsRow = 82;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_JENIS_SIMPANAN_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_JENIS_SIMPANAN_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_NAMA_BANK_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_NAMA_BANK_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_NILAI_NOMINAL_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_AKTIVA_NILAI_NOMINAL_2"});
		$xlsRow = 89;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_LOKASI_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_LOKASI_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_LBLT_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_LBLT_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_STATUS_ATAS_NAMA_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_STATUS_ATAS_NAMA_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_NILAI_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_TN_TB_NILAI_2"});
		$xlsRow = 98;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_JENIS_MERK_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_JENIS_MERK_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_TAHUN_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_TAHUN_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_ATAS_NAMA_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_ATAS_NAMA_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_NILAI_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_ASET_KENDARAAN_NILAI_2"});
	//------------

	//DATA PINJAMAN LAIN
		$xlsRow = 109;
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_JENIS_KREDIT_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_JENIS_KREDIT_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_TAHUN_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_TAHUN_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_OUTS_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_OUTS_2"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_ANGSURAN_BULAN_1"});
		$sheet->setCellValue('B'.$xlsRow++, $kprData->{"BKPR_FORM_DATA_PINJAMAN_LAIN_ANGSURAN_BULAN_2"});
	//------------

	//IMG DOKUMEN
		$xlsRow = 120;
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_KTP_PEMOHON"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_KTP_PASANGAN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_KARTU_KELUARGA"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SURAT_NIKAH"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SLIP_GAJI_TERAKHIR"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SK_PENGANGKATAN_PEGAWAI_TETAP"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_REKENING_TABUNGAN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_NPWP_SPT"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_AKTA_PENDIRIAN_DAN_PERUBAHAN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SIUP"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_TDP"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SURAT_KETERANGAN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_LAPORAN_KEUANGAN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SURAT_PERNYATAAN_LAIN"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SURAT_PERNYATAAN_BELUM_MEMILIKI_RUMAH"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SURAT_PERMOHONAN_KPR_SUBSIDI"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_SERTIFIKAT"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_IMB"});
		$sheet->setCellValue('B'.$xlsRow++, $gUploadUrl.$kprData->{"BKPR_FORM_DOKUMEN_IMG_PATH_DOKUMEN_LAIN"});
	//------------

		//set default sheet on opening file
		$objPHPExcel->setActiveSheetIndex(0);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

		//OUTPUT section
		if(strtoupper($download) == "Y") {
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="Laporan Pengajuan KPR GMA 5 Booking #'.$kprData->{"BOOK_KODE"}.' ('.date("YmdHis").').xls"');
			header('Cache-Control: max-age=0');

			$objWriter->save('php://output');
		}
		else {
			$saveFile = '../downloads/laporan-kpr-'.$kprData->{"BOOK_KODE"}.'-'.date("YmdHis").'.xls';
			$objWriter->save($saveFile);
			//header("Content-type: application/json");
			//echo composeReply("SUCCESS", "Laporan telah dibuat");
			echo "<a href='".$saveFile."' target='_blank'>Download Laporan Pengajuan KPR Book #".$kprData->{"BOOK_KODE"}."</a>";
		}
	}
}
?>