<?php
access_control();
$title = Request::post('title');
$where = array('remark_status'=>1,'status'=>1,'pay_status'=>1);
if($title){
	$where['full_name@~'] = $title;
}
$orders = new Model('orders');

if('del'==Request::get(1))
{
	$id = Request::get(2);
	$orders->find(array('id'=>$id));

	if($orders->has_id()&&$orders->remark_status == 1){
		$orders->remark_status =2;
		$orders->save();
	}
		
}
$p = new Pagination();
$res = $p->model_list($orders->where($where)->order('id desc'));


