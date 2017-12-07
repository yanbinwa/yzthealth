<?php
$index = "pro";
$set = new Model('web_set');
$set->find();

$id = Request::get(1);
// 
$art = new Model('web_pro');
$p = new Pagination();
$where['status']=1;
if($id){
	$where['sid'] = $id;
	$shop = new Model('web_shop');
	$shop->find(array('id'=>$id));
}
$list = $p->model_list($art->where($where)->order('sort desc,id desc'));
// dump($list);