<?php
$f = new Model('web_danye');
$f->find(array('type'=>'集团概况'));
if($f->try_post()){
	$f->type="集团概况";
	$f->save();
	tusi('保存成功');
	Redirect::delay_to("about.html",1);
}