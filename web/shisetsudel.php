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
$id = $_POST['id'];

/*
if ($link) {
	$result = pg_query("DELETE FROM shisetsu WHERE ID = ".$delid);
	if (!$result) {
		error_log("削除に失敗しました。".pg_last_error());
		echo json_encode("NG");
	}else{
		echo json_encode("OK");
	}
}else{
	echo json_encode("NG");
}
*/

if ($link) {
	$delresult = true;
	foreach($id as $delid){
		$result = pg_query("DELETE FROM shisetsu WHERE ID = ".$delid);
		if (!$result) {
			error_log("削除に失敗しました。".pg_last_error());
			$delresult = false;
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

