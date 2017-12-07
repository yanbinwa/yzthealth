<?php
$wid = Session::get('wid');

$m = new Model('web_art');
$id = Request::get(1);
$m->find(array('id'=>$id));
//获取所有的文章分类
$type = new Model('web_type');
$typeArr = $type->order('sort Desc,id Desc')->map_array('id','name');
// dump($typeArr);
if($m->try_post()){
	$m->save();
	tusi('保存成功');
	Redirect::delay_to("art.html",1);
}

