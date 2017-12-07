<?php
$set = new Model('web_set');
$set->find();

if(Request::get(1) == '3'){
		$index = "anli";
	}else{
		$index = "news";
	}
$art = new Model('web_art');
$p = new Pagination();
$where=array();
if(Request::get(1)){
	$where['tid'] = Request::get(1);
	$type = new Model('web_type');
	$type->find(array('id'=>Request::get(1)));
}
$t = new Model('web_type');
$trr= $t->map_array('id','name');
$list = $p->model_list($art->where($where)->order('sort desc,id desc'));

//热门排行
$i = 1;
$list_hot = $art->where(array('is_hot'=>1))->limit('4')->list_all();
//竞猜视频
$video = new Model('web_art');
$video->where(array('is_tj'=>1,'tid'=>9))->order('sort desc,id desc')->find();
$pro = new Model('web_pro');
$pro->where(array('status'=>1))->order('id desc')->find();