<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$lbs = new Model('youhuiquan');

$p = new Pagination();
$where = array('state@!='=>'2','wid'=>$wid);
$rs = $p->model_list($lbs->where($where)->order('id desc'));


if(Request::post() && 'stop'==Request::get(1))
{
	$id    = intval($_POST['id']);
	if(!empty($id))
	{
		$lbs->update($condition=array('id'=>$id),$data=array('state'=>intval($_POST['state'])));
		if($lbs){
			$errno = 200;
			$error = '优惠券已停用';
		}else{
			$errno = 201;
			$error = '系统错误请刷新后重试';
		}
	}
	else
	{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}
if(Request::post() && 'star'==Request::get(1))
{
	$id    = intval($_POST['id']);
	if(!empty($id))
	{
		$lbs->update($condition=array('id'=>$id),$data=array('state'=>intval($_POST['state'])));
		if($lbs){
			$errno = 200;
			$error = '优惠券已启用';
		}else{
			$errno = 201;
			$error = '系统错误请刷新后重试';
		}
	}
	else
	{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}

if(Request::post() && 'del'==Request::get(1))
{
	$id    = intval($_POST['id']);
	if(!empty($id))
	{
		$lbs->update($condition=array('id'=>$id),$data=array('state'=>intval($_POST['state'])));
		if($lbs){
			$errno = 200;
			$error = '优惠券已删除';
		}else{
			$errno = 201;
			$error = '系统错误请刷新后重试';
		}
	}
	else
	{
		$errno = 203;
		$error = '参数传递错误请刷新后重试';
	}
	$rs = array(
	  'errno' =>$errno,
	  'error' =>$error 
	);
	response::json($rs);
}


