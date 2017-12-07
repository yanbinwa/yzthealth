<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$model = new Model('member');
if(Request::get(1) == 'wsh'){
	$where['status'] = '1';
}elseif(Request::get(1)=='shtg'){
	$where['status'] = '2';
}elseif(Request::get(1)=='shbtg'){
	$where['status'] = '3';
}elseif(Request::get(1)=='wtj'){
	$where['status'] ='0';
}else{
	$where['status@!='] = '0';
}
if(Request::post('title')){
	$title = Request::post('title');
	$where['or'] = array('uname@~'=>$title,'true_name@~'=>$title);
}

$p = new Pagination();
$list = $p->model_list($model->where($where)->order('id desc'));

$zs = $model->where(array('status'=>'0'))->count(); //未提交审核
$sq = $model->where(array('status'=>'1'))->count(); //审核中
$shtg = $model->where(array('status'=>'2'))->count(); //审核通过
$shbtg = $model->where(array('status'=>'3'))->count(); //审核不通过



