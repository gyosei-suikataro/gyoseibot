<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="施設情報">
<title>施設情報</title>
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
$j2value = array();

if ($link) {
	$result = pg_query("SELECT * FROM shisetsu ORDER BY genre1, genre2");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='id' data-type='numeric' data-identifier='true' data-width='3%'>ID</th>
               <th data-column-id='meisho' data-width='10%'>名称</th>
               <th data-column-id='jusho'  data-width='10%'>住所</th>
               <th data-column-id='tel'  data-width='7%'>電話番号</th>
               <th data-column-id='genre1'  data-width='10%'>ジャンル１</th>
               <th data-column-id='genre2'  data-width='10%'>ジャンル２</th>
               <th data-column-id='lat'  data-width='5%'>緯度</th>
               <th data-column-id='lng'  data-width='5%'>経度</th>
               <th data-column-id='iurl'  data-width='17%'>画像URL</th>
               <th data-column-id='url'  data-width='17%'>詳細URL</th>
               <th data-column-id='mod'  data-width='6%' data-formatter='mods' data-sortable='false'></th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		echo "<tr>";
		echo "<td>";
		echo $row[0];
		echo "</td>";
		echo "<td>";
		echo $row[1];
		echo "</td>";
		echo "<td>";
		echo $row[2];
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		$result2 = pg_query("SELECT meisho FROM genre WHERE gid1 = {$row[4]} AND bunrui = 1");
		$row2 = pg_fetch_row($result2);
		echo "<td>";
		echo $row2[0];
		echo "</td>";
		$result2 = pg_query("SELECT meisho FROM genre WHERE gid1 = {$row[4]} AND gid2 = {$row[5]} AND bunrui = 2");
		$row2 = pg_fetch_row($result2);
		echo "<td>";
		echo $row2[0];
		echo "</td>";
		echo "<td>";
		echo $row[7];
		echo "</td>";
		echo "<td>";
		echo $row[8];
		echo "</td>";
		echo "<td>";
		echo $row[9];
		echo "</td>";
		echo "<td>";
		echo $row[10];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";

	//ジャンル情報検索
	$result = pg_query("SELECT * FROM genre WHERE bunrui = 1");
	while ($row = pg_fetch_row($result)) {
		$j1value = $j1value + array($row[1] => $row[4]);
	}

	foreach($j1value as $key => $value){
		$result = pg_query("SELECT * FROM genre WHERE bunrui = 2 AND gid1 = {$key}");
		$arr = array();
		while ($row = pg_fetch_row($result)) {
			$arr = $arr + array($row[2] => $row[4]);
		}
		$j2value = $j2value + array($key => $arr);
	}
}

?>
<div class="container" align="center">
	<input id="btn_del" type="button" class="btn btn-default" value="選択行の削除" onclick="drow()">
	<input id="btn_ins" type="button" class="btn btn-default" value="施設の追加" onclick="irow()">
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
				<h4 class="modal-title" id="modal-label">施設登録</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_meisho">施設名称</label>
						<div class="col-sm-10">
							<input id="dia_meisho" class="form-control" maxlength="40" placeholder="行政公園">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_jusho">住所</label>
						<div class="col-sm-10">
							<input id="dia_jusho" class="form-control" maxlength="128" placeholder="行政市行政1-1-1">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_tel">電話番号</label>
						<div class="col-sm-10">
							<input id="dia_tel" class="form-control" type="tel" maxlength="14" placeholder="000-000-0000">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_j1">ジャンル１</label>
						<div class="col-sm-10">
							<select class="form-control" id="dia_j1"  onChange="j1change()">
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_j2">ジャンル２</label>
						<div class="col-sm-10">
							<select class="form-control" id="dia_j2">
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_latlng">緯度・経度</label>
						<div class="col-sm-10">
							<input id="dia_latlng" class="form-control" maxlength="33" placeholder="999.99999,999.99999">
							<input type="button" class="btn btn-default" style="display:inline;" onclick="map()" value="地図の確認" style="width: 100px;"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_iurl">画像ＵＲＬ</label>
						<div class="col-sm-10">
							<input id="dia_iurl" class="form-control" maxlength="300" placeholder="https://www.yyy.zzz.jpg">
							<input type="button" class="btn btn-default" style="display:inline;" onclick="image()" value="画像の確認" style="width: 100px;"/>
							※必ずhttpsから始まるURLを指定してください
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_url">詳細ＵＲＬ</label>
						<div class="col-sm-10">
							<input id="dia_url" class="form-control" maxlength="300" placeholder="http://www.yyy.zzz.html">
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
var modID = "";
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
	        	return "<input type='button' class='btn btn-default' value='修正' onclick='modwin("  + $row.id + ",\"" + $row.meisho + "\",\"" + $row.jusho + "\",\"" + $row.tel + "\",\"" + $row.genre1 + "\",\"" + $row.genre2 + "\",\"" + $row.lat + "\",\"" + $row.lng + "\",\"" + $row.iurl + "\",\"" + $row.url + "\")' > ";
             }
	    }
	}).on("selected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	        rowIds.push(rows[i].id);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	rowIds.some(function(v, ii){
	    	    if (v==rows[i].id) rowIds.splice(ii,1);
	    	});
	        //rowIds.push(rows[i].no);
	    }
	    //alert("Deselect: " + rowIds.join(","));
	});

	//ジャンルの設定
	var j1value = <?php echo json_encode($j1value); ?>;
	var select = document.getElementById('dia_j1');

	for( var key in j1value ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(j1value[key]);
		option.appendChild(text);
		select.appendChild(option);
	}
	j1change();
});

$(window).load(function () { //全ての読み込みが完了したら実行
	  $('#loader-bg').delay(900).fadeOut(800);
	  $('#loader').delay(600).fadeOut(300);
	  $('#wrap').css('display', 'block');
});

function drow() {
	if(rowIds.length == 0){
		alert("削除する行を選択してください");
		return;
	}
	var successFlg = true;
	var myRet = confirm("選択行を削除しますか？");
	if ( myRet == true ){
		for (var i = 0; i < rowIds.length; i++){
			$.ajax({
				type: "POST",
				url: "shisetsudel.php",
				data: "id=" + rowIds[i],
			}).then(
				function(){
				},
				function(){
					successFlg = false;
				}
			);
		}
		if( successFlg == true){
			alert("削除しました");
			location.reload();
		}else{
			alert("削除できませんでした");
		}
	}
}

function modwin(id,meisho,jusho,tel,genre1,genre2,lat,lng,iurl,url){
	document.getElementById('modal-label').innerHTML  = "施設情報修正";
	modID = id;
	document.getElementById('dia_meisho').value = meisho;
	document.getElementById('dia_jusho').value = jusho;
	document.getElementById('dia_tel').value = tel;
	var options = document.getElementById('dia_j1').options;
	for(var i = 0; i < options.length; i++){
		if(options[i].text === genre1){
			options[i].selected = true;
			break;
		};
	};
	j1change();
	var options = document.getElementById('dia_j2').options;
	for(var i = 0; i < options.length; i++){
		if(options[i].text === genre2){
			options[i].selected = true;
			break;
		};
	};
	document.getElementById('dia_latlng').value = lat + "," + lng;
	document.getElementById('dia_iurl').value = iurl;
	document.getElementById('dia_url').value = url;
	document.getElementById("btn_modal").click();
}

function irow(){
	document.getElementById('modal-label').innerHTML  = "施設情報追加";
	modID = "";
	document.getElementById('dia_meisho').value = "";
	document.getElementById('dia_jusho').value = "";
	document.getElementById('dia_tel').value = "";
	document.getElementById('dia_j1').selectedIndex = 0;
	j1change();
	document.getElementById('dia_latlng').value = "";
	document.getElementById('dia_iurl').value = "";
	document.getElementById('dia_url').value = "";
	document.getElementById("btn_modal").click();
}

//更新
function update(){
	var meisho = document.getElementById('dia_meisho').value;
	var jusho = document.getElementById('dia_jusho').value;
	var tel = document.getElementById('dia_tel').value;
	var j1 = document.getElementById('dia_j1').value;
	var j2 = document.getElementById('dia_j2').value;
	var latlng = document.getElementById('dia_latlng').value;
	var arrayOfStrings = latlng.split(",");
	var lat = arrayOfStrings[0];
	var lng = arrayOfStrings[1];
	var iurl = document.getElementById('dia_iurl').value;
	var url = document.getElementById('dia_url').value;
	$.ajax({
		type: "POST",
		url: "shisetsuup.php",
		data: {
			"id" : modID,
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

//ジャンル選択
function j1change(){
	var select = document.getElementById('dia_j2');
	while (0 < select.childNodes.length) {
		select.removeChild(select.childNodes[0]);
	}

	var j2value = <?php echo json_encode($j2value); ?>;
	var janru = j2value[document.getElementById('dia_j1').value];

	for( var key in janru ) {
		var option = document.createElement('option');
		option.setAttribute('value', key);
		var text = document.createTextNode(janru[key]);
		option.appendChild(text);
		select.appendChild(option);
	}
}

//地図の確認
function map(){
	latlng = document.getElementById('latlng').value;
	window.open( "http://maps.google.com/maps?q=" + latlng + "+(ココ)", '_blank');
}

//画像の確認
function image(){
	imageurl = document.getElementById('iurl').value;
	window.open( imageurl, '_blank');
}
</script>
</body>
</html>

