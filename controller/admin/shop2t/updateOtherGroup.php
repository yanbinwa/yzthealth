<?php
access_control();
$wid = Session::get('wid');


$m = new Model('z02_set');
$m->find(array('wid'=>$wid));
if($m->try_post()){
 	if($m->save()) {
 		Redirect::to('postageGroup.html');
    }  
}
?>