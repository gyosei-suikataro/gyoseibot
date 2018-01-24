<?php

date_default_timezone_set('Asia/Tokyo');
$tdate = date("YmdHis");

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$workspace_id = getenv('CVS_WORKSPASE_ID');
$workspace_id_shi = getenv('CVS_WORKSPASE_ID_SHI');
$username = getenv('CVS_USERNAME');
$password = getenv('CVS_PASS');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$param = $_POST['param'];
$g1meisho= $_POST['g1meisho'];

$formatmeisho = preg_replace("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u",'' ,$g1meisho);
error_log("★★★★★★★★★★★★★★★★★★formatmeisho:".$formatmeisho);

switch($param) {
	case 'search':
		search();
	case 'update':
		update();
	default:
		continue;
}

function search(){
	global $url;
	$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents/".urlencode($formatmeisho)."/examples?version=2017-05-26&export=true";
	$jsonString = callWatson2();
	$json = json_decode($jsonString, true);
	$arr = array();
	foreach ($json["examples"] as $value){
		//error_log("text:".$value["text"]);
		array_push($arr,$value["text"]);
	}
	echo json_encode($arr);
}

function callWatson(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}

function callWatson2(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
			),
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}
?>

