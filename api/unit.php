<?php 
define('_VALID_ACCESS',TRUE);

include "../middle/conn.php";
include "../middle/functions.php";

require "../vendor/autoload.php";
use Mailgun\Mailgun;

header("Content-type: application/json");

$errMsg = "";

$result = "";

if(isset($_POST["act"]))  $act = trim($_POST["act"]);
if(isset($_GET["act"]))   $act = trim($_GET["act"]);

if(isset($act) && trim($act) != "") {
    if($act == "tes" || $act == "test") {
        echo composeReply("SUCCESS", "All set !");
        exit;
    }
}

if ($act == "get_unit") {
    $unit = $gPDO->query("SELECT * FROM rs_unit ORDER BY UNIT_KODE")->fetchAll(PDO::FETCH_OBJ);    
        echo composeReply("SUCCESS", "Unit", $unit);
        exit;
}

if ($act == "create_unit") {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            echo composeReply("ERROR", "[Routing ERROR] Internal error.");
            exit;
        }

        // if (!isset($_POST["loginToken"]) || trim($_POST["loginToken"]) == "") {
        //     echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
        //     exit;
        // }
        // $loginToken = trim($_POST["loginToken"]);
        // //cek login token
        // $loginData = verifyStoredLogin($loginToken);
        // if (!$loginData) {
        //     echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
        //     exit;
        // }

        if(!isset($_POST["unitKode"]) || trim($_POST["unitKode"]) == "") {
            echo composeReply("ERROR", "Harap isikan Kode unit");
            exit;
        }
        $unitKode = trim($_POST["unitKode"]);

        if(!isset($_POST["unitMerk"]) || trim($_POST["unitMerk"]) == "") {
            echo composeReply("ERROR", "Harap isikan Merk unit");
            exit;
        }
        $unitMerk = trim($_POST["unitMerk"]);

        if(!isset($_POST["unitWarna"]) || trim($_POST["unitWarna"]) == "") {
            echo composeReply("ERROR", "Harap isikan Warna unit");
            exit;
        }
        $unitWarna = trim($_POST["unitWarna"]);

        if(!isset($_POST["unitHargasewa"]) || trim($_POST["unitHargasewa"]) == "") {
            echo composeReply("ERROR", "Harap isikan Harga sewa unit");
            exit;
        }
        $unitHargasewa = trim($_POST["unitHargasewa"]);

        if (isset($_FILES['uploadFile'])) {
            $fileName = $_FILES['uploadFile']['name'];
            $fileSize = $_FILES['uploadFile']['size'];
            $fileTmp = $_FILES['uploadFile']['tmp_name'];
            $fileType = $_FILES['uploadFile']['type'];
            $fileError = $_FILES['uploadFile']['error'];

            $a = explode(".", $_FILES["uploadFile"]["name"]);
            $fileExt = strtolower(end($a));

            if (isset($fileError) && $fileError > 0) {
                $FILE_UPLOAD_ERROR_INFO = array(
                    0 => 'There is no error, the file uploaded with success',
                    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                    3 => 'The uploaded file was only partially uploaded',
                    4 => 'No file was uploaded',
                    6 => 'Missing a temporary folder',
                    7 => 'Failed to write file to disk.',
                    8 => 'A PHP extension stopped the file upload.',
                );

                echo composeReply("ERROR", "Upload gagal : ".$FILE_UPLOAD_ERROR_INFO[$fileError]);
                exit;
            }

            $arrFileExt = array("jpg", "jpeg", "png");
            if (isset($fileName) && trim($fileName) != "") {
                if (in_array($fileExt, $arrFileExt) === false) {
                    echo composeReply("ERROR", "Harap pilih file image yang sesuai");
                    exit;
                }

                $uploadFile = substr(md5(date("YmdHis")), 0, 5) . "." . $fileExt;
                if (move_uploaded_file($fileTmp, "../uploads/" . $uploadFile)) {
                    $gPDO->prepare("INSERT INTO rs_unit (UNIT_KODE, UNIT_MERK, UNIT_WARNA, UNIT_GAMBAR, UNIT_HARGASEWA) VALUES (?,?,?,?,?)")->execute([$unitKode, $unitMerk, $unitWarna, $uploadFile, $unitHargasewa]);
                    $imgId = $gPDO->lastInsertId();
                    if (isset($imgId) && $imgId > 0) {
                        echo composeReply("SUCCESS", "Data unit telah disimpan");
                    } 
                    else {
                        @unlink("../uploads/" . $uploadFile);
                        echo composeReply("ERROR", "Gagal menyimpan data");
                    }
                    exit;
                } 
                else {
                    echo composeReply("ERROR", "Upload gagal");
                    exit;
                }
            } 
            else {
                echo composeReply("ERROR", "Upload gagal");
                exit;
            }
        } 
        else {
            echo composeReply("ERROR", "Harap upload file");
            exit;
        }
}

if ($act == "update_unit") {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            echo composeReply("ERROR", "[Routing ERROR] Internal error.");
            exit;
        }

        // if (!isset($_POST["loginToken"]) || trim($_POST["loginToken"]) == "") {
        //     echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
        //     exit;
        // }
        // $loginToken = trim($_POST["loginToken"]);
        // //cek login token
        // $loginData = verifyStoredLogin($loginToken);
        // if (!$loginData) {
        //     echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
        //     exit;
        // }

        $unitId = "0";
            if (!isset($_POST["unitId"]) || trim($_POST["unitId"]) == "") {
                echo composeReply("ERROR", "Harap pilih unit");
                exit;
            }
            $unitId = trim($_POST["unitId"]);

            $stmt = $gPDO->prepare("SELECT * FROM rs_unit WHERE UNIT_ID = ?");
            $stmt->execute([$unitId]);
            $unitData = $stmt->fetch(PDO::FETCH_OBJ);
            if(!$unitData) {
                echo composeReply("ERROR", "Data unit tidak dikenal");
                exit;
            }

        if(!isset($_POST["unitKode"]) || trim($_POST["unitKode"]) == "") {
            echo composeReply("ERROR", "Harap isikan Kode unit");
            exit;
        }
        $unitKode = trim($_POST["unitKode"]);

        if(!isset($_POST["unitMerk"]) || trim($_POST["unitMerk"]) == "") {
            echo composeReply("ERROR", "Harap isikan Merk unit");
            exit;
        }
        $unitMerk = trim($_POST["unitMerk"]);

        if(!isset($_POST["unitWarna"]) || trim($_POST["unitWarna"]) == "") {
            echo composeReply("ERROR", "Harap isikan Warna unit");
            exit;
        }
        $unitWarna = trim($_POST["unitWarna"]);

        if(!isset($_POST["unitHargasewa"]) || trim($_POST["unitHargasewa"]) == "") {
            echo composeReply("ERROR", "Harap isikan Harga sewa unit");
            exit;
        }
        $unitHargasewa = trim($_POST["unitHargasewa"]);

        if (isset($_FILES['uploadFile'])) {
            $fileName = $_FILES['uploadFile']['name'];
            $fileSize = $_FILES['uploadFile']['size'];
            $fileTmp = $_FILES['uploadFile']['tmp_name'];
            $fileType = $_FILES['uploadFile']['type'];
            $fileError = $_FILES['uploadFile']['error'];

            $a = explode(".", $_FILES["uploadFile"]["name"]);
            $fileExt = strtolower(end($a));

            if (isset($fileError) && $fileError > 0) {
                $FILE_UPLOAD_ERROR_INFO = array(
                    0 => 'There is no error, the file uploaded with success',
                    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                    3 => 'The uploaded file was only partially uploaded',
                    4 => 'No file was uploaded',
                    6 => 'Missing a temporary folder',
                    7 => 'Failed to write file to disk.',
                    8 => 'A PHP extension stopped the file upload.',
                );

                echo composeReply("ERROR", "Upload gagal : ".$FILE_UPLOAD_ERROR_INFO[$fileError]);
                exit;
            }

            $arrFileExt = array("jpg", "jpeg", "png");
            if (isset($fileName) && trim($fileName) != "") {
                if (in_array($fileExt, $arrFileExt) === false) {
                    echo composeReply("ERROR", "Harap pilih file image yang sesuai");
                    exit;
                }

                if($unitKode != $unitData->{"UNIT_KODE"}) { //perubahan kode unit
                $stmt = $gPDO->prepare("SELECT * FROM rs_unit WHERE UNIT_KODE = ?");
                $stmt->execute([$unitKode]);
                $cek = $stmt->fetch(PDO::FETCH_OBJ);
                if($cek) {
                    echo composeReply("ERROR", "Proses gagal karene kode ".$unitKode." sudah pernah digunakan.\nHarap gunakan kode lain.");
                    exit;
                }
            }

                $uploadFile = substr(md5(date("YmdHis")), 0, 5) . "." . $fileExt;
                if (move_uploaded_file($fileTmp, "../uploads/" . $uploadFile)) {
                    $gPDO->prepare("UPDATE rs_unit SET UNIT_KODE = ?, UNIT_MERK = ?, UNIT_WARNA = ?, UNIT_GAMBAR = ?, UNIT_HARGASEWA = ? WHERE UNIT_ID = ?")->execute([$unitKode, $unitMerk, $unitWarna, $uploadFile, $unitHargasewa, $unitData->{"UNIT_ID"}]);
                echo composeReply("SUCCESS", "Perubahan data unit telah disimpan");
                exit;
                    // $imgId = $gPDO->lastInsertId();
                    // if (isset($imgId) && $imgId > 0) {
                    //     echo composeReply("SUCCESS", "Data unit telah disimpan");
                    // } 
                    // else {
                    //     @unlink("../uploads/" . $uploadFile);
                    //     echo composeReply("ERROR", "Gagal menyimpan data");
                    // }
                    // exit;
                } 
                else {
                    echo composeReply("ERROR", "Upload gagal");
                    exit;
                }
            } 
            else {
                echo composeReply("ERROR", "Upload gagal");
                exit;
            }
        } 
        else {
            echo composeReply("ERROR", "Harap upload file");
            exit;
        }
}

if($act == "delete_unit") {
        if($_SERVER["REQUEST_METHOD"] != "POST") {
            echo composeReply("ERROR", "[Routing ERROR] Internal error.");
            exit;
        }

        // if(isset($_POST["loginToken"]) && trim($_POST["loginToken"]) != "")     $loginToken = trim($_POST["loginToken"]);
        // if(!isset($loginToken)) {
        //     echo composeReply("ERROR", "Akses tidak dikenal", array("API_ACTION" => "LOGOUT"));
        //     exit;
        // }

        // $stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_LOGIN_TOKEN = ?");
        // $stmt->execute([$loginToken]);
        // $userData = $stmt->fetch(PDO::FETCH_OBJ);
        // if(!$userData) {
        //     echo composeReply("ERROR", "User tidak dikenal", array("API_ACTION" => "LOGOUT"));
        //     exit;
        // }

        if(isset($_POST["unitId"]) && trim($_POST["unitId"]) != "") $unitId = trim(strtoupper($_POST["unitId"]));
        if(!isset($unitId)) {
            echo composeReply("ERROR", "Parameter tidak lengkap");
            exit;
        }

        $stmt = $gPDO->prepare("SELECT * FROM rs_unit WHERE UNIT_ID = ?");
        $stmt->execute([$unitId]);
        $unitData = $stmt->fetch(PDO::FETCH_OBJ);
        if(!$unitData) {
            echo composeReply("ERROR", "Data unit tidak dikenal");
            exit;
        }

        //hapus !!
        $gPDO->prepare("DELETE FROM rs_unit WHERE UNIT_ID = ?")->execute([$unitData->{"UNIT_ID"}]);
        	echo composeReply("SUCCESS", "Data unit telah dihapus");
        	exit;
}

else {
    echo composeReply("ERROR", "[Routing ERROR] Internal error.");
    exit;
}
?>