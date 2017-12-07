<?php
access_control();
$wid = Session::get('wid');

$groupModel = new Model('postage_group');
if(Request::get('group_id')){
	$gid = Request::get('group_id');
	$groupModel = new Model('postage_group');
	$groupInfo  = $groupModel->find(array('wid'=>$wid,'id'=>$gid));
	// print_r($groupInfo);
	// echo $groupInfo->id;
	// exit;
}else{
	$groupInfo=='';
}

if($groupInfo->try_post()){
	$groupInfo->wid = $wid;
 	if($groupInfo->save()) {
 		Redirect::to('postageGroup');
    }  
}
?>