<?php
$f = new Model('web_danye');
$f->find(array('type'=>'企业介绍'));
if($f->try_post()){
	$f->type="企业介绍";
	$f->save();
	tusi('保存成功');
	Redirect::delay_to("jieshao.html",1);
}