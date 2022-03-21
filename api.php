<?php
include "common.php";

$url = $_REQUEST['url'];
$mode = $_REQUEST['mode'];

if($mode != "shorten") {
	echo "BAD MODE VALUE";
	exit;
}

if($url == "") {
	echo "NO LONG URL VALUE";
	exit;
}

if($mode == "shorten") {
	if($url != "") {
		$short_url = make_short_url($url);
		$arr = array("short_url"=>"http://{$my_domain}/".$short_url);
		echo json_encode($arr);
	}
}

?>