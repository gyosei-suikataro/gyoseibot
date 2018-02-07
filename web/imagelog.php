<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="チャットボットの画像ログを表示します。">
<title>チャットボット画像ログ</title>
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



if ($link) {
	$result = pg_query("SELECT * FROM logimage ORDER BY no DESC");
	echo "<table id='grid-basic' class='table table-condensed table-hover table-striped'>";
	echo "<thead>";
	echo "<tr><th data-column-id='no' data-type='numeric' data-identifier='true' data-width='3%'>No</th>
               <th data-column-id='day' data-width='10%'>日時</th>
               <th data-column-id='user'  data-width='20%'>ユーザーID</th>
               <th data-column-id='img'  data-width='20%'  data-formatter='image'>送信画像</th>
               <th data-column-id='cls'  data-width='15%'>分類</th>
               <th data-column-id='scr'  data-width='15%'>確信度</th>
               <th data-column-id='zm'  data-width='7%' data-formatter='zoom' data-sortable='false'></th>
           </tr>";
	echo "</thead>";
	echo "<tbody>";
	while ($row = pg_fetch_row($result)) {
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
		//echo "<img class='table-img' src='getimage.php?id=" . $row[0]. "'/>";
		//echo "<img class='table-img' src='https://placeholdit.imgix.net/~text?txtsize=23&bg=F44336&txtclr=ffffff&w=50&h=50'/>";
		echo "</td>";
		$bunrui = "";
		switch (trim($row[5])){
			//燃えるゴミ
			case "burnable":
				$bunrui = "可燃ゴミ";
				break;
			//燃えないゴミ
			case "nonburnable":
				$bunrui = "不燃ゴミ";
				break;
				//資源ゴミ
			case "resource":
				$bunrui = "資源ゴミ";
				break;
				//粗大ゴミ
			case "bulky":
				$bunrui = "粗大ゴミ";
				break;
				//その他
			default:
				$bunrui = "分類不可";
				break;
		}
		echo "<td>";
		echo $bunrui;
		//echo $row[5];
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
	<input id="btn_modal" type="button" style="display:none" data-toggle="modal"  data-target="#image_Modal" value="モーダル表示" />
</div>
</div>
<div class="modal" id="image_Modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" id="dia_cont">
			<div class="modal-body" align="center">
				<p id="dia_kaku"></p>
				<img  id="dia_image"/>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
<script>
var rowIds = [];
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
	        "image": function($column, $row) {
	              return "<img class='table-img' src='getimage.php?id=" + $row.no + "' />";
	         },
	        "zoom": function($column, $row) {
                  //return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + $row.no + "\">画像拡大</button> ";
	        	//return "<Form><input type='button' value='画像拡大' onClick='window.open('" + getimage.php?id=$row.no + "','test','width=250,height=100,');'></Form> ";
	        	//return "<Form><input type='button' value='画像拡大' onclick='imgwin()'></Form> ";
	        	return "<input type='button' class='btn btn-default' value='画像拡大' onclick='imgwin("  + $row.no + ",\"" + $row.cls + "\"," + $row.scr + ")'> ";
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
			url: "imagedel.php",
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

function imgwin(imgno,bunrui,kakushin){

	var oimg = new Image();
	oimg.src = "getimage.php?id=" + imgno;
	var img = document.getElementById("dia_image");
	img.width = oimg.width;
	img.height = oimg.height;
	document.getElementById('dia_kaku').innerHTML  = "分類：" + bunrui + "　　確信度：" + kakushin;
	img.src = "getimage.php?id=" + imgno;
	var img = document.getElementById("dia_image");
	if(img.width > 600){
		var orgWidth  = img.width;
		var orgHeight = img.height;
		img.width = 600;
		img.height = orgHeight * (img.width / orgWidth);
	}
	var imgwidth = img.width + 40;
	if(imgwidth < 600){
		imgwidth = 600;
	}
	var imgmar = img.width / 2;
	document.getElementById('dia_cont').style.width = imgwidth + "px";
	document.getElementById("btn_modal").click();
}

</script>
</body>
</html>

