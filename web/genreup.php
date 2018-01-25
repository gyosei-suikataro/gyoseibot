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
	//$formatmeisho = preg_replace("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u",'' ,$meisho);
	//$formatmeishoOld = preg_replace("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u",'' ,$meishoOld);
	if($uiKbn == 1){
		$sql = "UPDATE genre SET meisho = '{$meisho}' WHERE gid1 = {$gid1} AND gid2 = {$gid2}";
		$result_flag = pg_query($sql);
		if (!$result_flag) {
			error_log("アップデートに失敗しました。".pg_last_error());
		}
		if($gid2 == 0){
			$result = pg_query("SELECT gid2,meisho FROM genre WHERE gid1 = {$gid1}");
			//大分類
			//CVSデータ修正
			//Intents
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents/".$gid1."?version=2017-05-26";
			$data = array("description" => $meisho);
			callWatson();

			/*
			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".urlencode($formatmeishoOld)."?version=2017-05-26";
			$data = array("entity" => $formatmeisho);
			callWatson();

			//dialog_node
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".$gid1.".".$gid2."?version=2017-05-26";
			$data = array("title" => $formatmeisho,"conditions" => "@".$formatmeisho);
			callWatson();

			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/"."node_".$gid1."?version=2017-05-26";
			$data = array("title" => "#".$formatmeisho,"conditions" => "#".$formatmeisho);
			callWatson();

			while ($row = pg_fetch_row($result)) {
				if($row[0] != "0"){
					$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".$gid1.".".$row[0]."?version=2017-05-26";
					$data = array("conditions" => "@".$formatmeisho.":".$row[1]);
					callWatson();
				}
			}
			*/
		}else{
			//小分類
			//CVSデータ修正
			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".$gid1."/values/".urlencode($meishoOld)."?version=2017-05-26";
			$data = array("value" => $meisho);
			callWatson();

			//DIALOG
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".$gid1.".".$gid2."?version=2017-05-26";
			$data = array("conditions" => "@".$gid1.":".$meisho);
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
			//Intents
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/intents?version=2017-05-26";
			$data = array("intent" => (string)$gid1,"description" => $meisho);
			callWatson();

			//ENTITIES
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities?version=2017-05-26";
			$data = array("entity" => (string)$gid1);
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
			$data = array("dialog_node" => $gid1.".".$gid2,"title" => "entity".$gid1,"conditions" => "@".$gid1,"previous_sibling" => "ようこそ","metadata" => array("_customization" => array("mcr" => true)));
			callWatson();

			if($previous_sibling == ""){
				$previous_sibling = $gid1.".".$gid2;
			}
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$data = array("dialog_node" => "node_".$gid1,"title" => "intent".$gid1,"conditions" => "#".$gid1,"previous_sibling" => $previous_sibling,"output" => array("text" => array("values" => array($gid1.".".$gid2))));
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
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".$gid1."/values?version=2017-05-26";
			$data = array("value" => $meisho);
			callWatson();

			//上記で取得したdialog_nodeをparentに設定して新規ノードを作成
			$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/?version=2017-05-26";
			$data = array("dialog_node" => $gid1.".".$gid2,"type" => "response_condition","parent" =>  "entity".$gid1,"conditions" => "@".$gid1.":".$meisho,"output" => array("text" => array("values" => array($gid1.".".$gid2))));
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

