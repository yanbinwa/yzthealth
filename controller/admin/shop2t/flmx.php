<?php
//提现类型
$fx_user = Session::get('news');

$m = new Model('fx_fl_info_list');
$wd = new Model('fx_wd_list');
$fkwd = new Model('fx_wd_list');

$where['wid'] = Session::get('wid');

$keyvalue = trim($_GET['keyvalue']);
$keytype = trim($_GET['keytype']);

$sta = Request::get(1);
if($sta!='')
$where['status'] = $sta;

if($keyvalue!='')
{
    if($keytype=='fkwdnam')
	{
	   $where["$keytype@~"] = $keyvalue;
	}
	elseif($keytype=='hflrname')  //获赠送人姓名
	{
       $wd->find(array('name@~'=>$keyvalue));
	   $where["wd_id"] = $wd->id;
	}
	elseif($keytype=='hflrphone') //获赠送人手机
	{
	   $wd->find(array('tel@~'=>$keyvalue));
	   $where["wd_id"] = $wd->id;
	}
}


$p = new Pagination();

$res = $p->model_list($m->where($where)->order("id desc"));


foreach($res as $v){
	unset($wd->id);
	unset($fkwd->id);
	unset($fkwd->shop_name);
	$wd->find($v->wd_id);
	$fkwd->find($v->fkwdid);
	if(!empty($v->fkwdid))
	{
		$v->fkwdname = $fkwd->shop_name; 
	}
	else
	{
	   $v->fkwdname = $wd->shop_name;
	}
	
	
	$v->name = $wd->name; 
	$v->tel = $wd->tel; 
	$v->shop_name = $wd->shop_name;
}


//已完成
$where['status']=1;
$ywccc = $m->where($where)->count();
$yflzje = $m->where($where)->sum('fl_amount');
//未完成
$where['status']=0;
$wwccc = $m->where($where)->count();
$wwczje = $m->where($where)->sum('fl_amount');

	



