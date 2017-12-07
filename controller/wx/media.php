<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
	$cata = new Model('cata_list');
	$cata_array = $cata->where(array('pid'=>106))->order('sorts')->list_all();

	$set = new Model('web_set');
	$set->find(array('id'=>2));
	
}else{
	die('请从微信进入');
}









