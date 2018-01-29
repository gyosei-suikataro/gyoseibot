<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Menu</title>
<link href="css/common.css" rel="stylesheet" />
<link href="css/bootstrap.css" rel="stylesheet" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.js"></script>
</head>
<body>
	<nav class="navbar navbar-default navbar-static-top navbar-inverse">
		<div class="container">
			<ul class="nav navbar-nav">
				<li class="active">
				<a href="./index.php"><span class="glyphicon glyphicon-home"></span> Home</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-align-justify"></span> Menu<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="./botlog.php"><span class="glyphicon glyphicon-list"></span> ログ参照</a></li>
						<li><a href="./imagelog.php"><span class="glyphicon glyphicon-list"></span> 画像ログ参照</a></li>
						<li><a href="./shisetsu.php"><span class="glyphicon glyphicon-list"></span> 施設情報</a></li>
						<li><a href="./genre.php"><span class="glyphicon glyphicon-list"></span> 施設ジャンル</a></li>
						<li><a href="./opinion.php"><span class="glyphicon glyphicon-list"></span> 市政へのご意見</a></li>
						<li><a href="./test.php"><span class="glyphicon glyphicon-list"></span> ボットテスト</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
<div class="container">
	<div class="center-block">
		<input type="button" class="btn btn-default" onclick="detailwin()" value="モーダル表示" />
	</div>
</div>
<div class="modal" id="shosaiDialog"  tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modal-label">詳細</h4>
			</div>
			<div class="modal-body">
				<p style='display:inline;'>　　　　日時　</p>
				<input id="date" type="text" readonly style="width: 600px;">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
			</div>
		</div>
	</div>
</div>
</body>
<script>

function detailwin(){
	$('#shosaiDialog').modal('show');
}
</script>
</html>

