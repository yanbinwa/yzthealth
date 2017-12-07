<?php

if($wid = Session::get('wid')){
	$model = new Model('z02_set');
	$model->find(array('wid'=>$wid));
	if($model->try_post()){
	    $model->save();
	}
}
?>