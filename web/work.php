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
		<input type="button" class="btn btn-default" onclick="detailwin()" data-toggle="modal"  data-target="#shosaiDialog" value="モーダル表示２" />
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
						<label class="col-sm-2 control-label" for="date">日時</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" id="date" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="userid">ユーザーＩＤ</label>
						<div class="col-sm-10">
							<textarea class="form-control" id="userid" readonly></textarea>
						</div>
					</div>
				</form>
			</div>
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
	//$("#shosaiDialog").modal("show");
	//$('#date').value = "テスト";
	document.getElementById('date').value = "テスト";
	document.getElementById('userid').innerHTML  = "テストマン";
}
</script>
</html>

