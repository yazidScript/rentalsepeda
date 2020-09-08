<?php
date_default_timezone_set('Asia/Jakarta');

define('_VALID_ACCESS', TRUE);
include "../middle/conn.php";
include "../middle/functions.php";

$arrSpesifikasi = array(
	"Pondasi batu kali",
	"Struktur beton bertulang",
	"Dinding bata merah finishing plester aci",
	"Cat V-Tex",
	"Rangka atap baja ringan",
	"Genteng beton",
	"Lantai keramik 40x40 putih polos",
	"Plafon calciboard finishing cat lis gypsum",
	"Kusen kayu lokal finishing cat",
	"Pintu kayu lokal finishing cat",
	"Kloset jongkok",
	"Lantai dan dinding km.mandi keramik",
	"Pintu KM fiber pabrikan",
	"Air artetis",
	"Listrik 900 watt"
);

$arrImages = array("default.png");

$gPDO->prepare("TRUNCATE TABLE mp_proyek_unit_spesifikasi")->execute();
$gPDO->prepare("TRUNCATE TABLE mp_proyek_unit_images")->execute();

$unit = $gPDO->query("SELECT * FROM mp_proyek_unit")->fetchAll(PDO::FETCH_OBJ);
foreach ($unit as $aUnit) {
	foreach ($arrSpesifikasi as $key => $value) {
		$gPDO->prepare("INSERT INTO mp_proyek_unit_spesifikasi (SPEK_NAMA, UNIT_ID, PRO_ID) VALUES (?,?,?)")->execute([$value, $aUnit->{"UNIT_ID"}, $gClientProjectId]);
		$spekId = $gPDO->lastInsertId();
		if(!isset($spekId) || $spekId <= 0)	die("Proses input spesifikasi GAGAL di UNIT_ID #".$aUnit->{"UNIT_ID"}."\n");
	}

	$unitDeskripsi = "Deskripsi ".$aUnit->{"UNIT_KODE"}." (".$aUnit->{"UNIT_TIPE"}.")";
	$unitUTJ = 3000000;
	$unitDP = 7000000;
	$unitTnhLbhLuasM2 = 0;
	$unitThnLbhHargaM2 = 0;
	$unitHargaLumpsum = 150000000;
	$unitImgPath = "default.png";
	$gPDO->prepare("UPDATE mp_proyek_unit SET UNIT_DESKRIPSI = ?, UNIT_UTJ = ?, UNIT_DP = ?, UNIT_TNH_LBH_LUAS_M2 = ?, UNIT_TNH_LBH_HARGA_M2 = ?, UNIT_HARGA_LUMPSUM = ?, UNIT_IMG_PATH = ? ")->execute([$unitDeskripsi, $unitUTJ, $unitDP, $unitTnhLbhLuasM2, $unitThnLbhHargaM2, $unitHargaLumpsum, $unitImgPath]);

	foreach ($arrImages as $key => $value) {
		$gPDO->prepare("INSERT INTO mp_proyek_unit_images (IMG_PATH, UNIT_ID, PRO_ID) VALUES (?,?,?)")->execute([$value, $aUnit->{"UNIT_ID"}, $gClientProjectId]);
		$imgId = $gPDO->lastInsertId();
		if(!isset($imgId) || $imgId <= 0)	die("Proses input image GAGAL di UNIT_ID #".$aUnit->{"UNIT_ID"}."\n");
	}
}

echo "OK\n";
?>