<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="施設ジャンル">
<title>施設ジャンル</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<link href="css/jquery.bootgrid.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
<script src="js/jquery.bootgrid.js"></script>
</head>
<body>
<div id="loader-bg">
  <div id="loader">
    <img src="img/loading.gif" width="80" height="80" alt="Now Loading..." />
    <p>Now Loading...</p>
  </div>
</div>
<div id="wrap" style="display:none">
<div id="header"></div>
<?php

//環境変数の取得
$db_host =  getenv('DB_HOST');
$db_name =  getenv('DB_NAME');
$db_pass =  getenv('DB_PASS');
$db_user =  getenv('DB_USER');

//DB接続
$conn = "host=".$db_host." dbname=".$db_name." user=".$db_user." password=".$db_pass;
$link = pg_connect($conn);

//ジャンル
$j1value = array();

if ($link) {
	$no = 1;
	$result = pg_query("SELECT * FROM genre ORDER BY gid1,gid2");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='bunrui' >分類</th>
               <th data-column-id='g1'  >大分類名称</th>
               <th data-column-id='g2'  >小分類名称</th>
               <th data-column-id='gid1'>分類ID1</th>
               <th data-column-id='gid2'>分類ID2</th>
               <th data-column-id='mod'  data-width='7%' data-formatter='mods' data-sortable='false'></th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		echo "<tr>";
		echo "<td>";
		echo $no++;
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo "大分類";
		}else{
			echo "小分類";
		}
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo $row[4];
		}else{
			$result2 = pg_query("SELECT meisho FROM genre WHERE bunrui = 1 AND gid1 = {$row[1]}");
			$row2 = pg_fetch_row($result2);
			echo $row2[0];
		}
		echo "</td>";
		echo "<td>";
		if($row[0] == 1){
			echo "－";
		}else{
			echo $row[4];
		}
		echo "</td>";
		echo "<td>";
		echo $row[1];
		echo "</td>";
		echo "<td>";
		echo $row[2];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";

	if ($link) {
		$result = pg_query("SELECT * FROM genre WHERE bunrui = 1");
		while ($row = pg_fetch_row($result)) {
			$j1value = $j1value + array($row[1] => $row[4]);
		}
	}
}

?>
<div class="container" align="center">
	<input id="btn_del" type="button" class="btn btn-default" value="選択行の削除" onclick="drow()">
	<input id="btn_ins" type="button" class="btn btn-default" value="ジャンルの追加" onclick="irow()">
	<input id="btn_int" type="button" class="btn btn-default" value="検索ワード追加" onclick="intent()">
	<input id="btn_int" type="button" class="btn btn-default" value="類義語追加" onclick="entity()">
	<input id="btn_modal" type="button" style="display:none" data-toggle="modal"  data-target="#shosaiDialog" value="モーダル表示" />
</div>
</div>
<div class="modal" id="shosaiDialog"  tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" style="width:740px; margin-left: -20px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modal-label">ジャンル登録</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_bunrui">分類</label>
						<div class="col-sm-10">
							<select class="form-control" id="dia_bunrui"  onChange="bchange()">
								<option value="1">大分類</option>
								<option value="2">小分類</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_g1meisho">大分類名称</label>
						<div class="col-sm-10">
							<input id="dia_g1meisho" class="form-control" maxlength="50" placeholder="大分類名称">
							<select id="dia_g1" class="form-control">
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_tel">小分類名称</label>
						<div class="col-sm-10">
							<input id="dia_g2meisho" class="form-control" maxlength="50" placeholder="小分類名称">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" onclick="update()">更新</button>
				<button id="dia_close" type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
<script>
var rowIds = [];
var rowgid1 = [];
var rowgid2 = [];
var meishoOld = "";
var uiKbn = 0;
var gid2 = 0;

$(function() {
	var h = $(window).height();
	$('#wrap').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');

	$("#header").load("header.html");

	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
	    keepSelection: true,
	    formatters: {
	        "mods": function($column, $row) {
	        	return "<input type='button' class='btn btn-default' value='修正' onclick='modwin("  + $row.no + ",\"" + $row.gid1 + "\",\"" + $row.gid2 + "\",\"" + $row.g1 + "\",\"" + $row.g2 + "\")' > ";
             }
	    }
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

	//ジャンルの設定
	var j1value = <?php echo json_encode($j1value); ?>;
	var select = document.getElementById('dia_g1');

	for( var key in j1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(j1value[key]);
		option.appendChild(text);
		select.appendChild(option);
	}
});

$(window).load(function () { //全ての読み込みが完了したら実行
	  $('#loader-bg').delay(900).fadeOut(800);
	  $('#loader').delay(600).fadeOut(300);
	  $('#wrap').css('display', 'block');
	  //$('#btn_del').css('display', 'block');
	  //$('#btn_ins').css('display', 'block');
	  //$('#btn_mod').css('display', 'block');
});

function drow() {
	if(rowIds.length == 0){
		alert("削除する行を選択してください");
		return;
	}
	//大分類が選択されている場合、小分類を削除する
	var idarray = [];
	var g1array = [];
	for (var i = 0; i < rowIds.length; i++){
		idarray.push(rowgid1[i] + "." + rowgid2[i]);
		if(rowgid2[i] == "0"){
			g1array.push(rowgid1[i]);
		}
	}
	g1array.forEach(function(v, i){
		for (var ii = idarray.length - 1; ii >= 0; ii--) {
			var aos = idarray[ii].split(".");
			if(aos[0] == v){
				if(aos[1] > 0){
					idarray.splice(ii,1);
				}
			}
		}
	});
	var myRet = false;
	if(g1array.length > 0){
		myRet = confirm("選択行を削除しますか？\n※大分類を削除すると関連する小分類も削除されます");
	}else{
		myRet = confirm("選択行を削除しますか？");
	}
	if ( myRet == true ){
		$.ajax({
			type: "POST",
			url: "genredel.php",
			data: {
				"id" : idarray
			}
		}).done(function (response) {
			result = JSON.parse(response);
			if(result == "OK"){
				alert("削除しました");
				location.reload();
			}else{
				alert("削除できませんでした");
			}
		}).fail(function () {
			alert("削除できませんでした");
		});
	}
}

function irow(){
	document.getElementById('modal-label').innerHTML  = "ジャンル追加";
	uiKbn = 2;
	meishoOld = "";
	gid2 = 0;
	initmodal();
	document.getElementById('dia_g1').style.display = "none";
	document.getElementById('dia_g2meisho').disabled = true;
	document.getElementById("btn_modal").click();
}

function modwin(no,gid1,_gid2,g1,g2){
	document.getElementById('modal-label').innerHTML  = "ジャンル修正";
	initmodal();
	gid2 = _gid2;
	uiKbn = 1;
	document.getElementById('dia_bunrui').disabled = true;
	if(gid2 > 0){
		meishoOld = g2;
		document.getElementById('dia_bunrui').value = 2;
		document.getElementById('dia_g1').value = gid1;
		document.getElementById('dia_g1').disabled = true;
		document.getElementById('dia_g1meisho').style.display = "none";
		document.getElementById('dia_g2meisho').value = g2;
	}else{
		meishoOld = g1;
		document.getElementById('dia_bunrui').value = 1;
		document.getElementById('dia_g1').value = gid1;
		document.getElementById('dia_g1').style.display = "none";
		document.getElementById('dia_g1meisho').value = g1;
		document.getElementById('dia_g2meisho').disabled = true;
	}
	document.getElementById("btn_modal").click();
}

//分類選択
function bchange(){
	if(document.getElementById('dia_bunrui').value == 1){
		document.getElementById('dia_g1').style.display = "none";
		document.getElementById('dia_g1meisho').style.display = "block";
		document.getElementById('dia_g2meisho').disabled = true;
		document.getElementById('dia_g2meisho').value = "";
	}
	if(document.getElementById('dia_bunrui').value == 2){
		document.getElementById('dia_g1').style.display = "block";
		document.getElementById('dia_g1meisho').style.display = "none"
		document.getElementById('dia_g2meisho').disabled = false;
	}
}

//ダイアログ初期化
function initmodal(){
	document.getElementById('dia_bunrui').value = 1;
	document.getElementById('dia_g1').selectedIndex = 0;
	document.getElementById('dia_g1meisho').value = "";
	document.getElementById('dia_g2meisho').value = "";
	document.getElementById('dia_g1meisho').style.display = "block";
	document.getElementById('dia_g1').style.display = "block";
	document.getElementById('dia_bunrui').disabled = false;
	document.getElementById('dia_g1').disabled = false;
	document.getElementById('dia_g1meisho').disabled = false;
	document.getElementById('dia_g2meisho').disabled = false;
}

//更新
function update(){
	var bunrui = document.getElementById('dia_bunrui').value;
	var gid1 = document.getElementById('dia_g1').value;
	var g1meisho = document.getElementById('dia_g1').options[document.getElementById('dia_g1').selectedIndex].text;
	var meisho = "";
	if(bunrui == 1){
		meisho = document.getElementById('dia_g1meisho').value;
	}else{
		meisho = document.getElementById('dia_g2meisho').value;
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
	}).done(function (response) {
		result = JSON.parse(response);
		if(result == "OK"){
			alert("更新しました");
			location.reload();
		}else{
			alert("更新できませんでした");
		}
    }).fail(function () {
        alert("更新できませんでした");
    });

}

function intent(){
	window.location.href = "./genreint.php";
}

function entity(){
	window.location.href = "./genreent.php";
}
</script>
</body>
</html>

