<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_order');
$rs = $m->where(array('wid'=>$wid))->list_all();

$pay_status = array('0'=>'未支付','1'=>'已支付','2'=>'退款');