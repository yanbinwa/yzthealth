<?php
Page::ignore_view ();

if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wxid = Session::get('wxid');
	$wid = Session::get('wid');

	if (!empty($_POST['ordersId'])) {
		$ordersId = $_POST['ordersId'];
		$orders = new Model('orders');
		$orders->find(array('id'=>$ordersId,'wxid'=>$wxid));
		if ($orders->id) {
			Redirect::to(Conf::$http_path.'wxpay/topayv33.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid.'&orders_str='.$orders->id);		
			
		}
		die;

	}
}