<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>施設登録</title>
</head>
<body>
<p  style="display:inline;">　施設名称</p>
<input id="meisho" maxlength="40" placeholder="行政公園" style="width: 500px;">
<br><br>
<p style="display:inline;">　　　住所</p>
<input id="jusho" maxlength="128" placeholder="行政市行政1-1-1"  style="width: 500px;">
<br><br>
<p style="display:inline;">　電話番号</p>
<input id="tel" maxlength="14" placeholder="000-0000-0000"  style="width: 100px;">
<br><br>
<p style="display:inline;">ジャンル１</p>
<select id="j1"  onChange="j1change()">
<option value="" selected></option>
<option value="グルメ">グルメ</option>
<option value="レジャー・観光・スポーツ">レジャー・観光・スポーツ</option>
<option value="ホテル・旅館">ホテル・旅館</option>
<option value="駅・バス・車・交通">駅・バス・車・交通</option>
<option value="公共・病院・銀行・学校">公共・病院・銀行・学校</option>
<option value="ショッピング">ショッピング</option>
<option value="生活・不動産">生活・不動産</option>
</select>
<br><br>
<p style="display:inline;">ジャンル２</p>
<select id="j2">
</select>
<br><br>
<p style="display:inline;">　　　緯度</p>
<input id="lat" maxlength="14" placeholder="999.99999" style="width: 100px;">
<br><br>
<p style="display:inline;">　　　経度</p>
<input id="lng" maxlength="14" placeholder="999.99999" style="width: 100px;">
<br><br>
<p style="display:inline;">画像ＵＲＬ</p>
<input id="iurl" maxlength="300" placeholder="https://www.yyy.zzz.jpg" style="width: 500px;">
<br>
※必ずhttpsから始まるURLを指定してください
<br><br>
<p style="display:inline;">詳細ＵＲＬ</p>
<input id="url" maxlength="300" placeholder="http://www.yyy.zzz.html" style="width: 500px;">
<br><br>
<input type="button" onclick="clearform()" value="クリア" />
<input type="button" onclick="update()" value="更新" />
<input type="button" onclick="map()" value="地図の確認" />
<input type="button" onclick="back()" value="もどる" />

<?php

//引数
$id = $_GET['id'];

$meisho = "";
$jusho= "";
$tel= "";
$genre1= "";
$genre2= "";
$lat= 0;
$lng= 0;
$imageurl= "";
$url = "";

if(id){

	error_log("★★★★★★★★★★★★★★★id:".$id);

	//環境変数の取得
	$db_host =  getenv('DB_HOST');
	$db_name =  getenv('DB_NAME');
	$db_pass =  getenv('DB_PASS');
	$db_user =  getenv('DB_USER');

	//DB接続
	$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
	$link = pg_connect($conn);

	if ($link) {
		$result = pg_query("SELECT meisho, jusho, tel, genre1, genre2, lat, lng, imageurl, url FROM shisetsu WHERE id = '{$id}'");
		$row = pg_fetch_row($result);
		$meisho = $row[0];
		$jusho= $row[1];
		$tel= $row[2];
		$genre1= $row[3];
		$genre2= $row[4];
		$lat= $row[5];
		$lng= $row[6];
		$imageurl= $row[7];
		$url = $row[8];
	}
}


?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script>
var meisho = "";
var jusho = "";
var tel = "";
var j1 = "";
var j2 = "";
var lat = "";
var lng = "";
var iurl = "";
var url = "";

$(function(){
	meisho = <?php echo json_encode($meisho); ?>;
	jusho = <?php echo json_encode($jusho); ?>;
	tel = <?php echo json_encode($tel); ?>;
	j1 = <?php echo json_encode($genre1); ?>;
	j2 = <?php echo json_encode($genre2); ?>;
	lat = <?php echo json_encode($lat); ?>;
	lng = <?php echo json_encode($lng); ?>;
	iurl = <?php echo json_encode($iurl); ?>;
	url = <?php echo json_encode($url); ?>;

	document.getElementById('meisho').value = meisho;
	document.getElementById('jusho').value = jusho;
	document.getElementById('tel').value = tel;
	document.getElementById('j1').value = j1;
	document.getElementById('j2').value = j2;
	document.getElementById('lat').value = lat;
	document.getElementById('lng').value = lng;
	document.getElementById('iurl').value = iurl;
	document.getElementById('url').value = url;
});

//ジャンル選択
function j1change(){
	var select = document.getElementById('j2');
	while (0 < select.childNodes.length) {
		select.removeChild(select.childNodes[0]);
	}

	var janru = [];
	switch (document.getElementById('j1').value){
	  case "グルメ":
		  janru = ["和食","寿司","洋食","中華","多国籍"];
	    break;
	  case "レジャー・観光・スポーツ":
		  janru = ["レジャー施設","美術館","温泉","公園","緑地","庭園","神社","寺","協会","スポーツ","アウトドア","ゴルフ場","釣堀","動物園","遊園地","博物館"];
	    break;
	  case "ホテル・旅館":
		  janru = ["ホテル","旅館","民宿"];
	    break;
	  case "駅・バス・車・交通":
		  janru = ["駅","バス停","駐車場","ガソリンスタンド","レンタカー","ディーラー","空港","道の駅"];
	    break;
	  case "公共・病院・銀行・学校":
		  janru = ["役所","病院","幼稚園","保育園","小学校","中学校","高校","大学","専門学校","図書館","銀行"];
	    break;
	  case "ショッピング":
		  janru = ["コンビニ","薬局","スーパー","家電","ホームセンター","本屋","洋服"];
	    break;
	  case "生活・不動産":
		  janru = ["レンタルショップ","クリーニング","不動産"];
	    break;
	    break;
	  default:
	    break;
	}

	janru.forEach(function (item, index, array) {
		var option = document.createElement('option');
		option.setAttribute('value', item);
		var text = document.createTextNode(item);
		option.appendChild(text);
		select.appendChild(option);
	});
}

//クリア
function clearform(){
	document.getElementById('meisho').value = "";
	document.getElementById('jusho').value = "";
	document.getElementById('tel').value = "";
	document.getElementById('j1').selectedIndex = 0;
	document.getElementById('j2').selectedIndex = 0;
	document.getElementById('lat').value = "";
	document.getElementById('lng').value = "";
	document.getElementById('iurl').value = "";
	document.getElementById('url').value = "";
}

//更新
function update(){
	meisho = document.getElementById('meisho').value;
	jusho = document.getElementById('jusho').value;
	tel = document.getElementById('tel').value;
	j1 = document.getElementById('j1').value;
	j2 = document.getElementById('j2').value;
	lat = document.getElementById('lat').value;
	lng = document.getElementById('lng').value;
	iurl = document.getElementById('iurl').value;
	url = document.getElementById('url').value;
	$.ajax({
		type: "POST",
		url: "shisetsuup.php",
		data: {
			"meisho" : meisho,
			"jusho" : jusho,
			"tel" : tel,
			"j1" : j1,
			"j2" : j2,
			"lat" : lat,
			"lng" : lng,
			"iurl" : iurl,
			"url" : url
		}
	}).then(
		function(){
			alert("登録が完了しました。画面を閉じてください。");
		},
		function(){
			alert("登録できませんでした。");
		}
	);
}

//もどる
function back(){
	window.location.href = "./shisetsu.php";
}

//地図の確認
function map(){
	lat = document.getElementById('lat').value;
	lng = document.getElementById('lng').value;
	window.open( "http://maps.google.com/maps?q=" + lat + "," + lng + "+(ココ)", '_blank');
}
</script>
</body>
</html>
