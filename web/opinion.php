<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="市政へのご意見">
<title>市政へのご意見</title>
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
	$result = pg_query("SELECT * FROM opinion ORDER BY no DESC");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='date' data-width='7%'>日時</th>
               <th data-column-id='sex'  data-width='5%'>性別</th>
               <th data-column-id='age'  data-width='5%'>年齢</th>
               <th data-column-id='sadness' data-type='numeric' data-width='9%'>悲しみ</th>
               <th data-column-id='joy' data-type='numeric' data-width='9%'>喜び</th>
               <th data-column-id='fear' data-type='numeric' data-width='9%'>恐れ</th>
               <th data-column-id='disgust' data-type='numeric' data-width='9%'>嫌悪</th>
               <th data-column-id='anger' data-type='numeric' data-width='9%'>怒り</th>
               <th data-column-id='opinion'  data-width='30%'>ご意見</th>
               <th data-column-id='detail'  data-width='5%' data-formatter='details' data-sortable='false'></th>
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
		if($row[2] == "1"){
			echo "男性";
		}else if($row[2] == "2"){
			echo "女性";
		}else{
			echo "登録なし";
		}
		echo "</td>";
		echo "<td>";
		echo $row[3];
		echo "</td>";
		echo "<td>";
		echo $row[5];
		echo "</td>";
		echo "<td>";
		echo $row[6];
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
						<label class="col-sm-2 control-label" for="dia_sex">性別</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="dia_sex" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_age">年齢</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="dia_age" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_sadness">悲しみ</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="dia_sadness" readonly>
						</div>
						<label class="col-sm-2 control-label" for="dia_joy">喜び</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="dia_joy" readonly>
						</div>
						<label class="col-sm-2 control-label" for="dia_fear">恐れ</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="dia_fear" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_disgust">嫌悪</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="dia_disgust" readonly>
						</div>
						<label class="col-sm-2 control-label" for="dia_anger">怒り</label>
						<div class="col-sm-2">
							<input type="text" class="form-control" id="dia_anger" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dia_opinion">ご意見</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="dia_opinion" rows='5' readonly></textarea>
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
	    keepSelection: true,
	    formatters: {
	        "details": function($column, $row) {
	        	return "<input type='button' class='btn btn-default' value='詳細' onclick='detailwin("  + $row.no + ")'> ";
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
	var myRet = confirm("選択行を削除しますか？");
	if ( myRet == true ){
		$.ajax({
			type: "POST",
			url: "opiniondel.php",
			data:{
				"no" : rowIds
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
	var sex = "";
	if(dbvalue[index][2] == 1){
	    sex = "男性";
	}
	if(dbvalue[index][2] == 2){
	    sex = "女性";
	}
	document.getElementById('dia_sex').value  = sex;
	document.getElementById('dia_age').value  = dbvalue[index][3];
	document.getElementById('dia_sadness').value  = dbvalue[index][5];
	document.getElementById('dia_joy').value  = dbvalue[index][6];
	document.getElementById('dia_fear').value  = dbvalue[index][7];
	document.getElementById('dia_disgust').value  = dbvalue[index][8];
	document.getElementById('dia_anger').value  = dbvalue[index][9];
	document.getElementById('dia_opinion').innerHTML  = dbvalue[index][4];

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

