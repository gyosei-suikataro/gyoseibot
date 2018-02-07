<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//引数
$no = $_POST['no'];


if ($link) {
	$delresult = true;
	foreach($no as $delno){
		$result = pg_query("DELETE FROM botlog WHERE NO = ".$delno);
		if (!$result) {
			error_log("削除に失敗しました。".pg_last_error());
			$delresult = false;
			break;
		}
	}
	if($delresult){
		echo json_encode("OK");
	}else{
		echo json_encode("NG");
	}
}else{
	echo json_encode("NG");
}

?>

