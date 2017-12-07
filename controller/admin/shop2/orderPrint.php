<?php

$uid = Session::get('uid');
$wid = Session::get('wid');

$orderModel = new Model('z02_order');

if($orderID = Request::get(1)){
	$db = DB::get_db();
	$sql = "select z02.*,memlist.shr,memlist.phone,memlist.yb,memlist.s_addr,memlist.s_p,memlist.s_s,memlist.s_x from z02_order as z02 
	left join micro_membercard_dz_list as memlist on memlist.id = z02.addid 
	where z02.id = ".$orderID;
	$orderInfo =  $db->query($sql);
	if(!$orderInfo){
		break;
	}
	

	$orderInfo = $orderInfo[0];
	$area = new Model('chinaarea');
	$s_p = $area->find(array('ord'=>$orderInfo['s_p']));
	$area = new Model('chinaarea');
	$s_s = $area->find(array('ord'=>$orderInfo['s_s']));
	$area = new Model('chinaarea');
	$s_x = $area->find(array('ord'=>$orderInfo['s_x']));

	$orderInfo['s_addr'] = $s_p->name.$s_s->name.$s_x->name.$orderInfo['s_addr'];

	$prolist   = json_decode($orderInfo['prolist'],true);

}
?>