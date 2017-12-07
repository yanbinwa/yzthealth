<?php

$set = new Model('web_set');
$set->find(array('id'=>1));

if($set->try_post()){
	$set->save();
	tusi('保存成功');
	Redirect::delay_to("set.html",1);
}