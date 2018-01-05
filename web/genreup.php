<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$workspace_id_shi = getenv('CVS_WORKSPASE_ID_SHI');
$username = getenv('CVS_USERNAME');
$password = getenv('CVS_PASS');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//引数
$uiKbn= $_POST['uiKbn'];
$bunrui= $_POST['bunrui'];
$meisho= $_POST['meisho'];
$gid1= $_POST['gid1'];
$gid2= $_POST['gid2'];
$g1meisho= $_POST['g1meisho'];

error_log("uiKbn:".$uiKbn." bunrui:".$bunrui." meisho:".$meisho." gid1:".$gid1." gid2:".$gid2." g1meisho:".$g1meisho);

if ($link) {
	if($uiKbn == 1){
		$sql = "UPDATE genre SET meisho = '{$meisho}' WHERE gid1 = {$gid1} AND gid2 = {$gid2}";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
	}else{
		if($bunrui == 1){
			$result= pg_query("SELECT gid1 FROM genre ORDER BY gid1 DESC");
			$row = pg_fetch_row($result);
			$gid1 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, 0, 0, '{$meisho}')";
			$result_flag = pg_query($sql);
		}else{
			$result= pg_query("SELECT gid2 FROM genre WHERE gid1 = {$gid1} ORDER BY gid2 DESC");
			$row = pg_fetch_row($result);
			$gid2 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, {$gid2}, 0, '{$meisho}')";
			$result_flag = pg_query($sql);

			//CVSデータ作成
			//ENTITIES
			error_log("★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★");
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".urlencode($g1meisho)."/values?version=2017-05-26";
			$data = array("value" => $meisho);
			callWatson();

			//DIALOG
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".urlencode($g1meisho)."?version=2017-05-26";
			$data = array("newConditions" => "@".$g1meisho.":".$meisho,"newOutput" => array("text" => $gid1.".".$gid2));
			callWatson();
		}
		if (!$result_flag) {
			error_log("インサートに失敗しました。".pg_last_error());
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

