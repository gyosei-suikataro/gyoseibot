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
$user = $_POST['user'];

//error_log("user:".$user." age:".$age." sex:".$sex." region:".$region);

if ($link) {
	//$result = pg_query("DELETE FROM userinfo WHERE userid = '{$user}'");
	$result = pg_query("UPDATE userinfo SET  sex = '0', age = 999 , region = '000',updkbn = '0' WHERE userid = '{$user}'");
}


?>

