<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_major');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}else{
		if ('del'==Request::get('do')) {
			$m->remove();
			if($m){
				echo json_encode(array('success'=>true));
				exit;
			}	
		}
	}
}



// if ($goodID=Request::get(1)) {
// 	$m->find(array('wid'=>$wid,'id'=>$goodID));
// 	if ('del'==Request::get('do')) {
// 		$m->is_del = '2';
// 		$m->save();
// 		if($m){
// 			echo json_encode(array('success'=>true));
// 			exit;
// 		}	
// 	}
// }

if($m->try_post()){
	$m->wid = $wid;
	$m->save();
	Redirect::to('major');
}
