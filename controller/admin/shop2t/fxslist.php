<?php
	$wd = new Model('fx_wd_list');
	$where = array('wid'=>Session::get('wid'));
	$keyvalue = trim($_GET['keyvalue']);
	$keytype = trim($_GET['keytype']);
	
	$lev = Request::get(1);
	
    if(!empty($lev))
	{
	   if($lev==4)
	   $lev = 0;
	   
	   $where['lev'] = $lev;
	}
	else
	{
	   $where['lev@<>'] = 0;
	}
	if($keyvalue!='')
	{
		$where["$keytype@~"] = $keyvalue;
	}
	
	$eorders = $wd->where($where)->list_all();
	$p = new Pagination();
	$res = $p->model_list($wd->where($where)->order("vip_start_time desc"));
	foreach ($res as $v)
	{
		if($v->pid==0)
		$v->pname = '';
		else
		{
			unset($wd->shop_name);
			$wd->find($v->pid);
			$v->pname = $wd->shop_name;
		}
	}
	
	
	$levarr = array('0'=>'未代理','1'=>'普通代理','2'=>'高级代理','3'=>'至尊代理');

	foreach($eorders as $v)
	{
	    $v->lev = $levarr[$v->lev];
		
		
		unset($wd->shop_name);
		if($v->pid!='')
		{
			$wd->find($v->pid);
			$v->pid = $wd->shop_name;
		}
		else
		$v->pid = '无';

	}
	
	if(trim($_GET['etype'])=='a') //导出
	{
		$keynames=array('代理编号','代理级别','父级代理 ','姓名','手机号','微店名','赠送总金额');
		$array_key=array('id','lev','pid','name','tel','shop_name','flje');
		down_excel($eorders, $keynames,$array_key, $name = '会员列表.xls');
		exit();
	}


	
	$allcc  = $wd->where(array('wid'=>Session::get('wid'),'lev@<>'=>0))->count();
	$allcc1 = $wd->where(array('wid'=>Session::get('wid'),'lev'=>1))->count();
	$allcc2 = $wd->where(array('wid'=>Session::get('wid'),'lev'=>2))->count();
	$allcc3 = $wd->where(array('wid'=>Session::get('wid'),'lev'=>3))->count();
	$dailino = $wd->where(array('wid'=>Session::get('wid'),'lev'=>0))->count();
		
	


