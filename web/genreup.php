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
$meishoOld= $_POST['meishoOld'];

error_log("★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★");
error_log("uiKbn:".$uiKbn." bunrui:".$bunrui." meisho:".$meisho." gid1:".$gid1." gid2:".$gid2." g1meisho:".$g1meisho." meishoOld:".$meishoOld);

if ($link) {
	if($uiKbn == 1){
		$sql = "UPDATE genre SET meisho = '{$meisho}' WHERE gid1 = {$gid1} AND gid2 = {$gid2}";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
		if($gid2 == 0){
			//大分類
		}else{
			//小分類
			//CVSデータ修正
			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".urlencode($g1meisho)."/values/".urlencode($meishoOld)."?version=2017-05-26";
			$data = array("value" => $meisho);
			callWatson();

			//DIALOG
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".$gid1.".".$gid2."?version=2017-05-26";
			$data = array("conditions" => "@".$g1meisho.":".$meisho);
			callWatson();
		}
	}else{
		if($bunrui == 1){
			//大分類
			$result= pg_query("SELECT gid1 FROM genre ORDER BY gid1 DESC");
			$row = pg_fetch_row($result);
			$gid1 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, 0, 0, '{$meisho}')";
			$result_flag = pg_query($sql);

			//CVSデータ作成
			$formatmeisho = preg_replace("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u",'' ,$meisho);
			//Intents
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents?version=2017-05-26";
			$data = array("intent" => $formatmeisho);
			callWatson();

			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities?version=2017-05-26";
			$data = array("entity" => $formatmeisho);
			callWatson();

			//dialog_node
			$previous_sibling = "";
			$bgid1 = $gid1 - 1;
			$nodevalue = $bgid1.".0";
			//全てのLISTから１つ前のダイアログを探す
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$jsonString = callWatson2();
			$json = json_decode($jsonString, true);
			foreach ($json["dialog_nodes"] as $value){
				error_log("values:".$value["output"]["text"]["values"][0]);
				if($value["output"]["text"]["values"][0] == $nodevalue){
					$previous_sibling = $value["dialog_node"];
					error_log($previous_sibling);
					break;
				}
			}
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$data = array("dialog_node" => $gid1,"title" => "#".$formatmeisho,"conditions" => "#".$formatmeisho,"previous_sibling" => $previous_sibling,"output" => array("text" => array("values" => array($gid1.".".$gid2))));
			callWatson();

			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$data = array("dialog_node" => $gid1.".".$gid2,"title" => $formatmeisho,"conditions" => "@".$formatmeisho);
			callWatson();

		}else{
			//小分類
			$result= pg_query("SELECT gid2 FROM genre WHERE gid1 = {$gid1} ORDER BY gid2 DESC");
			$row = pg_fetch_row($result);
			$gid2 = $row[0] + 1;
			$sql = "INSERT INTO genre (bunrui, gid1, gid2, gid3, meisho) VALUES ({$bunrui}, {$gid1}, {$gid2}, 0, '{$meisho}')";
			$result_flag = pg_query($sql);

			//CVSデータ作成
			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".urlencode($g1meisho)."/values?version=2017-05-26";
			$data = array("value" => $meisho);
			callWatson();

			//DIALOG
			$parent = "";
			//全てのLISTから大分類名とタイトルが同じノードのdialog_nodeを取得
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$jsonString = callWatson2();
			$json = json_decode($jsonString, true);
			foreach ($json["dialog_nodes"] as $value){
				if($value["title"] == $g1meisho){
					$parent = $value["dialog_node"];
					break;
				}
			}

			//上記で取得したdialog_nodeをparentに設定して新規ノードを作成
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$data = array("dialog_node" => $gid1.".".$gid2,"type" => "response_condition","parent" =>  $parent,"conditions" => "@".$g1meisho.":".$meisho,"output" => array("text" => array("values" => array($gid1.".".$gid2))));
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

