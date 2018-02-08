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
$para = $_POST['para'];
$info = $_POST['info'];
$agek = $_POST['agek'];
$agem = $_POST['agem'];
$sex = $_POST['sex'];
$region= $_POST['region'];

error_log("★★★★★★★★★★★★★★★★★★★★★★★★★★★★");
error_log("para:".$para." info:".$info." agek:".$agek." agem:".$agem." sex:".$sex." region:".$region);

$query = "SELECT COUNT(*) FROM userinfo";
$queryWhere = "";

if ($link) {
	if($para == "search"){
		switch ($info) {
			//全て
			case 0:
				$result = pg_query("SELECT COUNT(*) FROM userinfo");
				break;
			//属性登録あり
			case 1:
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
				$result = pg_query("SELECT COUNT(*) FROM userinfo WHERE sex = '0' AND age = 999 AND region = '000'");
				break;
		}
		if(!$result){
			echo json_encode("NG");
		}else{
			error_log("★★★★★★★★★★★★★★★★★★".$result);
			echo json_encode($result);
		}
	}

}else{
	echo json_encode("NG");
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

