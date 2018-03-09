<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');
$accessToken = getenv('LINE_CHANNEL_ACCESS_TOKEN3');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//引数
$para = $_POST['para'];
$info = $_POST['info'];
$agek = $_POST['agek'];
$agem = $_POST['agem'];
$sex = $_POST['sex'];
$region= $_POST['region'];
$sendmess= $_POST['sendmess'];

error_log("★★★★★★★★★★★★★★★★★★★★★★★★★★★★");
error_log("para:".$para." info:".$info." agek:".$agek." agem:".$agem." sex:".$sex." region:".$region." sendmess:".$sendmess);

$query = "";
$queryWhere = "";
$result = null;

if ($link) {
	if($para == "search"){
		$query = "SELECT COUNT(*) AS rows FROM userinfo";
		readDB();
		if(!$result){
			echo json_encode("NG");
		}else{
			$row = pg_fetch_assoc($result);
			echo json_encode($row["rows"]);
		}
	}
	if($para == "send"){
		//jsonを作成し、LINEに送信
		$query = "SELECT userid FROM userinfo";
		readDB();
		if(!$result){
			echo json_encode("NG");
		}else{
			$uids = [];
			while ($row = pg_fetch_row($result)) {
				//$uids = $uids."\"".trim($row[0])."\",";
				array_push($uids,trim($row[0]));
			}
			error_log("★★★★★★★★★★★★★★★★★★".$uids);
			$response_format_text = [
					"to" => $uids,
					"messages" => [
							[
								"type" => "text",
								"text" => $sendmess
							]
					]
			];
			$ch = curl_init("https://api.line.me/v2/bot/message/multicast");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response_format_text));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json; charser=UTF-8',
					'Authorization: Bearer ' . $accessToken
			));
			$result = curl_exec($ch);
			if(!curl_errno($ch)) {
				$info = curl_getinfo($ch);
				error_log("★★★★★★★★★★★★★★★★★★".$info['http_code']);
				if($info['http_code'] == "200"){
					echo json_encode("OK");
				}else{
					echo json_encode("NG");
				}
			}else{
				echo json_encode("NG");
			}

			curl_close($ch);
		}
	}

}else{
	echo json_encode("NG");
}

function readDB(){
	global $info, $query, $agek, $agem, $sex, $region, $queryWhere, $result;
	switch ($info) {
		//全て
		case 0:
			$result = pg_query($query);
			break;
		//属性登録あり
		case 1:
			queryadd("updkbn = '0'");
			if($agek != 999){
				queryadd("age >= ".$agek);
				if($agem != 999){
					queryadd("age <= ".$agem);
				}
			}
			if($sex > 0){
				queryadd("sex = '".$sex."'");
			}
			if($region != "000"){
				queryadd("region = '".$region."'");
			}
			if($queryWhere != ""){
				$query = $query." WHERE ".$queryWhere;
			}
			error_log("★★★★★★★★★★★★★★★★★★".$query);
			$result = pg_query($query);
			break;
		//属性登録なし
		case 2:
			$result = pg_query($query." WHERE updkbn = '0'");
			break;
	}
}

function queryadd($st){
	global $queryWhere;
	if($queryWhere != ""){
		$queryWhere = $queryWhere." AND ".$st;
	}else{
		$queryWhere = $st;
	}
}


?>

