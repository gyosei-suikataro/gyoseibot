<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
<div id="header"></div>
<div class="container">
	<div class="center-block">
		<input type="button" class="btn btn-default" onclick="detailwin()" data-toggle="modal"  data-target="#shosaiDialog" value="モーダル表示" />
	</div>
</div>
<div class="modal" id="shosaiDialog"  tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content" style="width:740px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modal-label">詳細</h4>
			</div>
			<span class="modal-body">
				<br>
				<p style='display:inline;'>　　　　日時　</p>
				<input id="date" type="text" readonly style="width: 600px;">
				<br><br>
				<p style='display:inline;'>ユーザーＩＤ　</p>
				<input id="userid" type="text" readonly style="width: 600px;">
			</span>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
</body>
<script>
$(function(){
	$("#header").load("header.html");
})
function detailwin(){
	$("#shosaiDialog").modal("show");
	//$('#date').value = "テスト";
	document.getElementById('date').value = "テスト";
	document.getElementById('userid').value = "テストマン";
}
</script>
</html>

