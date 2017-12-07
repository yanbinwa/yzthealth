<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('advert');
$adarr = $m->order('id')->list_all_array();


if($m->try_post()){
	foreach ($_POST['url'] as $k => $v) {
		if (!empty($_POST['url'][$k]) && !empty($_POST['pic'][$k])) {
			$id = $k+1;
			$url = $_POST['url'][$k];
			$pic = $_POST['pic'][$k];
			$title = $_POST['title'][$k];
			$db = DB::get_db();
			$sql = "update advert set url='".$url."',pic='".$pic."',title='".$title."' where id=".$id." ";
			$db->query($sql);
		}
	}
	
	tusi('保存成功');
	Redirect::delay_to('editad',1);
}
