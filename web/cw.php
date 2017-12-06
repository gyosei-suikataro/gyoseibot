<?php

date_default_timezone_set('Asia/Tokyo');
$tdate = date("YmdHis");

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$workspace_id = getenv('CVS_WORKSPASE_ID');
$workspace_id_ken = getenv('CVS_WORKSPASE_ID_KEN');
$username = getenv('CVS_USERNAME');
$password = getenv('CVS_PASS');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

$resmess = "";
$user = filter_input(INPUT_GET, 'user');
$param = filter_input(INPUT_GET, 'param');
$text = filter_input(INPUT_GET, 'text');
$kbn = filter_input(INPUT_GET, 'kbn');
$wid = "";
if($param == "1"){
	$wid = $workspace_id_ken;
}else if($param == "2"){
	$wid = $workspace_id;
}else{
	echo json_encode(['text' => 'エラー']);
	exit;
}

$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$wid."/message?version=2017-04-21";

error_log("★★★★★★★★★★★★★★★★★★user:".$user." param=".$param." text=".$text." kbn=".$kbn);

$text= str_replace("\n","",$text);
$data = array('input' => array("text" => $text));

if($kbn == "0"){
	//初回
	init();
}else{
	//2回め以降
	main();
}
error_log("★★★★★★★★★★★★★★★★★★text:".$resmess);
echo json_encode(['text' => $resmess]);

function init(){
	$jsonString = callWatson();
	$json = json_decode($jsonString, true);
	$conversation_id = $json["context"]["conversation_id"];
	$resmess= $json["output"]["text"][0];
	//改行コードを置き換え
	$resmess = str_replace("\\n","\n",$resmess);
	$conversation_node = $json["context"]["system"]["dialog_stack"][0]["dialog_node"];
	error_log("resmess=".$resmess);
	error_log("conversation_id=".$conversation_id);
	error_log("conversation_node=".$conversation_node);
	if ($link) {
		$result = pg_query("SELECT * FROM cvsdata WHERE userid = '{$user}'");
		if (pg_num_rows($result) == 0) {
			error_log("データなし");
			$sql = "INSERT INTO cvsdata (userid, conversationid, dnode, time) VALUES ('{$user}','{$conversation_id}','{$conversation_node}','{$tdate}')";
			$result_flag = pg_query($sql);
		}else{
			error_log("データあり");
			$sql = "UPDATE cvsdata SET conversationid = '{$conversation_id}', dnode = '{$conversation_node}', time = '{$tdate}' WHERE userid = '{$user}'";
			$result_flag = pg_query($sql);
		}
	}
}

function main(){
	if ($link) {
		$result = pg_query("SELECT * FROM cvsdata WHERE userid = '{$user}'");
		$row = pg_fetch_row($result);
		$conversation_id = $row[1];
		$conversation_node= $row[2];
		$conversation_time= $row[3];
	}

	$data["context"] = array("conversation_id" => $conversation_id,
			"system" => array("dialog_stack" => array(array("dialog_node" => $conversation_node)),
					"dialog_turn_counter" => 1,
					"dialog_request_counter" => 1));
	$jsonString = callWatson();
	$json = json_decode($jsonString, true);
	$resmess= $json["output"]["text"][0];
	$conversation_node = $json["context"]["system"]["dialog_stack"][0]["dialog_node"];

	if ($link) {
		$sql = "UPDATE cvsdata SET conversationid = '{$conversation_id}', dnode = '{$conversation_node}', time = '{$tdate}' WHERE userid = '{$user}'";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
	}
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
?>

