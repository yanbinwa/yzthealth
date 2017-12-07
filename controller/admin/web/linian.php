<?php
$f = new Model('web_danye');
$f->find(array('type'=>'健康理念'));
if($f->try_post()){
	$f->type="健康理念";
	$f->save();
	tusi('保存成功');
	Redirect::delay_to("linian.html",1);
}