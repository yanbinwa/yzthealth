<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wid =  Session::get('wid');
	$wxid = Session::get('wxid');
	
	$s =  intval(Request::get('s'));
	$paytype = array('1'=>'到店支付','2'=>'刷卡','3'=>'支付宝','4'=>'财付通','5'=>'微支付');
	$paystatus = array('0'=>'未付款','1'=>'已付款');
	$cfzt = array('0'=>'未就餐','1'=>'已就餐');
	$makesta = array('0'=>'未处理','1'=>'已处理');
	$f_status = array('1'=>'已完成','2'=>'作废');

	$orderm = new Model('micro_wcy_order');
	$where = array('wid'=>$wid,'wxid'=>$wxid);
	
	if($s==3)
	{
		$where['pay_status'] = 0;
		$where['cfzt'] = 0;
	}
	if($s==1)
	{
		$where['pay_status'] = 1;
		$where['cfzt'] = 0;
	}
	if($s==2)
	{
		$where['pay_status'] = 1;
		$where['cfzt'] = 1;
	}



	$orderlist = $orderm->where($where)->order('id DESC')->list_all();
	$lbs = new Model('lbs');
	$pm = new Model('micro_wcy_product');
	foreach ($orderlist as $key=>$v)
	{

		$lbs->find($v->storeid);
		$v->sname = $lbs->name;
		$v->address = $lbs->address;
		$parr = json_decode($v->prows);
		foreach ($parr as $keyz=>$p)
		{
			$pm->find($p->pid);
			$parr[$keyz]->name = $pm->name;
		}

		$orderlist[$key]->parr = $parr;
		$orderlist[$key]->lbs = $lbs;
	}

}else{
	die('请从微信进入');
}