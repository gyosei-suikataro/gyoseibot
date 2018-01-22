<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=10.0, user-scalable=yes">
<title>ジャンル検索ワード登録</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
<div id="header"></div>
<div class="container">
	<p>大分類</p>
	<select id="g1" class="form-control" style="width: 600px;">
	</select>
	<table id='grid-basic' class='table table-condensed table-hover table-striped'>
		<thead>
			<tr><th data-column-id='intent' data-identifier='true'>検索ワード</th></tr>
		</table>
		<tbody>
			<tr><td></td></tr>
		</tbody>
	</table>
	<br>
	<input type="button" class="btn btn-default"  data-toggle="modal" data-target="#updateDialog" value="追加" />
	<input type="button" class="btn btn-default" onclick="delete()" value="削除" />
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
				<input id="intent" class="form-control" placeholder="検索ワード">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
				<button type="button" class="btn btn-primary">保存</button>
			</div>
		</div>
	</div>
</div>
<?php

$gid1 = "";
$gid2 = "";
$meisho = "";

//ジャンル
$j1value = array();

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
		$j1value = $j1value + array($row[1] => $row[4]);
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

$(function(){
	$("#header").load("header.html");
	//ジャンルの設定
	var j1value = <?php echo json_encode($j1value); ?>;
	var select = document.getElementById('g1');

	for( var key in j1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(j1value[key]);
		option.appendChild(text);
		select.appendChild(option);
	}

	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
		rowSelect: true,
	    keepSelection: true,
	}).on("selected.rs.jquery.bootgrid", function(e, rows)
	{
		for (var i = 0; i < rows.length; i++)
	    {
	        rowIds.push(rows[i].no);
	        rowgid1.push(rows[i].gid1);
	        rowgid2.push(rows[i].gid2);
	        //alert("rowgid1:" + rows[i].gid1 + " rowgid2:" + rows[i].gid2);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	for (var ii = 0; ii < rowIds.length; ii++){
		    	if(rowIds[ii] == rows[i].no){
		    		rowIds.splice(ii,1);
		    		rowgid1.splice(ii,1);
		    		rowgid2.splice(ii,1);
		    		break;
		    	}
	    	}
	        //rowIds.push(rows[i].no);
	    }
	    //alert("Deselect: " + rowIds.join(","));
	});

	//テーブル追加
	$("#grid-basic").table.insertRow( -1 );
	$("#grid-basic").rows[0].cells[0].innerText = "テスト";

});

//分類選択
function bchange(){
	if(document.getElementById('bunrui').value == 1){
		document.getElementById('g1').style.display = "none";
		document.getElementById('g1meisho').style.display = "block";
		document.getElementById('g2meisho').disabled = true;
		document.getElementById('g2meisho').value = "";
	}
	if(document.getElementById('bunrui').value == 2){
		document.getElementById('g1').style.display = "block";
		document.getElementById('g1meisho').style.display = "none"
		document.getElementById('g2meisho').disabled = false;
	}
}

//更新
function update(){
	bunrui = document.getElementById('bunrui').value;
	gid1 = document.getElementById('g1').value;
	g1meisho = document.getElementById('g1').options[document.getElementById('g1').selectedIndex].text;
	if(bunrui == 1){
		meisho = document.getElementById('g1meisho').value;
	}else{
		meisho = document.getElementById('g2meisho').value;
	}
	$.ajax({
		type: "POST",
		url: "genreup.php",
		data: {
			"uiKbn" : uiKbn,
			"bunrui" : bunrui,
			"meisho" : meisho,
			"gid1" : gid1,
			"gid2" : gid2,
			"g1meisho" : g1meisho,
			"meishoOld" : meishoOld
		}
	}).then(
		function(){
			alert("登録が完了しました。");
			window.location.href = "./genre.php";
		},
		function(){
			alert("登録できませんでした。");
		}
	);
}

//もどる
function back(){
	window.location.href = "./genre.php";
}

</script>
</body>
</html>

