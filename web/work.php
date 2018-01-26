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
<div class="container">
	<div class="center-block">
		<input type="button" class="btn btn-default" onclick="detailwin()" value="モーダル表示" />
	</div>
</div>
<div class="modal fade" id="modal_box">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="box_inner">
        <p>
          てすとでーーーす
        </p>
        <p class="text-center"><a class="btn btn-primary" data-dismiss="modal" href="#">閉じる</a></p>
      </div>
    </div>
  </div>
</div>
</body>
<script>
/*
$(function(){
	$("#header").load("header.html");
});
*/

function detailwin(){
	$('#modal_box').modal('show');
}
</script>
</html>

