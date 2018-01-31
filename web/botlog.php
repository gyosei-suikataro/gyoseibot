<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="チャットボットのログを表示します。">
<title>チャットボットログ</title>
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

$dbvalue = array();

if ($link) {
	$result = pg_query("SELECT * FROM botlog ORDER BY no DESC");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='day' data-width='7%'>日時</th>
               <th data-column-id='user'  data-width='20%'>ユーザーID</th>
               <th data-column-id='que'  data-width='32%'>質問内容</th>
               <th data-column-id='ans'  data-width='32%'>回答内容</th>
               <th data-column-id='detail'  data-width='6%' data-formatter='details' data-sortable='false'></th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
		array_push($dbvalue,$row);
		echo "<tr>";
		echo "<td>";
		echo $row[0];
		echo "</td>";
		echo "<td>";
		echo substr($row[1], 0,4)."/".substr($row[1], 4,2)."/".substr($row[1], 6,2)." ".substr($row[1], 8,2).":".substr($row[1], 10,2);
		echo "</td>";
		echo "<td>";
		echo $row[2];
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		echo "<td>";
		echo $row[4];
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
	echo "<br>";
}

?>
<div class="container" align="center">
	<input id="btn_del" type="button" class="btn btn-default" value="選択行の削除" onclick="drow()">
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
				<h4 class="modal-title" id="modal-label">詳細</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_date">No</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="dia_no" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_date">日時</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="dia_date" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_user">ユーザーＩＤ</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="dia_user" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_que">質問内容</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="dia_que" rows='10' readonly></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_ans">回答内容</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="dia_ans" rows='10' readonly></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button id="sback" type="button" class="btn btn-default" onclick="shosai_back()">＜＜前へ</button>
				<button id="snext" type="button" class="btn btn-default" onclick="shosai_next()">次へ＞＞</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
<script>
var rowIds = [];
var dbvalue = [];
var shosai_idx = 0;
$(function() {
	dbvalue = <?php echo json_encode($dbvalue); ?>;
	var h = $(window).height();
	$('#wrap').css('display','none');
	$('#loader-bg ,#loader').height(h).css('display','block');

	$("#header").load("header.html");

	$("#grid-basic").bootgrid({
		selection: true,
		multiSelect: true,
		rowSelect: true,
	    keepSelection: true,
	    formatters: {
	        "details": function($column, $row) {
	        	//return "<input type='button' class='btn btn-default' value='詳細' onclick='detailwin("  + $row.no + ")'  data-toggle='modal'  data-target='#shosaiDialog'> ";
	        	return "<input type='button' class='btn btn-default' value='詳細' onclick='detailwin("  + $row.no + ")' > ";
	        	//return "<input type='button' class='btn btn-default'  data-toggle='modal' data-target='#shosaiDialog' value="詳細" /> ";
             }
	    }
	}).on("selected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	        rowIds.push(rows[i].no);
	    }
	    //alert("Select: " + rowIds.join(","));
	}).on("deselected.rs.jquery.bootgrid", function(e, rows)
	{
	    for (var i = 0; i < rows.length; i++)
	    {
	    	rowIds.some(function(v, ii){
	    	    if (v==rows[i].no) rowIds.splice(ii,1);
	    	});
	        //rowIds.push(rows[i].no);
	    }
	    //alert("Deselect: " + rowIds.join(","));
	});
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
				url: "botlogdel.php",
				data: "no=" + rowIds[i],
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

function detailwin(value){
	document.getElementById("btn_modal").click();
	for (var i = 0; i < dbvalue.length; i++){
		if(dbvalue[i][0] == value){
			shosai_idx = i;
			modal_mod(i);
		}
	}
}

function shosai_back(){
	shosai_idx = shosai_idx - 1;
	modal_mod(shosai_idx);
}

function shosai_next(){
	shosai_idx = shosai_idx + 1;
	modal_mod(shosai_idx);
}

function modal_mod(index){
	document.getElementById('dia_no').value  = dbvalue[index][0];
	var idate = dbvalue[index][1].substr(0,4) + "/" + dbvalue[index][1].substr(4,2) + "/" + dbvalue[index][1].substr(6,2) + " " + dbvalue[index][1].substr(8,2) + ":" + dbvalue[index][1].substr(10,2);
	document.getElementById('dia_date').value = idate;
	document.getElementById('dia_user').value  = dbvalue[index][2];
	document.getElementById('dia_que').innerHTML  = dbvalue[index][3];
	document.getElementById('dia_ans').innerHTML  = dbvalue[index][4];

	if(index == 0){
		document.getElementById("sback").disabled = "true";
	}else{
		document.getElementById("sback").disabled = "";
	}

	if(index == dbvalue.length - 1){
		document.getElementById("snext").disabled = "true";
	}else{
		document.getElementById("snext").disabled = "";
	}
}
</script>
</body>
</html>

