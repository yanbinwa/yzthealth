<?php
$pid = intval(Request::get(1)); 
$sproduct = new Model('z02_sproduct');
$sproduct_array = $sproduct->where(array('id'=>$pid,'is_ll'=>1,'sh_status'=>1))->find();
if (empty($sproduct_array->id)) {
	Redirect::delay_to("shop",0);
}

$rowtemp = json_decode($sproduct_array->rowtemp,true);


foreach ($rowtemp as $key => $value) {
	$aryLevel .= $key.',';
	$sell_price .= $value['sell_price'].',';
}
$sell_price = rtrim($sell_price, ",");
$aryLevel =  rtrim($aryLevel, ",");


