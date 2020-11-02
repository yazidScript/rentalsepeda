<?php
date_default_timezone_set('Asia/Jakarta');

function antiInjection($data) {
  $filter_sql = mysqli_real_escape_string(stripslashes(strip_tags(htmlspecialchars($data,ENT_QUOTES))));
  return $filter_sql;
}

function formatRupiah($angka){
	$rupiah = number_format($angka,0,',','.');
	return $rupiah;
}

function terbilang($satuan){  
	$huruf = array ("", "SATU", "DUA", "TIGA", "EMPAT", "LIMA", "ENAM","TUJUH", "DELAPAN", "SEMBILAN", "SEPULUH","SEBELAS");  

	if($satuan < 12) {
		return " ".$huruf[$satuan];  
	}
	elseif($satuan < 20) {
		return terbilang($satuan - 10)." BELAS";  
	}
	elseif($satuan < 100) {
		return terbilang($satuan / 10)." PULUH".terbilang($satuan % 10);  
	}
	elseif($satuan < 200) {
		return "seratus".terbilang($satuan - 100);  
	}
	elseif($satuan < 1000) {
		return terbilang($satuan / 100)." RATUS".terbilang($satuan % 100);  
	}
	elseif($satuan < 2000) {
		return "seribu".terbilang($satuan - 1000);
	}
	elseif($satuan < 1000000) {
		return terbilang($satuan / 1000)." RIBU". terbilang($satuan % 1000);   
	}
	elseif($satuan < 1000000000) {
		return terbilang($satuan / 1000000)." JUTA".terbilang($satuan % 1000000);   
	}
	elseif($satuan < 1000000000000) {
		return terbilang($satuan / 1000000)." MILYAR".terbilang($satuan % 1000000);   
	}
	else {
		echo "Angka terlalu besar";  
	}
}

function left($str, $length) {
	return substr($str, 0, $length);
}

function right($str, $length) {
    return substr($str, -$length);
}

function randomDigits($length){
	$digits = "";
	$numbers = range(0,9);
	shuffle($numbers);
	for($i = 0;$i < $length;$i++) {
		 $digits .= $numbers[$i];
	}
	return $digits;
}

function simpleEncrypt($text) {
	$salt ='secret text shared by both ends';
	return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}
 
function simpleDecrypt($text) {
	$salt ='secret text shared by both ends';
	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

// This function will proportionally resize image
function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType) {
	//Check Image size is not 0
	if($CurWidth <= 0 || $CurHeight <= 0) {
		return false;
	}
	 
	//Construct a proportional size of new image
	$ImageScale         = min($MaxSize/$CurWidth, $MaxSize/$CurHeight);
	$NewWidth           = ceil($ImageScale*$CurWidth);
	$NewHeight          = ceil($ImageScale*$CurHeight);
	$NewCanves          = imagecreatetruecolor($NewWidth, $NewHeight);
	 
	// Resize Image
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight)) {
		switch(strtolower($ImageType)) {
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
				
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;         
				
			case 'image/jpeg':
			
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
				
			default:
					return false;
		}
		
		//Destroy image, frees memory  
		if(is_resource($NewCanves)) imagedestroy($NewCanves);
		return true;
	}
}

//This function corps image to create exact square images, no matter what its original size!
function cropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType) {    
	//Check Image size is not 0
	if($CurWidth <= 0 || $CurHeight <= 0) {
		return false;
	}
	 
	//abeautifulsite.net has excellent article about "Cropping an Image to Make Square bit.ly/1gTwXW9
	if($CurWidth>$CurHeight) {
		$y_offset = 0;
		$x_offset = ($CurWidth - $CurHeight) / 2;
		$square_size    = $CurWidth - ($x_offset * 2);
	}
	else{
		$x_offset = 0;
		$y_offset = ($CurHeight - $CurWidth) / 2;
		$square_size = $CurHeight - ($y_offset * 2);
	}
	 
	$NewCanves  = imagecreatetruecolor($iSize, $iSize);
	if(imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size)) {
		switch(strtolower($ImageType)) {
			case 'image/png':
				imagepng($NewCanves,$DestFolder);
				break;
				
			case 'image/gif':
				imagegif($NewCanves,$DestFolder);
				break;         
				
			case 'image/jpeg':
			
			case 'image/pjpeg':
				imagejpeg($NewCanves,$DestFolder,$Quality);
				break;
				
			default:
				return false;
		}
		
		//Destroy image, frees memory  
		if(is_resource($NewCanves)) imagedestroy($NewCanves);
		return true;

	}     
}

function composeReply($status,$msg, $payload = null) {
	$reply = json_encode(array(
		// "SENDER" => getSetting("APP_NAME"),
		"STATUS" => $status,
		"MESSAGE" => $msg,
		"PAYLOAD" => $payload)
	);
	
	return $reply;
}

function mysqli_Insert($table, $inserts, $conn) {
	$count = count($inserts);
	$values = array_map('mysqli_real_escape_string', array_fill(1,count($inserts),$conn), array_values($inserts));
	$keys = array_keys($inserts);

	return mysqli_query($conn, 'INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')');
}

function mysqli_Update($table_name, $form_data, $where_clause = '', $conn) {
	// check for optional where clause
	$whereSQL = '';
	if(!empty($where_clause)) {
		// check to see if the 'where' keyword exists
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
			// not found, add key word
			$whereSQL = " WHERE ".$where_clause;
		} 
		else {
			$whereSQL = " ".trim($where_clause);
		}
	}

	// start the actual SQL statement
	$sql = "UPDATE ".$table_name." SET ";

	// loop and build the column /
	$sets = array();
	foreach($form_data as $column => $value) {
		$sets[] = "`".$column."` = '".$value."'";
	}
	$sql .= implode(', ', $sets);

	// append the where statement
	$sql .= $whereSQL;

	// run and return the query result
	return mysqli_query($conn, $sql);
}

//the where clause is left optional incase the user wants to delete every row!
function mysqli_Delete($table_name, $where_clause = '', $conn) {
	// check for optional where clause
	$whereSQL = '';
	if(!empty($where_clause)) {
		// check to see if the 'where' keyword exists
		if(substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
			// not found, add keyword
			$whereSQL = " WHERE ".$where_clause;
		} 
		else {
			$whereSQL = " ".trim($where_clause);
		}
	}
	// build the query
	$sql = "DELETE FROM ".$table_name.$whereSQL;

	// run and return the query result resource
	return mysqli_query($conn, $sql);
}

function tglIndo($tgl,$mode) {
	if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00" && $tgl != "-") {
		$t = explode("-",$tgl);
		$bln = array();
		$bln["01"]["LONG"] = "Januari";
		$bln["01"]["SHORT"] = "Jan";
		$bln["1"]["LONG"] = "Januari";
		$bln["1"]["SHORT"] = "Jan";
		$bln["02"]["LONG"] = "Februari";
		$bln["02"]["SHORT"] = "Feb";
		$bln["2"]["LONG"] = "Februari";
		$bln["2"]["SHORT"] = "Feb";
		$bln["03"]["LONG"] = "Maret";
		$bln["03"]["SHORT"] = "Mar";
		$bln["3"]["LONG"] = "Maret";
		$bln["3"]["SHORT"] = "Mar";		
		$bln["04"]["LONG"] = "April";
		$bln["04"]["SHORT"] = "Apr";
		$bln["4"]["LONG"] = "April";
		$bln["4"]["SHORT"] = "Apr";
		$bln["05"]["LONG"] = "Mei";
		$bln["05"]["SHORT"] = "Mei";
		$bln["5"]["LONG"] = "Mei";
		$bln["5"]["SHORT"] = "Mei";
		$bln["06"]["LONG"] = "Juni";
		$bln["06"]["SHORT"] = "Jun";
		$bln["6"]["LONG"] = "Juni";
		$bln["6"]["SHORT"] = "Jun";
		$bln["07"]["LONG"] = "Juli";
		$bln["07"]["SHORT"] = "Jul";
		$bln["7"]["LONG"] = "Juli";
		$bln["7"]["SHORT"] = "Jul";
		$bln["08"]["LONG"] = "Agustus";
		$bln["08"]["SHORT"] = "Ags";
		$bln["8"]["LONG"] = "Agustus";
		$bln["8"]["SHORT"] = "Ags";
		$bln["09"]["LONG"] = "September";
		$bln["09"]["SHORT"] = "Sep";
		$bln["9"]["LONG"] = "September";
		$bln["9"]["SHORT"] = "Sep";
		$bln["10"]["LONG"] = "Oktober";
		$bln["10"]["SHORT"] = "Okt";
		$bln["11"]["LONG"] = "November";
		$bln["11"]["SHORT"] = "Nov";
		$bln["12"]["LONG"] = "Desember";
		$bln["12"]["SHORT"] = "Des";
		
		$b = $t[1];
		
		if (strpos($t[2], ":") === false) { //tdk ada format waktu
			$jam = "";
		}
		else {
			$j = explode(" ",$t[2]);
			$t[2] = $j[0];
			$jam = $j[1];
		}
		
		return $t[2]." ".$bln[$b][$mode]." ".$t[0]." ".$jam;
	}
	else {
		return "-";
	}
}

function blnIndo($aBln,$mode) {
	$bln = array();
	$bln["01"]["LONG"] = "Januari";
	$bln["01"]["SHORT"] = "Jan";
	$bln["1"]["LONG"] = "Januari";
	$bln["1"]["SHORT"] = "Jan";
	$bln["02"]["LONG"] = "Februari";
	$bln["02"]["SHORT"] = "Feb";
	$bln["2"]["LONG"] = "Februari";
	$bln["2"]["SHORT"] = "Feb";
	$bln["03"]["LONG"] = "Maret";
	$bln["03"]["SHORT"] = "Mar";
	$bln["3"]["LONG"] = "Maret";
	$bln["3"]["SHORT"] = "Mar";		
	$bln["04"]["LONG"] = "April";
	$bln["04"]["SHORT"] = "Apr";
	$bln["4"]["LONG"] = "April";
	$bln["4"]["SHORT"] = "Apr";
	$bln["05"]["LONG"] = "Mei";
	$bln["05"]["SHORT"] = "Mei";
	$bln["5"]["LONG"] = "Mei";
	$bln["5"]["SHORT"] = "Mei";
	$bln["06"]["LONG"] = "Juni";
	$bln["06"]["SHORT"] = "Jun";
	$bln["6"]["LONG"] = "Juni";
	$bln["6"]["SHORT"] = "Jun";
	$bln["07"]["LONG"] = "Juli";
	$bln["07"]["SHORT"] = "Jul";
	$bln["7"]["LONG"] = "Juli";
	$bln["7"]["SHORT"] = "Jul";
	$bln["08"]["LONG"] = "Agustus";
	$bln["08"]["SHORT"] = "Ags";
	$bln["8"]["LONG"] = "Agustus";
	$bln["8"]["SHORT"] = "Ags";
	$bln["09"]["LONG"] = "September";
	$bln["09"]["SHORT"] = "Sep";
	$bln["9"]["LONG"] = "September";
	$bln["9"]["SHORT"] = "Sep";
	$bln["10"]["LONG"] = "Oktober";
	$bln["10"]["SHORT"] = "Okt";
	$bln["11"]["LONG"] = "November";
	$bln["11"]["SHORT"] = "Nov";
	$bln["12"]["LONG"] = "Desember";
	$bln["12"]["SHORT"] = "Des";

	return $bln[$aBln][$mode];
}

function tglInggris($tgl,$mode) {
	if($tgl != "" && $mode != "" && $tgl!= "0000-00-00" && $tgl != "0000-00-00 00:00:00" && $tgl != "-") {
		$t = explode("-",$tgl);
		$bln = array();
		$bln["01"]["LONG"] = "January";
		$bln["01"]["SHORT"] = "Jan";
		$bln["1"]["LONG"] = "January";
		$bln["1"]["SHORT"] = "Jan";
		$bln["02"]["LONG"] = "February";
		$bln["02"]["SHORT"] = "Feb";
		$bln["2"]["LONG"] = "February";
		$bln["2"]["SHORT"] = "Feb";
		$bln["03"]["LONG"] = "March";
		$bln["03"]["SHORT"] = "Mar";
		$bln["3"]["LONG"] = "March";
		$bln["3"]["SHORT"] = "Mar";		
		$bln["04"]["LONG"] = "April";
		$bln["04"]["SHORT"] = "Apr";
		$bln["4"]["LONG"] = "April";
		$bln["4"]["SHORT"] = "Apr";
		$bln["05"]["LONG"] = "May";
		$bln["05"]["SHORT"] = "May";
		$bln["5"]["LONG"] = "May";
		$bln["5"]["SHORT"] = "May";
		$bln["06"]["LONG"] = "June";
		$bln["06"]["SHORT"] = "Jun";
		$bln["6"]["LONG"] = "June";
		$bln["6"]["SHORT"] = "Jun";
		$bln["07"]["LONG"] = "July";
		$bln["07"]["SHORT"] = "Jul";
		$bln["7"]["LONG"] = "July";
		$bln["7"]["SHORT"] = "Jul";
		$bln["08"]["LONG"] = "August";
		$bln["08"]["SHORT"] = "Aug";
		$bln["8"]["LONG"] = "August";
		$bln["8"]["SHORT"] = "Aug";
		$bln["09"]["LONG"] = "September";
		$bln["09"]["SHORT"] = "Sep";
		$bln["9"]["LONG"] = "September";
		$bln["9"]["SHORT"] = "Sep";
		$bln["10"]["LONG"] = "October";
		$bln["10"]["SHORT"] = "Oct";
		$bln["11"]["LONG"] = "November";
		$bln["11"]["SHORT"] = "Nov";
		$bln["12"]["LONG"] = "December";
		$bln["12"]["SHORT"] = "Dec";
		
		$b = $t[1];
		
		if (strpos($t[2], ":") === false) { //tdk ada format waktu
			$jam = "";
		}
		else {
			$j = explode(" ",$t[2]);
			$t[2] = $j[0];
			$jam = $j[1];
		}
		
		return $t[2]."-".$bln[$b][$mode]."-".$t[0]." ".$jam;
	}
	else {
		return "-";
	}
}

function blnInggris($aBln,$mode) {
	$bln = array();
	$bln["01"]["LONG"] = "January";
	$bln["01"]["SHORT"] = "Jan";
	$bln["1"]["LONG"] = "January";
	$bln["1"]["SHORT"] = "Jan";
	$bln["02"]["LONG"] = "February";
	$bln["02"]["SHORT"] = "Feb";
	$bln["2"]["LONG"] = "February";
	$bln["2"]["SHORT"] = "Feb";
	$bln["03"]["LONG"] = "March";
	$bln["03"]["SHORT"] = "Mar";
	$bln["3"]["LONG"] = "March";
	$bln["3"]["SHORT"] = "Mar";		
	$bln["04"]["LONG"] = "April";
	$bln["04"]["SHORT"] = "Apr";
	$bln["4"]["LONG"] = "April";
	$bln["4"]["SHORT"] = "Apr";
	$bln["05"]["LONG"] = "May";
	$bln["05"]["SHORT"] = "May";
	$bln["5"]["LONG"] = "May";
	$bln["5"]["SHORT"] = "May";
	$bln["06"]["LONG"] = "June";
	$bln["06"]["SHORT"] = "Jun";
	$bln["6"]["LONG"] = "June";
	$bln["6"]["SHORT"] = "Jun";
	$bln["07"]["LONG"] = "July";
	$bln["07"]["SHORT"] = "Jul";
	$bln["7"]["LONG"] = "July";
	$bln["7"]["SHORT"] = "Jul";
	$bln["08"]["LONG"] = "August";
	$bln["08"]["SHORT"] = "Ags";
	$bln["8"]["LONG"] = "August";
	$bln["8"]["SHORT"] = "Ags";
	$bln["09"]["LONG"] = "September";
	$bln["09"]["SHORT"] = "Sep";
	$bln["9"]["LONG"] = "September";
	$bln["9"]["SHORT"] = "Sep";
	$bln["10"]["LONG"] = "October";
	$bln["10"]["SHORT"] = "Oct";
	$bln["11"]["LONG"] = "November";
	$bln["11"]["SHORT"] = "Nov";
	$bln["12"]["LONG"] = "December";
	$bln["12"]["SHORT"] = "Des";

	return $bln[$aBln][$mode];
}

function dayDifference($dateA,$dateB,$inDaysOnly) {
	$date1 = new DateTime($dateA);
	$date2 = new DateTime($dateB);
	$interval = $date1->diff($date2);

	if($inDaysOnly == TRUE) {
		// shows the total amount of days (not divided into years, months and days like above)
		$arrDiff["DAY"] = $interval->days;
	}
	else {
		$arrDiff["DAY"]	= $interval->d;
		$arrDiff["MONTH"] = $interval->m;
		$arrDiff["YEAR"] = $interval->y;
	}
	
	return $arrDiff;
}

function cURLPost($url,$params) {
	$postData = '';
	//create name value pairs separated by &
	foreach($params as $k => $v) { 
		$postData .= $k . '='.$v.'&'; 
	}
	rtrim($postData, '&');

	$ch = curl_init();  

	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	curl_setopt($ch,CURLOPT_POST, count($postData));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);    
	@curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
	@curl_setopt($ch,CURLOPT_MAXREDIRS,2);//only 2 redirects
	curl_setopt($ch,CURLOPT_COOKIEFILE, 'cookie.txt' );
  curl_setopt($ch,CURLOPT_COOKIEJAR, 'cookiejar.txt' );

	$output = curl_exec($ch);

	curl_close($ch);
	return $output; 
}

function isJSON($string){
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

function getLastDate($bln,$thn) {
	if($bln >= 1 && $bln <= 12 && $thn >= 1901) {
		return date("Y-m-t", mktime(0, 0, 0, $bln, 1, $thn));
	}
}

function addDaysWithDate($date,$days){
	$date = strtotime("+".$days." days", strtotime($date));
  return  date("Y-m-d", $date);
}

function validateDate($date){
	if($date != "" && $date != "-" && $date != "0000-00-00") {
	  $d = DateTime::createFromFormat('Y-m-d', $date);
	  return $d && $d->format('Y-m-d') == $date;
	}
	else {
		return false;
	}
}

function generateRandomColor() {
	return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

//-----
function verifyStoredLogin($loginToken) {
	global $gPDO;
	
	$stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_LOGIN_TOKEN = ? AND U_STATUS = 'USER_ACTIVE'");
	$stmt->execute([$loginToken]);
	$data = $stmt->fetch(PDO::FETCH_OBJ);

	if($data) {
		return $data;
	}
	else {
		return false;
	}
}

function getCenterFromDegrees($data) {
	/**
	 * Get a center latitude,longitude from an array of like geopoints
	 *
	 * @param array data 2 dimensional array of latitudes and longitudes
	 * For Example:
	 * $data = array
	 * (
	 *   0 = > array(45.849382, 76.322333),
	 *   1 = > array(45.843543, 75.324143),
	 *   2 = > array(45.765744, 76.543223),
	 *   3 = > array(45.784234, 74.542335)
	 * );
	*/
	
	if (!is_array($data)) return FALSE;

  $num_coords = count($data);

  $X = 0.0;
  $Y = 0.0;
  $Z = 0.0;

	foreach ($data as $coord) {
    $lat = $coord[0] * pi() / 180;
    $lon = $coord[1] * pi() / 180;

    $a = cos($lat) * cos($lon);
    $b = cos($lat) * sin($lon);
    $c = sin($lat);

    $X += $a;
    $Y += $b;
    $Z += $c;
	}

  $X /= $num_coords;
  $Y /= $num_coords;
  $Z /= $num_coords;

  $lon = atan2($Y, $X);
  $hyp = sqrt($X * $X + $Y * $Y);
  $lat = atan2($Z, $hyp);

	return array($lat * 180 / pi(), $lon * 180 / pi());
}

function parseToXML($htmlStr) { 
  $xmlStr = str_replace('<','&lt;',$htmlStr); 
  $xmlStr = str_replace('>','&gt;',$xmlStr); 
  $xmlStr = str_replace('"','&quot;',$xmlStr); 
  $xmlStr = str_replace("'",'&#39;',$xmlStr); 
  $xmlStr = str_replace("&",'&amp;',$xmlStr); 
  return $xmlStr; 
} 

// function to geocode address, it will return false if unable to geocode address
function geocode($address){ 
  // url encode the address
  $address = urlencode($address);
   
 

  // get the json response
  $resp_json = file_get_contents($url);   
  if(isset($resp_json) && trim($resp_json) != "") {
	  // decode the json
	  $resp = json_decode($resp_json, true);

	  // response status will be 'OK', if able to geocode given address 
	  if($resp['status']=='OK'){
	    // get the important data
	    $lati = $resp['results'][0]['geometry']['location']['lat'];
	    $longi = $resp['results'][0]['geometry']['location']['lng'];
	    $formatted_address = $resp['results'][0]['formatted_address'];
	     
	    // verify if data is complete
	    if($lati && $longi && $formatted_address){     
	      // put the data in the array
	      $data_arr = array();                     
	      array_push(
	        $data_arr, 
	        $lati, 
	        $longi, 
	        $formatted_address
	      );
	         
				return $data_arr;         
	    }
	    else {
	    	return false;
	    }       
	  }
	  else {
	  	return false;
	  }
	}
	else {
		return false;
	}
}

function distance($lat1, $lon1, $lat2, $lon2, $unit = "K") {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } 
  else if ($unit == "N") {
    return ($miles * 0.8684);
  } 
  else {
  	return $miles;
  }
}

function getAddress($lat,$lng) {
	$fullAddress = $lat.",".$lng;
  if(isset($address)) {
    $jsonData = @json_decode($address);
    $fullAddress = $jsonData->results[0]->formatted_address;
    $fullAddress = str_replace("Unnamed Road, ", "", $fullAddress);
  }

  return $fullAddress;
}

function submitFirebase($arr,$fbUrl) {
	$curlData = json_encode($arr);
  $ch = curl_init($fbUrl);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $curlData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  'Content-Type: application/json',
	  'Content-Length: ' . strlen($curlData))
  );
  $curlResult = curl_exec($ch);

  return $curlResult;
}

function sendAndroidPushNotificationToUser($registrationId, $notificationTitle, $notificationBody, $payloadTitle, $payloadMessage) {
	global $gFirebaseAPIKey;

	if(trim($registrationId) == ""  || trim($registrationId) == "-")	return "Invalid device ID";

	$data = array(
  	"to" => $registrationId,
  	"notification" => array( 
    	"title" => $notificationTitle, 
    	"body" => $notificationBody
  	),
  	"data" => array(
    	"title" => $payloadTitle,
    	"message" => $payloadMessage,
    	"is_background" => true,
    	"timestamp" => date('Y-m-d G:i:s')
  	),
  	"priority" => "high"
	);
	$data_string = json_encode($data); 

	$headers = array(
		'Authorization: key='.$gFirebaseAPIKey, 
		'Content-Type: application/json'
	);                                                                                 
                                                                                                                             
  $ch = curl_init();  

	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );                                                                  
	curl_setopt( $ch,CURLOPT_POST, true );  
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);                                                                  
                                                                                                                       
	$result = curl_exec($ch);

	curl_close ($ch);

	return $result;
}

function sendPushNotification($registration_ids, $message) {
	global $gFirebaseAPIKey;

	//firebase server url to send the curl request
	$url = 'https://fcm.googleapis.com/fcm/send';
	$fbApiKey = $gFirebaseAPIKey;

	$fields = array(
		'registration_ids' => $registration_ids,
		'data' => $message,
	);

	//building headers for the request
	$headers = array(
  	'Authorization: key=' . $fbApiKey,
  	'Content-Type: application/json'
	);

	//Initializing curl to open a connection
	$ch = curl_init();

	//Setting the curl url
	curl_setopt($ch, CURLOPT_URL, $url);
	  
	//setting the method as post
	curl_setopt($ch, CURLOPT_POST, true);

	//adding headers 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//disabling ssl support
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	  
	//adding the fields in json format 
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

	//finally executing the curl request 
	$result = curl_exec($ch);
	if ($result === FALSE) {
		//die('Curl failed: ' . curl_error($ch));
		return $result;
	}

	//Now close the connection
	curl_close($ch);

	//and return the result 
	return $result;
}

function sendSMS($to,$msg) {
	$userKey = getSetting("SMS_ACCT_USER"); 
	$passKey = getSetting("SMS_ACCT_PASSWORD"); 
	$url = getSetting("SMS_WEB_SERVICE");
	$results = "-";

	if($userKey != "" && $userKey != "-" && $passKey != "" && $passKey != "-" && $url != "" && $url != "-" && $to != "" && $msg != "") {
    /*--- zenziva */
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey='.$userKey.'&passkey='.$passKey.'&nohp='.$to.'&pesan='.urlencode($msg));
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    $results = curl_exec($curlHandle);
    curl_close($curlHandle);
	}

	return $results;
}

function sendSMSOTP($tlp, $otp) {
	/*
	Contoh respon:
			<response>
        <message>
               <to>081234567890</to>
               <status>0</status>
               <text>Success</text>
               <balance>9999</balance>
        </message>
			</response>
		Status Code
		status
		Deskiripsi
		0
		Success
		SMS telah berhasil disubmit ke server.
		1
		Nomor tujuan tidak valid.
		5
		Userkey / Passkey salah.
		6
		Konten SMS rejected.
		89
		Pengiriman SMS berulang-ulang ke satu nomor
		dalam satu waktu.
		99
		Credit tidak mencukupi.
	*/
	//$userKey = "ycrx3z"; 
	//$passKey = "ceabd0a34bde5902f2c8a2da"; 
	$userKey = getSetting("SMS_ACCT_USER"); 
  $passKey = getSetting("SMS_ACCT_PASSWORD"); 
	$url = "https://reguler.zenziva.net/apps/smsotp.php";
	$status = "-";
	
	if($userKey != "" && $userKey != "-" && $passKey != "" && $passKey != "-" && $url != "" && $url != "-" && $tlp != "" && $otp != "") {
		$curlHandle = curl_init();
		curl_setopt($curlHandle, CURLOPT_URL, $url);
		curl_setopt($curlHandle, CURLOPT_POSTFIELDS, 'userkey='.$userKey.'&passkey='.$passKey.'&nohp='.$tlp.'&kode_otp='.$otp);
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
		curl_setopt($curlHandle, CURLOPT_POST, 1);
		$results = curl_exec($curlHandle);
		curl_close($curlHandle);
		$XMLdata = new SimpleXMLElement($results);
		$status = $XMLdata->message[0]->text;
	}
	
	return $status;
}

function validasiPonsel($ponsel) {
  $pattern = "^[1-9][0-9]{0, 7}";
  if (ereg($pattern, $ponsel)) {
    return true;
  }
  else {
    return false;
  }
}

function formatPonsel($ponsel,$prefix) {
  if(trim($ponsel) != "" && trim($ponsel) != "-") {
    if(substr($ponsel,0,5) == "+6262" || substr($ponsel,0,4) == "+620" || substr($ponsel,0,4) == "6262" || substr($ponsel,0,3) == "620") {
      //+626281xxxx
      if(substr($ponsel,0,5) == "+6262")  {
        if($prefix == "+62") { 
          $ponsel = "+62".substr($ponsel,5);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,5);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,5);  
        }   
      }

      //+62081xxxx    
      if(substr($ponsel,0,4) == "+620") {
        if($prefix == "+62") {
          $ponsel = "+62".substr($ponsel,4);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,4);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,4);  
        }
      }

      //626281xxxx
      if(substr($ponsel,0,4) == "6262") {
        if($prefix == "+62") { 
          $ponsel = "+62".substr($ponsel,4);  
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,4);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,4);  
        }   
      }

      //62081xxxx   
      if(substr($ponsel,0,3) == "620")  {
        if($prefix == "+62") { //no change
          $ponsel = "+62".substr($ponsel,3);  
        }
        if($prefix === "0") {
          $ponsel = "0".substr($ponsel,3);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,3);  
        }   
      }
    }
    else {
      //+6281xxxxx
      if(substr($ponsel,0,3) == "+62")  {
        if($prefix == "+62") { //no change

        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,3);  
        }   
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,3);  
        }   
      }

      //628xxxxx
      if(substr($ponsel,0,2) == "62") {
        if($prefix == "+62") {
          $ponsel = "+".$ponsel;
        }
        if($prefix == "0") {
          $ponsel = "0".substr($ponsel,2);
        }
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,2);  
        }   
      }

      //8132333
      if(substr($ponsel,0,1) == "8")  {
        if($prefix == "+62") {
          $ponsel = "+62".$ponsel;
        }
        if($prefix == "0") {
          $ponsel = "0".$ponsel;
        }   
        if(trim($prefix) == "") { //no change
          
        }   
      }

      //081xxxxx
      if(substr($ponsel,0,2) == "08") {
        if($prefix == "+62") {
          $ponsel = "+62".substr($ponsel,1);
        }
        if($prefix == "0") { //no change
          
        }
        if(trim($prefix) == "") {
          $ponsel = substr($ponsel,1);  
        }   
      }
    }
  }

  return $ponsel;
}

function uploadFile($path, $newName, $httpFileVariable){		
	$folderPath = $path;	
	if ($_FILES[$httpFileVariable]["error"] == 0){
		if (file_exists($folderPath . $newName))	{
			unlink($folderPath . $newName);
			//return false;
		}
		
		try{
			$move = move_uploaded_file($_FILES[$httpFileVariable]["tmp_name"],	$folderPath . $newName);
		}
		catch (Exception $e) {
			echo composeReply('File did not upload : ' . $e->getMessage());
			exit;
			return false;
		}
		
		return true;
	}
}

// function getSetting($setId) {
// 	global $gPDO;
	
// 	$stmt = $gPDO->prepare("SELECT * FROM _settings WHERE SET_ID = ?");
// 	$stmt->execute([$setId]);
// 	$setting = $stmt->fetch(PDO::FETCH_OBJ);
// 	if($setting) {
// 		return $setting->{"SET_VALUE"};
// 	}
// 	else {
// 		return "";
// 	}
// }

function getSettingInfo($setId) {
	global $gPDO;
	
	$stmt = $gPDO->prepare("SELECT * FROM _settings WHERE SET_ID = ?");
	$stmt->execute([$setId]);
	$setting = $stmt->fetch(PDO::FETCH_OBJ);
	if($setting) {
		return $setting->{"SET_INFO"};
	}
	else {
		return "";
	}
}

function getReferenceInfo($category, $value) {
	global $gPDO;
	
	$stmt = $gPDO->prepare("SELECT * FROM _references WHERE R_CATEGORY = ? AND R_ID = ?");
	$stmt->execute([$category, $value]);
	$ref = $stmt->fetch(PDO::FETCH_OBJ);
	if($ref) {
		return $ref->{"R_INFO"};
	}
	else {
		return "";
	}
}

function checkLogin($id, $password) {
	global $gPDO;
	
	$stmt = $gPDO->prepare("SELECT * FROM _users WHERE U_ID = ? AND U_PASSWORD_HASH = ? AND U_STATUS = 'USER_ACTIVE'");
	$stmt->execute([$id, md5($id.$password)]);
	$login = $stmt->fetch(PDO::FETCH_OBJ);
	if($login) {
		return $login;
	}
	else {
		return false;
	}	
}

function calculateUnitPrice($unitId) {
	global $gPDO;

	$stmt = $gPDO->prepare("SELECT * FROM mp_proyek_unit WHERE UNIT_ID = ?");
	$stmt->execute([$unitId]);
	$unitData = $stmt->fetch(PDO::FETCH_OBJ);

	if(!$unitData)	return 0;

	$hargaTanahLebih = floatval($unitData->{"UNIT_TNH_LBH_LUAS_M2"}) * floatval($unitData->{"UNIT_TNH_LBH_HARGA_M2"});
    $bookHargaTotal = floatval($unitData->{"UNIT_HARGA_LUMPSUM"}) + $hargaTanahLebih - floatval($unitData->{"UNIT_DISKON_LUMPSUM"});      

    return $bookHargaTotal;          
}

function sendNotification($to, $msg, $from, $tipe) {
	global $gPDO;
	global $gClientProjectId;

	$gPDO->prepare("INSERT INTO mp_notifikasi (PRO_ID, N_RCPT_U_ID, N_SENDER_U_ID, N_INFO, N_FLAG_BARU, N_TIPE, SYS_CREATE_TIME, SYS_CREATE_U_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")->execute([$gClientProjectId, $to, $from, $msg, "Y", $tipe, date("Y-m-d H:i:s"), $from]);
	$nId = $gPDO->lastInsertId();
	if(isset($nId) && $nId > 0) {
		//return true;
		return $nId;
	}
	else {
		return false;
	}
}

function addLog($info) {
	global $gPDO;

	if(trim($info) == "")	return;
	
	$gPDO->prepare("INSERT INTO mp_logs (LOG_TGL, LOG_INFO) VALUES (?,?)")->execute([date("Y-m-d H:i:s"), $info]);
	$logId = $gPDO->lastInsertId();

	return $logId;
}

// define("DOMAIN", "dcpos.digitalcode.co.id");
// define("MAILGUN_API", "8a23ca45ae36b6741a3130078f093b2b-f45b080f-0c0cb7ca"); // Mailgun Private API Key

function br2nl($string) {
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

function mailgunSend($to, $subject, $message) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, 'api:'.MAILGUN_API);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  	$plain = strip_tags(br2nl($message));

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v2/'.DOMAIN.'/messages');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array(
		'from' => 'noreply@'.DOMAIN,
		'to' => $to,
		'subject' => $subject,
		'html' => $message,
		'text' => $plain));

  	$j = json_decode(curl_exec($ch));

  	$info = curl_getinfo($ch);

  	if($info['http_code'] != 200)	return false;

	curl_close($ch);

  	return $j;
}

require "../vendor/autoload.php";
use Mailgun\Mailgun;

function mailgunSendByAPI($mailName, $mailTo, $mailSubject, $htmlContent, $attachment = null) {
	# Instantiate the client.
    $mgClient = new Mailgun(MAILGUN_API);
    $mgClient->setSslEnabled(false);

    $arrParameters = array(
        'from'      => 'GMA5 <noreply@gma5.com>',
        'to'        => $mailName.' <'.$mailTo.'>',
        'subject'   => $mailSubject,
        'text'      => strip_tags($htmlContent),
        'html'      => $htmlContent
    );

    # Make the call to the client.
    //$result = $mgClient->sendMessage(DOMAIN, $arrParameters, array('attachment' => '../downloads/laporan-booking-penjualan.xlsx'));
    if(isset($attachment)) {
    	$result = $mgClient->sendMessage(DOMAIN, $arrParameters, array('attachment' => $attachment));	
    }
    else {
    	$result = $mgClient->sendMessage(DOMAIN, $arrParameters);
    }
    /*
	    - http_response_body	
			-- id	"<20200119054126.1.8092BF3FDDAA662A@dcpos.digitalcode.co.id>"
			-- message	"Queued. Thank you."
		- http_response_code	200
	*/

    return $result;
}
?>