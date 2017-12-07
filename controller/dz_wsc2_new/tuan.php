<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	//团购产品
	$pro = new Model('z02_sproduct');
	$tuan = $pro->where(array('wid'=>$wid,'istuan'=>'1','status'=>0,'tjssj@>'=>date('Y-m-d H:i:s',time())))->list_all();
	foreach($tuan as $k=>$tu){
		$tuarr = json_decode($tu->rowtemp);
		//查找一个商品中各个规格的价格最低的
		$sell = $tuarr[0]->sell_price;
		$market = $tuarr[0]->market_price;
	   $count = count($tuarr);
	   if($count > 1){
		foreach($tuarr as $k=>$v){
			if($v->sell_price < $sell){
				$sell = $v->sell_price;
				$market = $v->market_price;
			}
		}
	   }
	   $tu->sell = $sell;
	   $tu->market = $market;
	}
	
	
	//判断团购状态
	function ztpd($q){
		if(strtotime($q->tkssj)>time()){
			return '未开始';
		}
		return '进行中';
	}
	
}