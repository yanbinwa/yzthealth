<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
$wxid = Session::get('wxid');
$wid = Session::get('wid');

$red = new Model('newyy_record');
$ddres = $red->where(array('wid'=>Session::get('wid'),'wxid'=>Session::get('wxid')))->order('id desc')->list_all();


$state_arr = array('0'=>'处理中','1'=>'已确认','2'=>'已取消','3'=>'已完成','10'=>'已支付');
$alnum = count($ddres);
foreach ($ddres as $dr){
	$formind = 0;
	$ddx = array();
	
    $dr->iszxpay=false;
	$dr->showpj = 0;
	
    if($dr->state==10)
	{
	   $dr->showpj = 1;
	}
	$h = new Model('newyy');
	$h->find(array('id'=>$dr->hid));
	$dr->yuyue = $h;
   $ddxjson = json_decode($h->bookingset);

	foreach ($ddxjson as $dj){
		$dd = new stdClass();
		if($dj->name!='工单前缀'){
			$dd->name = $dj->name;
			$val = 'form'.$formind;
			if($dj->type=='upload')
			{
			  $imgjson = json_decode($dr->$val);
			  
			  $htmlimg = '';
			  foreach($imgjson as $img)
			  {
				   $htmlimg = $htmlimg. '<img src="'. $img->src .'" style="width:80px;height:80px;margin-right:10px" />';
			  }

			  $dd->val = $htmlimg;
			}
			else
			{
			  $dd->val = $dr->$val;
			}

		}
		//echo $dj->name;
		//是否是在线付款
		if($dj->name=='付款方式'||$dj->name=='选择付款方式'){
			$val = 'form'.$formind;
			if($dr->$val=="现在预付款"||$dr->$val=="在线支付"){
				$dr->iszxpay=true;
			}
			$dd->name = "付款方式";
		}
		
		$formind++;
		$ddx[] = $dd;
	}
	$dr->nr = $ddx;
}

if($wid==41424 || $wid==9014){
	$fkhf = 1;
}else{
	$fkhf = 0;
}
