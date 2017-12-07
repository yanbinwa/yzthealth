<?php
$wid = Session::get('wid');

$m = new Model('web_pro');
$id = Request::get(1);
$m->find(array('id'=>$id));
$shop = new Model('web_shop');
$typeArr = $shop->where(array('status'=>'1'))->map_array('id','name'); 
// dump($typeArr);
if($m->try_post()){
	$m->creat_time = date('Y-m-d H:i:s',time());
	$m->save();
	tusi('保存成功');
	Redirect::delay_to("good.html",1);
}

