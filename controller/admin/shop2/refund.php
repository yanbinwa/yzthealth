<?php
access_control();
$wid = Session::get('wid');
$orderid =intval($_POST['oid']);
$type =intval($_POST['type']);


$return['success']=false;
if (is_numeric($wid) && is_numeric($orderid)) {
	$order = new Model('z02_order');
	$order->find(array('wid'=>$wid,'id'=>$orderid));

	if(!$order->id){

		$return['msg']='订单不存在';
	}else{

		if($type == '1'){
			
			$payset = new Model('ai9me_pay_set');
			$payset->where(array('token'=>$wid,'pc_enabled'=>'1','pc_type'=>'wxpay'))->find();

			$pems = new Model('wx_pem');
			$pems->find(array('wid'=>$wid));


			$inputArr['appid'] = $payset->appId;
			$inputArr['mch_id'] = $payset->partner;			  //商户号
			$inputArr['nonce_str'] = createRandomStr(20);     //随机字符串
			$inputArr['op_user_id'] =  $payset->partner;	  //商户号
			$inputArr['out_trade_no'] = 'WXSC_'.$order->id;	  //订单编号
			$inputArr['out_refund_no'] = time().rand(1000,9999);	//退款编号
			$key = $payset->key;	  //api秘钥
			$sslcert = YYUC_FRAME_PATH.YYUC_PUB.'/pos/'.$pems->cert_pem;  //证书地址
			$sslkey = YYUC_FRAME_PATH.YYUC_PUB.'/pos/'.$pems->key_pem;	  
			$wxpay = new Wxpay();

			if ($order->use_balance) {
				if ($order->jg*100+$order->yf*100 > $order->balance_expense*100) {
					$inputArr['refund_fee'] = $order->jg*100+$order->yf*100-$order->balance_expense*100;		    //退款金额
					$inputArr['total_fee'] = $order->jg*100+$order->yf*100-$order->balance_expense*100;				//订单金额
					//会员卡余额
					$MemberID=checkMember($order->wid,$order->wxid);
					changeje($MemberID,$order->balance_expense,0,'',1);
					$order->save();
					$response = $wxpay->refund($inputArr,$sslcert,$sslkey,$key);
				}else{
					//会员卡余额
					$MemberID=checkMember($order->wid,$order->wxid);
					changeje($MemberID,$order->balance_expense,0,'',1);
					$order->save();
					$response['success'] = 1;
				}
				
			}else{
				$inputArr['refund_fee'] = $order->jg*100+$order->yf*100;		    //退款金额
				$inputArr['total_fee'] = $order->jg*100+$order->yf*100;			    //订单金额
				$response = $wxpay->refund($inputArr,$sslcert,$sslkey,$key);
			}
			
			if (!$response['success']) $return['msg']=$response['msg'];
			else{
				$order->id = $orderid;
				$order->status = 4;	//退款		
				$order->save();
				// $order->update($condition=array('id'=>$orderid),$data=array('status'=>4));
				
				$refund = new Model('wx_refund');
				$refund->wid = $wid;
				$refund->type = 'wxsc';
				$refund->orderid=$orderid;
				$refund->refund_fee = $order->jg;
				$refund->created_at = time();
				$refund->save();
				$return['msg']='成功';
				$return['success']=true;

			}
			echo json_encode($return);
			exit;
		}elseif($type == '2'){
			$order->id = $orderid;
			$order->status = 5;	//退款
			$order->save();
			// $order->update($condition=array('id'=>$orderid),$data=array('status'=>5));
			$return['success']=true;
			$return['msg']='操作成功';
			echo json_encode($return);
			exit;
		}
			
	}
}else{
	$return['msg']='参数错误';
	echo json_encode($return);
	exit;
}



function createRandomStr($length){ 
	$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//52个字符 
	$strlen = 52; 
	while($length > $strlen){ 
	$str .= $str; 
	$strlen += 52; 
	} 
	$str = str_shuffle($str); 
	return substr($str,0,$length); 
} 

