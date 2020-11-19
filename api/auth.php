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

    if($act == "login") {
        if($_SERVER["REQUEST_METHOD"] != "POST") {
            echo composeReply("ERROR", "[Routing ERROR] Internal error.");
            exit;
        }

        if(!isset($_POST["username"]) || trim($_POST["username"]) == "")  {
            echo composeReply("ERROR", "Harap isikan email anda");
            exit;
        }
        $id = trim($_POST["username"]);
        // if(!isset($_POST["password"]) || trim($_POST["password"]) == "")  {
        //     echo composeReply("ERROR", "Parameter tidak lengkap");
        //     exit;
        // }
        if (isset($_POST["passwordEnc"]) && trim($_POST["passwordEnc"]) != "") $passwordEnc = trim($_POST["passwordEnc"]);
        if (isset($passwordEnc)) {
            require "../middle/MCrypt.php";
            $mcrypt = new MCrypt();
            #Encrypt
            //$encrypted = $mcrypt->encrypt("Text to encrypt");

            #Decrypt
            $decrypted = $mcrypt->decrypt($passwordEnc);
            //overwrite
            $password = $decrypted;
        } else {
            if (!isset($_POST["password"]) || trim($_POST["password"]) == "") {
                echo composeReply("ERROR", "Password harus diisi");
                exit;
            }
            $password = trim($_POST["password"]);
        }


        // $username = trim($_POST["username"]);
        // $password = trim($_POST["password"]);

        $stmt = $gPDO->prepare("SELECT * FROM _users WHERE (U_ID = ? OR U_EMAIL = ? OR U_PHONE = ?) AND U_PASSWORD_HASH = ? AND U_STATUS = 'USER_ACTIVE'");
        $stmt->execute([$id, $id, $id, md5($password)]);
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$userData) {
            echo composeReply("ERROR", "User tidak dikenal atau account belum aktif");
            exit;
        }


        $loginToken = substr(md5($id . date("YmdHis")), 0, 20);
        $gPDO->prepare("UPDATE _users SET U_LOGIN_TOKEN = ? WHERE U_ID = ?")->execute([$loginToken, $userData->{"U_ID"}]);


        echo composeReply("SUCCESS", "Logged in", array(
            "LOGIN_ID" => $userData->{"U_ID"},
            "LOGIN_NAME" => $userData->{"U_NAME"},
            "U_LOGIN_TOKEN" => $loginToken,
            "GROUP_ROLE" => $userData->{"U_GROUP_ROLE"},
            "LOGIN_EMAIL" => $id,
            "LOGIN_PASSWORD" => $password)
        );
        exit;
    }

    if ($act == "register_user") {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            echo composeReply("ERROR", "[Routing ERROR] Terjadi kesalahan internal.");
            exit;
        }

        //cek apakah proses dari backend
        if(isset($_POST["loginToken"]) && trim($_POST["loginToken"]) != "") {
            $loginToken = trim($_POST["loginToken"]);
            $loginData = verifyStoredLogin($loginToken);
            if(!$loginData) {
                echo composeReply("ERROR", "User tidak dikenal");
                exit;
            }
            else {
                if($loginData->{"U_GROUP_ROLE"} != "GR_ADMINISTRATOR") {
                    echo composeReply("ERROR", "User tidak berwenang melakukan tindakan ini");
                    exit;
                }
            }
        }

        if (!isset($_POST["id"]) || trim($_POST["id"]) == "") {
            echo composeReply("ERROR", "Harap isikan nomor ponsel Anda");
            exit;
        }
        $id = formatPonsel(trim($_POST["id"]), "0");

        if (isset($_POST["passwordEnc"]) && trim($_POST["passwordEnc"]) != "") $passwordEnc = trim($_POST["passwordEnc"]);
        if (isset($passwordEnc)) {
            require "../middle/MCrypt.php";
            $mcrypt = new MCrypt();
            #Encrypt
            //$encrypted = $mcrypt->encrypt("Text to encrypt");

            #Decrypt
            $decrypted = $mcrypt->decrypt($passwordEnc);
            //overwrite
            $password = $decrypted;
        }
        else {
            if (!isset($_POST["password"]) || trim($_POST["password"]) == "") {
                echo composeReply("ERROR", "Password harus diisi");
                exit;
            }
            $password = trim($_POST["password"]);
        }

        if (!isset($_POST["name"]) || trim($_POST["name"]) == "") {
            echo composeReply("ERROR", "Harap isikan nama Anda");
            exit;
        }
        $name = trim($_POST["name"]);

        if (!isset($_POST["address"]) || trim($_POST["address"]) == "") {
            echo composeReply("ERROR", "Harap isikan alamat Anda");
            exit;
        }
        $address = trim($_POST["address"]);

        if (!isset($_POST["noktp"]) || trim($_POST["noktp"]) == "") {
            echo composeReply("ERROR", "Harap isikan nomer KTP Anda");
            exit;
        }
        $noktp = trim($_POST["noktp"]);

        if (!isset($_POST["email"]) || trim($_POST["email"]) == "") {
            echo composeReply("ERROR", "Harap isikan email Anda");
            exit;
        }
        $email = trim($_POST["email"]);

        $fcmToken = "-";
        if (isset($_POST["fcmToken"]) && trim($_POST["fcmToken"]) != "") $fcmToken = trim($_POST["fcmToken"]);

        $deviceId = "-";
        if (isset($_POST["deviceId"]) && trim($_POST["deviceId"]) != "") $deviceId = trim($_POST["deviceId"]);

        $referal = $gClientProjectId;
        if (isset($_POST["referal"]) && trim($_POST["referal"]) != "")  $referal = trim($_POST["referal"]);
        // if (!isset($_POST["referal"]) || trim($_POST["referal"]) == "") {
        //     echo composeReply("ERROR", "Harap tentukan dari mana Anda memperoleh informasi produk kami");
        //     exit;
        // }
        // $referal = trim($_POST["referal"]);

        //cek apakah id available
        //pake PDO prepare krn query ini terima input dari user -> menghindari sql injection
        $stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_ID = ?");
        $stmt->execute(array($id));
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userData) {
            echo composeReply("ERROR", "Maaf, nomor ponsel sudah pernah digunakan. Silahkan gunakan nomor ponsel lain.");
            exit;
        }

        $stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_EMAIL = ?");
        $stmt->execute(array($email));
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userData) {
            echo composeReply("ERROR", "Maaf, alamat email sudah pernah digunakan. Silahkan gunakan alamat email lain.");
            exit;
        }

        $gPDO->prepare("INSERT INTO _users (
            U_ID,
            U_PASSWORD,
            U_PASSWORD_HASH,
            U_NAME,
            U_GROUP_ROLE,
            U_REG_DATE,
            U_DEVICE_ID,
            U_PHONE,
            U_FCM_TOKEN,
            U_ADDRESS,
            U_AUTHORITY_ID_1,
            U_EMAIL,
            U_REFERAL) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute(array(
            $id,
            $password,
            md5($password),
            strtoupper($name),
            'GR_ADMIN',
            date("Y-m-d"),
            $deviceId,
            $id,
            $fcmToken,
            $address,
            $noktp,
            $email,
            $referal
        ));

        //cek apakah data user berhasil diinput
        $stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_ID = ?");
        $stmt->execute(array($id));
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userData) {
            echo composeReply("SUCCESS", "Data user baru telah disimpan");
        }
        else {
            echo composeReply("ERROR", "Proses registrasi gagal");

        }
    }

    if ($act == "get_user") {
        if (isset($_POST["loginToken"]) && trim($_POST["loginToken"]) != "") $loginToken = trim($_POST["loginToken"]);
        if (isset($_GET["loginToken"]) && trim($_GET["loginToken"]) != "")   $loginToken = trim($_GET["loginToken"]);
        if (!isset($loginToken)) {
            echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
            exit;
        }
        $loginData = verifyStoredLogin($loginToken);
        if (!$loginData) {
            echo composeReply("ERROR", "Silahkan login dahulu untuk mengakses fitur ini");
            exit;
        }

            $konsumen = $gPDO->query("SELECT * FROM _users WHERE U_GROUP_ROLE = 'GR_KONSUMEN'")->fetchAll(PDO::FETCH_OBJ);
            echo composeReply("SUCCESS", "Konsumen", $konsumen);
        exit;
    }

    if($act == "delete_user") {
        if(isset($_GET["loginToken"]) && trim($_GET["loginToken"]) != "")       $loginToken = trim($_GET["loginToken"]);
        if(isset($_POST["loginToken"]) && trim($_POST["loginToken"]) != "")     $loginToken = trim($_POST["loginToken"]);
        if(!isset($loginToken)) {
            echo composeReply("ERROR", "Akses tidak dikenal", array("API_ACTION" => "LOGOUT"));
            exit;
        }

        $stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_LOGIN_TOKEN = ?");
        $stmt->execute([$loginToken]);
        $userData = $stmt->fetch(PDO::FETCH_OBJ);
        if(!$userData) {
            echo composeReply("ERROR", "User tidak dikenal", array("API_ACTION" => "LOGOUT"));
            exit;
        }

        if (!isset($_POST["uId"]) || trim($_POST["uId"]) == "") {
            echo composeReply("ERROR", "Harap pilih user yang akan dihapus");
            exit;
        }
        $uId = trim($_POST["uId"]);

        $gPDO->prepare("DELETE FROM _users WHERE U_ID = ?")->execute([$uId]);

        echo composeReply("SUCCESS", "Data user telah dihapus");
        exit;

    }


}
else {
    echo composeReply("ERROR", "[Routing ERROR] Internal error.");
    exit;
}
?>
