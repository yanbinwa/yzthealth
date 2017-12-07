<?php
    $wid = Session::get('wid');
	
	if(Request::get(1)==1)
	{
	   $m = new Model('fx_paymfee_log');
	   $title='普通会员交费记录';
	}
	elseif(Request::get(1)==2)
	{
	   $m = new Model('fx_gjpaymfee_log');
	   $title='高级会员交费记录';
	}
	else
	{
	   $m = new Model('fx_zzpaymfee_log');
	   $title='至尊会员交费记录';
	}
	$wd = new Model('fx_wd_list');
	
	$keyvalue = trim($_GET['keyvalue']);
    $keytype = trim($_GET['keytype']);
	if($keyvalue!='')
	{
		if($keytype=='shopname')  //店铺名
		{
		   $wd->find(array('shop_name@~'=>$keyvalue));
		   $where["wxid"] = $wd->wxid;
		}
		if($keytype=='payname') //缴费人
		{
		   $wd->find(array('name@~'=>$keyvalue));
		   $where["wxid"] = $wd->wxid;
		}
	}


	
	$p = new Pagination();
	$where['wid'] = $wid;
	$where['pay_status'] = 1;
	
	$res = $p->model_list($m->where($where)->order("pay_time desc"));
	
	
	
	foreach ($res as $v)
	{
		$wd->find(array('wid'=>$v->wid,'wxid'=>$v->wxid));
		$v->shopname = $wd->shop_name;
		$v->uname = $wd->name;
	}
		
	
	$ywccc = $m->where($where)->count();
	
	$yflzje = $m->where($where)->sum('total');
	


