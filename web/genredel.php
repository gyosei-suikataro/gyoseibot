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
$gid1 = $_POST['gid1'];
$gid2 = $_POST['gid2'];

//error_log("★★★★★★★★★★★ gid1:".$gid1." gid2:".$gid2);

if ($link) {
	//大分類の場合は小分類も削除
	if($gid2 == 0){
		$result = pg_query("DELETE FROM genre WHERE gid1 = {$gid1}");
	}else{
		//名称の取得
		$result = pg_query("SELECT meisho FROM genre WHERE bunrui = 1 AND gid1 = {$gid1}");
		$row = pg_fetch_row($result);
		$g1meisho = $row[0];

		$result = pg_query("SELECT meisho FROM genre WHERE gid1 = {$gid1} AND gid2 = {$gid2}");
		$row = pg_fetch_row($result);
		$g2meisho = $row[0];

		//削除
		$result = pg_query("DELETE FROM genre WHERE gid1 = {$gid1} AND gid2 = {$gid2}");

		//CVS削除
		error_log("★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★");
		error_log("gid1:".$g1meisho." gid2:".$g2meisho);
		$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/entities/".urlencode($g1meisho)."/values/".urlencode($g2meisho)."?version=2017-05-26";
		callWatson();

		$url = "https://gateway.watsonplatform.net/conversation/api/v1/workspaces/".$workspace_id_shi."/dialog_nodes/".$gid1.".".$gid2."?version=2017-05-26";
		callWatson();
	}

}

function callWatson(){
	global $curl, $url, $username, $password, $data, $options;
	$curl = curl_init($url);

	$options = array(
			CURLOPT_USERPWD => $username . ':' . $password,
			CURLOPT_CUSTOMREQUEST => 'DELETE',
			CURLOPT_RETURNTRANSFER => true,
	);

	curl_setopt_array($curl, $options);
	return curl_exec($curl);
}

?>

