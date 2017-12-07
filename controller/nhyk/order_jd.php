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
	$order = new Model('yypk_hotel_record');
	$hotel = new Model('yypk_hotel');
	$room = new Model('yypk_hotel_room');
	$where = array('wid'=>$wid,'wxid'=>$wxid);
	$p = new Pagination();
	$list = $p->model_list($order->where($where)->order('id DESC'));
	foreach($list as $k=>$v){
		$list[$k]->hotel = $hotel->where(array('id'=>$v->hid))->find();
		$list[$k]->room = $room->where(array('id'=>$v->rid))->find();
	}
	$state_arr = array('0'=>'处理中','1'=>'付款成功','2'=>'已取消','3'=>'交易完成','4'=>'已确认');
}else{
	die('非法操作 请返回');
}