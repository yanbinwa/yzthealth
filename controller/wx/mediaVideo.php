<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');

if(Request::get(1)){
	$vid = Request::get(1);
	Session::set('vid',$vid);
}
if (!empty($wid) && !empty($wxid)) {
	$pubs = new Model('pubs');
	$pubs->find(array('id'=>'1'));

	$video_list = new Model('artice_list');
	$video = $video_list->find(array('id'=>$vid));
	$cata_list = new Model('cata_list');
    $cata_list->find(array('id'=>$video_list->cateid));	

	$rewards = new Model('rewards');
	$rewards->where(array('wxid'=>$wxid,'aid'=>$vid,'status'=>'1'))->find();
	if ($rewards->has_id()) {
		Page::view('mediaVideo');
	}else{
		Page::view('reward');
	}
}else{
	die('请从微信进入');
}


