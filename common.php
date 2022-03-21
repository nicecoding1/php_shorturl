<?php
$db_host = "localhost";
$db_user = "test";
$db_pass = "test";
$db_name = "short_url";
$my_domain = "localhost";

$user_id = $_SESSION['user_id'];

$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789";
$arr = str_split($str);

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die("DB connect fail");
mysqli_query($conn, "set names utf8");

function dbquery($sql) {
	global $conn;
	$res = @mysqli_query($conn, $sql) or die(mysqli_error($conn)." | ".$sql);
	return $res;
}

function dbfetch($res) {
	$row = mysqli_fetch_array($res);
	return $row;
}

function dbqueryfetch($sql) {
	global $conn;
	$res = @mysqli_query($conn, $sql) or die(mysqli_error($conn)." | ".$sql);
	$row = mysqli_fetch_array($res);
	return $row;
}

function chk_short_url_exist($s) {
	$sql = "select * from short_url where short_url='$s'";
	$row = dbqueryfetch($sql);
	if($row['short_url'] != "") return true;
	else return false;
}

function make_short_url($long) {
	global $arr;
	shuffle($arr);

	$sql = "select * from short_url where long_url='$long'";
	$row = dbqueryfetch($sql);
	if($row['short_url'] != "") return $row['short_url'];

	$temp = $arr[0].$arr[1].$arr[2].$arr[3].$arr[4].$arr[5];
	$test = chk_short_url_exist($temp);
	if($test) {
		make_short_url($long);
	} else {
		$sql = "insert into short_url set short_url='$temp', long_url='$long', ins_dt=now()";
		dbquery($sql);

		return $temp;
	}
}

function alert_redir($msg, $url) {
	$msg = str_replace("\n", "\\n", $msg);
	echo("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">");
	echo("<script>\n");
	if($msg) echo("alert(\"$msg\");\n");
	if($url) echo("window.location='$url';\n");
	else echo("history.back();\n");
	echo("</script>\n");
	exit;
}

function get_hit_count($url) {
	$sql = "select * from short_url where short_url='$url'";
	$row = dbqueryfetch($sql);
	return $row['hit'];
}

function get_long_url($url) {
	$sql = "select * from short_url where short_url='$url'";
	$row = dbqueryfetch($sql);
	return $row['long_url'];
}

?>