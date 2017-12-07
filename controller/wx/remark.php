<?php


$orderId = Request::get(1); 
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$wechatset = new Model('web_set');
$wechatset->find(array('id'=>2));
if (!empty($wid) && !empty($wxid)) {
	$order = new Model('orders');

	if ($_POST) {
		$order->find(array('id'=>$_POST['remarkId']));
		if ($order->id) {
			$order->remark_content = $_POST['remarks'];
			$order->remark_type = $_POST['remark_type'];
			$order->remark_time = date('Y-m-d H:i:s',time());
			$order->remark_status = 1;
			$order->save();
			tusi('发表评论成功！');
			Redirect::delay_to("/user/ddcenter.html",1);
			
		}
		
	}
	
}	