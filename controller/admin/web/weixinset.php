<?php

$set = new Model('web_set');
$set->find(array('id'=>2));

if($set->try_post()){
	/*print_r($_POST);*/

	$set->save();
	tusi('保存成功');
	Redirect::delay_to("weixinset.html",1);
}
