<?php
$wid = Session::get('wid');
if(!$wid){
	Redirect::to('/admin/ind');
}
$m = new Model('z02_set');
$m->find(array('wid'=>$wid));
if($m->try_post()){
 	if($m->save()) {
 		Redirect::to('/admin/shop2/postageGroup.html');
    }  
}
?>