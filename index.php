<?php
session_start();

include "common.php";

$short = $_REQUEST['short'];
$long = $_REQUEST['long'];
$mode = $_REQUEST['mode'];

$id = $_REQUEST['id'];
$pw = $_REQUEST['pw'];

if($mode == "" && $short != "") {
	$pos = strpos($short, "+");
	if($pos !== false) {
		$temp = rtrim($short, "+");
		$long = get_long_url($temp);
	} else {
		$sql = "select * from short_url where short_url='$short'";
		$row = dbqueryfetch($sql);

		if($row['long_url'] != "") {
			$sql2 = "update short_url set hit=hit+1 where short_url='$short'";
			dbquery($sql2);

			header("Location: $row[long_url]");
		}
	}

} else {
	if($mode == "make") {
		if($long == "") $temp = "";
		else {
			$pos = strpos($long, "http://");
			$pos2 = strpos($long, "https://");
			$pos3 = strpos($long, "+");
			if($pos3 !== false) {
				$temp = rtrim($long, "+");
				$long = get_long_url($temp);
			} else if($pos === false && $pos2 === false) $long = "http://".$long;
			$temp = make_short_url($long);
		}
	} else if($mode == "login") {
		$id_list = array("admin"=>"1234");
		if($id != "" && $pw != "") {
			if($id_list[$id] == $pw) {
				$_SESSION['user_id'] = $id;
				alert_redir("관리자 로그인 성공", "index.php");
			} else {
				$_SESSION['user_id'] = "";
				alert_redir("관리자 로그인 실패", "index.php");
			}
		} else {
			alert_redir("", "index.php");
		}

	} else if($mode == "reg") {
		if($long != "" && $short != "") {
			$pos = strpos($long, "http://");
			$pos2 = strpos($long, "https://");
			if($pos === false && $pos2 === false) $long = "http://".$long;

			$sql = "insert into short_url set short_url='$short', long_url='$long', ins_dt=now()";
			dbquery($sql);
			$temp = $short;
		} else $temp = "";

	}
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>단축 URL 서비스</title>
<link href="./css/bootstrap.min.css" rel="stylesheet">
<link href="./css/notosanskr.css" rel="stylesheet">
<style>
	body, td, div {font-size:14px; font-family: 'Noto Sans KR', sans-serif; color:#555555; line-height:150%;}
	div {padding:10px;}
</style>
</head>

<body>

<div class="container">
	<div class="text-center"><h1>단축 URL 서비스</h1></div>
	<div>&nbsp;</div>

	<form method="post">
	<input type="hidden" name="mode" value="make">
	<div class="text-center">
		<input type="text" class="form-control" name="long" size="50" value="<?=$long?>" placeholder="http://" style="border:5px solid #3399cc;font-size:14pt;height:70px;">
	</div>
	<div>&nbsp;</div>

	<div class="text-center">
		<input type="submit" class="btn btn-primary btn-lg" value="단축 URL 생성하기">
	</div>
	<div>&nbsp;</div>

	<div class="text-center">
		<?if($long != "") {?>
		<p>단축 URL : <a href="http://<?=$my_domain?>/<?=$temp?>" target="_blank">http://<?=$my_domain?>/<?=$temp?></a>  (Hit: <?=number_format(get_hit_count($temp))?>)</p>
		<p><input type="text" size="30" value="http://<?=$my_domain?>/<?=$temp?>" onclick="select()" style="border:5px solid #ff6600;font-size:12pt;height:40px;max-width:100%;width:300px;padding:10px;"></p>
		<?}?>
	</div>
	<div>&nbsp;</div>

	<div class="text-center">
	<?if($user_id == "") {?>
	<span data-toggle="modal" data-target="#loginModal" style="cursor:pointer;">[관리자 로그인]</span>
	<?} else {?>
	<span data-toggle="modal" data-target="#regModal" style="cursor:pointer;">[단축 URL 입력]</span>
	<?}?>

	</div>
	</form>

</div>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="loginModalLabel">관리자 로그인</h4>
  </div>
  <div class="modal-body">
	  <form name="form" method="post" action="index.php">
	  <input type="hidden" name="mode" value="login">
		<div class="form-group">
		  <label>아이디</label>
		  <input type="text" class="form-control" name="id" value="" placeholder="" required>
		</div>
		<div class="form-group">
		  <label>비밀번호</label>
		  <input type="password" class="form-control" name="pw" value="" placeholder="" required>
		</div>
  </div>
  <div class="modal-footer">
	<input type="submit" class="btn btn-primary" value="로그인"> &nbsp;&nbsp;&nbsp;
	<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
  </div>
  </form>
</div>
</div>
</div>


<div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
  <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="regModalLabel">단축 URL 입력</h4>
  </div>
  <div class="modal-body">
	  <form name="form" method="post" action="index.php">
	  <input type="hidden" name="mode" value="reg">
		<div class="form-group">
		  <label>Long URL</label>
		  <input type="text" class="form-control" name="long" value="" placeholder="http://" required>
		</div>
		<div class="form-group">
		  <label>Short URL</label>
		  <input type="text" class="form-control" name="short" value="" placeholder="영문+숫자(20자리 이내) 입력해주세요" required>
		</div>
  </div>
  <div class="modal-footer">
	<input type="submit" class="btn btn-primary" value="단축 URL 입력"> &nbsp;&nbsp;&nbsp;
	<button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
  </div>
  </form>
</div>
</div>
</div>


<script src="./js/jquery.min.js"></script>
<script src="./js/jquery-ui.js"></script>
<script src="./js/bootstrap.min.js"></script>

</body>
</html>
