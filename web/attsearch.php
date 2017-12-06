<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

$request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
if($request !== 'xmlhttprequest') exit;

$user = filter_input(INPUT_GET, 'user');

error_log("★★★★★★★★★★★★★★★★★★".$user);

if ($link) {
	$result = pg_query("SELECT * FROM userinfo WHERE userid = '{$user}'");
	if (pg_num_rows($result) == 0) {
		echo json_encode(['lang' => '0', 'sex' => '0', 'age' => '999', 'region' => '', 'search' => '']);
	}else{
		$row = pg_fetch_row($result);
		echo json_encode(['lang' => $row[2], 'sex' => $row[3], 'age' => $row[4], 'region' => $row[5], 'search' => $row[7]]);
	}
}

echo json_encode(['text' => $text . ', World!',]);
?>

