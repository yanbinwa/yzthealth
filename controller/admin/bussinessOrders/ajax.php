<?php 

$return['success'] = false;
if ($uname = request::get('uname')) {
	$member = new model('member');
	$member->find(array('uname'=>$uname));
	if ($member->id) {
		$return['success'] = true;
		$return['mid'] = $member->id;
		$return['memberName'] = $member->telephone;
	}
	echo json_encode($return);exit;

}