<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>類義語登録</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
<div id="header"></div>
<div class="container">
	<p>大分類</p>
	<select id="g1" class="form-control" onChange="g1change()" style="width: 600px;">
	</select>
	<br>
	<p>小分類</p>
	<select id="g2" class="form-control" onChange="g2change()" style="width: 600px;">
	</select>
	<br>
	<table id='grid-basic' class='table table-sm'>
		<thead>
			<tr><th >類義語</th><th style="text-align: right;">テスト</th></tr>
		</table>
		<tbody>
			<tr><td></td></tr>
		</tbody>
	</table>
	<br>
	<input type="button" class="btn btn-default"  data-toggle="modal" data-target="#updateDialog" value="追加" />
	<input type="button" class="btn btn-default" onclick="back()" value="もどる" />
</div>
<div class="modal" id="updateDialog" tabindex="-1">
	<div class="modal-dialog">
    	<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modal-label">追加</h4>
			</div>
			<div class="modal-body">
				<input id="synonym" class="form-control" placeholder="類義語">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
				<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="update()">更新</button>
			</div>
		</div>
	</div>
</div>
<?php

$gid1 = "";
$gid2 = "";
$meisho = "";

//ジャンル
$g1value = array();
$g2value = array();

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

if ($link) {
	$result = pg_query("SELECT * FROM genre WHERE bunrui = 1");
	while ($row = pg_fetch_row($result)) {
		$g1value = $g1value + array($row[1] => $row[4]);
	}
	$result = pg_query("SELECT * FROM genre WHERE bunrui = 2");
	while ($row = pg_fetch_row($result)) {
		$g2value = $g2value + array($row[1].".".$row[2]=> $row[4]);
	}
}
?>

<script>
var bunrui = 0;
var gid1 = 0;
var gid2 = 0;
var meisho = "";
var meishoOld = "";
var uiKbn = 0;
var g1meisho = "";

var rowIds = [];
var rowgid1 = [];
var rowgid2 = [];

var wtable = document.getElementById('grid-basic');

$(function(){
	$("#header").load("header.html");
	//ジャンルの設定
	var g1value = <?php echo json_encode($g1value); ?>;
	var select1 = document.getElementById('g1');

	for( var key in g1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(g1value[key]);
		option.appendChild(text);
		select1.appendChild(option);
	}

	g1change();

});

//インテント取得
function getwtent(){
	g1meisho = document.getElementById('g1').options[document.getElementById('g1').selectedIndex].text;
	g2meisho = document.getElementById('g2').options[document.getElementById('g2').selectedIndex].text;
	$.ajax({
		type: "POST",
		url: "cw2.php",
		data: {
			"param" : "entitySearch",
			"g1meisho" : g1meisho,
			"g2meisho" : g2meisho,
			"sword" : ""
		}
	}).done(function (response) {
		result = JSON.parse(response);
		for( var index in result ) {
			var raw = wtable.insertRow( -1 );
			var td1 = raw.insertCell(-1),td2 = raw.insertCell(-1);
			td1.innerHTML = result[index];
			td2.innerHTML = '<input type="button" value="削除" class="btn btn-default" onclick="delLine(\'' + result[index] + '\',this)" />';
		}
    }).fail(function () {
        alert("Watsonデータの取得に失敗しました");
    });
}

//大分類切替
function g1change(){
	var g2value = <?php echo json_encode($g2value); ?>;
	var select2 = document.getElementById('g2');

	while(select2.lastChild)
	{
		select2.removeChild(select2.lastChild);
	}

	g1value = document.getElementById('g1').value;

	for( var key in g2value ) {
		g12 = key.split(".");
		if(g12[0] == g1value){
			var option = document.createElement('option');
			option.setAttribute('value', g12[1]);
			var text = document.createTextNode(g2value[key]);
			option.appendChild(text);
			select2.appendChild(option);
		}
	}
	g2change();
}

//小分類切替
function g2change(){
	//テーブル初期化
	while( wtable.rows[ 1 ] ) wtable.deleteRow( 1 );

	getwtent();
}

//更新
function update(){
	synonym = document.getElementById('synonym').value;
	g1meisho = document.getElementById('g1').options[document.getElementById('g1').selectedIndex].text;
	g2meisho = document.getElementById('g2').options[document.getElementById('g2').selectedIndex].text;
	$.ajax({
		type: "POST",
		url: "cw2.php",
		data: {
			"param" : "entityUpdate",
			"g1meisho" : g1meisho,
			"g2meisho" : g2meisho,
			"sword" : synonym
		}
	}).done(function (response) {
		result = JSON.parse(response);
		if(result == "OK"){
			alert("更新しました");
			var raw = wtable.insertRow( -1 );
			var td1 = raw.insertCell(-1),td2 = raw.insertCell(-1);
			td1.innerHTML = synonym;
			td2.innerHTML = '<input type="button" value="削除" class="btn btn-default" onclick="delLine(\'' + synonym + '\',this)" />';
		}else{
			alert("更新できませんでした");
		}
    }).fail(function () {
        alert("更新できませんでした");
    });
}

//行削除
function delLine(value,raw){
	var myRet = confirm("類義語「"+ value + "」を削除しますか？");
	if ( myRet == true ){
		g1meisho = document.getElementById('g1').options[document.getElementById('g1').selectedIndex].text;
		g2meisho = document.getElementById('g2').options[document.getElementById('g2').selectedIndex].text;
		$.ajax({
			type: "POST",
			url: "cw2.php",
			data: {
				"param" : "entityDelete",
				"g1meisho" : g1meisho,
				"g2meisho" : g2meisho,
				"sword" : value
			}
		}).done(function (response) {
			result = JSON.parse(response);
			if(result == "OK"){
				alert("削除しました");
				tr = raw.parentNode.parentNode;
				tr.parentNode.deleteRow(tr.sectionRowIndex);
			}else{
				alert("削除できませんでした");
			}
	    }).fail(function () {
	        alert("削除できませんでした");
	    });
	}
}

//もどる
function back(){
	window.location.href = "./genre.php";
}

</script>
</body>
</html>

