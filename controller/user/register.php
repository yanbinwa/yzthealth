<?php

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$opid = Session::get('opid');
if (!empty($wid) && !empty($wxid)) {
    $member = new Model('member');
    $member->find(array('wxid'=>$wxid,'wid'=>$wid));
    if ($member->has_id()) {
    	Redirect::to('coupanList');
    }
	if($_POST){
		$telephone = trim($_POST['telephone']);
		$true_name = trim($_POST['true_name']);
		$validCode = trim($_POST['validCode']);
		$sendCode = new Model('member_send_code_log');
		$sendCode->where(array('tel'=>$telephone,'status'=>0))->order('id desc')->find();
		if($sendCode->has_id() && $sendCode->code == $validCode){
			$member->wxid = $wxid;
    		$member->wid = $wid;
    		$member->true_name = $true_name;
    		$member->telephone = $telephone;
			$member->rtime = date('Y-m-d H:i:s',time());
            $member->fx_wxid =  $opid;
    		$member->save();	

    		$sendCode->status = 1;
    		$sendCode->save();
            Redirect::to('coupanList');	    		

		}else{
			tusi('验证码错误');
		}
    	
    }    	

    
}