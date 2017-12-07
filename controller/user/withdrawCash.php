<?php 
if(Request::get('wxid')){
    Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
    Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')&&Session::has('wxid')){
	$mid = Request::get(1);
	$wxid = Session::get('wxid');
	$wid = Session::get('wid');
	$fans = new Model('fans');
    $fans->find(array('wxid'=>$wxid,'wid'=>$wid));	
	$orders = new Model('orders');
    $orders_arr_num = $orders->where(array('wxid'=>$wxid,'status'=>1,'pay_status'=>1))->count();
    $orders_array_num = $orders->where(array('wxid'=>$wxid,'status'=>0,'pay_status'=>1))->count();      
	$member = new Model('member');
    $member->where(array('wxid'=>$wxid,'wid'=>$wid,'status'=>2,'id'=>$mid))->find();	

    if ($member->has_id()) {

		if($_POST){
			$total = trim($_POST['total']);
			$validCode = trim($_POST['validCode']);
			$sendCode = new Model('member_send_code_log');
			$sendCode->where(array('tel'=>$member->telephone,'status'=>0))->order('id desc')->find();
			if($sendCode->has_id() && $sendCode->code == $validCode){
				$rawals = new Model('withdrawals_order');
				$wshtx = $rawals->where(array('mid'=>$member->id,'status'=>0))->sum(total);
				$tixian = floatval($member->total)-floatval($total)-floatval($wshtx);
				if ($tixian>0) {				
					$withdrawals = new Model('withdrawals_order');
					$withdrawals->mid = $member->id;
					$withdrawals->total = $total;
					$withdrawals->created_at = time();
					$withdrawals->card_num = $member->bank_card;
					$withdrawals->bank_id = $member->bank_type;
					$withdrawals->branch_name = $member->bank_name;
					$withdrawals->rename = $member->true_name;
					$withdrawals->id_card = $member->idcard;
					$withdrawals->save();
					if ($withdrawals->has_id()) {
						$sendCode->status = 1;
		    			$sendCode->save();	
		    			echo '<script type="text/javascript" >alert("申请已提交，等待审核");</script>';
	    				/*Redirect::to('coupanList');*/
		    		}else{
		    			echo '<script type="text/javascript" >alert("认证信息有误，请联系客服");</script>';
		    		}				
		    		
				}else{
					echo '<script type="text/javascript" >alert("可提现金额不足");</script>';
				}
				    		
			}else{
				echo '<script type="text/javascript" >alert("验证码错误");</script>';
			}
		    	
		}      	

    }else{
    	die('非法操作');
    }

}else{
	die('非法操作');
}
