<?php
$wid = Session::get('wid');
$rewards = new Model('rewards');
	
 
$id  = Request::get(1);

$p = new Pagination();

$res = $p->model_list($rewards->where(array('aid'=>$id,'status'=>'1'))->order('rewardtime desc'));

//打赏总人数
$countren = $rewards->where(array('aid'=>$id,'status'=>'1'))->count();
//打赏总金额
$artice_list = new Model('artice_list');
$artice_list->find($id);
$countprice = $artice_list->reward*$countren;



