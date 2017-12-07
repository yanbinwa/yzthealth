<?php
Page::ignore_view ();
$wid = $_POST ['wid'];
$wxid = $_POST ['wxid'];
$img = $_POST ['img'];
if ($wid && $wxid && $img) {
	$mem = new Model ( "micro_membercard_member_list" );
	$mem->where ( array ('wid' => $wid, 'wxid' => $wxid ) )->find ();
	if ($mem->id) {
		$mem->head_pic = $img;
		$re = $mem->save ();
		if ($re) {
			echo "上传成功！";
		} else {
			die ();
		}
	} else {
		die ();
	}
} else {
	die ();
}