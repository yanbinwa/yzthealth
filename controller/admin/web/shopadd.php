<?php
$wid = Session::get('wid');

$m = new Model('web_shop');
$id = Request::get(1);
$m->find(array('id'=>$id));
// dump($typeArr);
if($m->try_post()){
	$m->save();
	tusi('保存成功');
	Redirect::delay_to("shop.html",1);
}

