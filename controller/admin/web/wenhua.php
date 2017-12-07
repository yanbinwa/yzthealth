<?php
$f = new Model('web_danye');
$f->find(array('type'=>'企业文化'));
if($f->try_post()){
	$f->type="企业文化";
	$f->save();
	tusi('保存成功');
	Redirect::delay_to("wenhua.html",1);
}