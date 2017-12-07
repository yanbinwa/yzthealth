<?php
$f = new Model('web_danye');
$f->find(array('type'=>'联系我们'));
if($f->try_post()){
	$f->type="联系我们";
	$f->save();
	tusi('保存成功');
	Redirect::delay_to("content.html",1);
}