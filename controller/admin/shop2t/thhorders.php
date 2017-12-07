<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$unid = Session::get('unid');


$m = new Model('z02_return_log');
$where = array('wid'=>$wid,'unid'=>$unid);
if('w'==Request::get(1))
{
	$where['rreplysta'] = 0;
}
if('t'==Request::get(1))
{
	$where['rtype'] = 1;
}
if('h'==Request::get(1))
{
	$where['rtype'] = 2;
}


$keyword = trim($_GET['keyword']);
$sstatus = intval($_GET['sstatus']);

if(!empty($keyword))
{
	$where['id@~'] = $keyword;
}

if($sstatus!=0)
{
	if($sstatus==1)
	{
		$where['wlstatus'] = 1;
	}
	else
	{
		$where['wlstatus'] = 0;
	}
}
$p = new Pagination();
$rs = $p->model_list($m->where($where)->order('id desc'));

$wcount = $m->where(array('wid'=>$wid,'rreplysta'=>0,'unid'=>$unid))->count();


$tcount = $m->where(array('wid'=>$wid,'rtype'=>1,'unid'=>$unid))->count(); //退货
$hcount = $m->where(array('wid'=>$wid,'rtype'=>2,'unid'=>$unid))->count(); //换货



$rtype = array('1'=>'退货','2'=>'换货');
$rsta = array('1'=>'退款申请中','2'=>'退款中','3'=>'已退款');
$rreplysta = array('0'=>'未处理','1'=>'同意','2'=>'不同意');


