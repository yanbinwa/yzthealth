<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$wechatset = new Model('web_set');
$wechatset->find(array('id'=>2));
if (!empty($wid) && !empty($wxid)) {
	$orderId = Request::get(1);
	$orders = new Model('orders');
	$orders->find(array('id'=>$orderId,'wxid'=>$wxid));
	if ($orders->id) {
		if ($orders->pay_status == 1) {
			if ($orders->status == 0) {
				$order_status = '待消费';
			}else{
				$order_status = '已完成';
				$remark= $orders->remark_status == 1 ? '':' <a href="remark-'.$orderId.'.html">待评论</a>';
			}
			
		}else{
			$order_status = '未支付';

		}
		if ($orders->couponid) {
			$youhuiquanlist = new Model('youhuiquan_list');
			$youhuiquanlist->find(array('id'=>$orders->couponid));
			if ($youhuiquanlist->id) {
				$youhuiquan = new Model('youhuiquan');
				$youhuiquan->find(array('id'=>$youhuiquanlist->yid));
				$yhq = '满'.$youhuiquan->maxcon.'即送'.$youhuiquan->redcon;
			}
		}else{
			$yhq = '无';
		}
		$supplier = new Model('lgc_supplier_user');
		if ($orders->llshopid) {
			$supplier->find(array('id'=>$orders->llshopid));
		}else{
			$supplier->find(array('id'=>$orders->unid));
		}
		$shopSupplier = new Model('lgc_supplier_user');
		$shopSupplier->find(array('id'=>$orders->unid));
		
		$sproduct = new Model('z02_sproduct');
		$sproduct->find(array('id'=>$orders->proid));
		$lltime = $orders->num*$sproduct->lltime;

		$totall = $sproduct->lowest*$orders->num;

	}
	if($_POST['action']=='trueOrder'){
		$trueId = $_POST['trueId'];
		$orderstrue = new Model('orders');
		$orderstrue->find(array('id'=>$trueId,'wxid'=>$wxid));
		if ($orderstrue->id) {
			$amount = 0;
			if (!empty($orderstrue->fx_wxid)) {
				$brokerage = new Model('brokerage_record');
				$member = new Model('member');
				$pay_set = new Model('ai9me_pay_set');
				$pay_set->find(array('id'=>1));
				$second_rate = $orderstrue->total*$pay_set->second_rate/100; //总分销费用为使用优惠券之前费用
				$first_rate = $orderstrue->total*$pay_set->first_rate/100;//总分销费用为使用优惠券之前费用
				
				$member->find(array('wxid'=>$orderstrue->fx_wxid,'wid'=>$wid,'status'=>2));
				if ($member->id && !empty($member->fx_wxid)) {

					$memberfirst = new Model('member');
					$memberfirst->find(array('wxid'=>$member->fx_wxid,'wid'=>$wid,'status'=>2));
					if ($memberfirst->id) {
						$amount = $second_rate+$first_rate;
						$member->total += $second_rate; 
						$member->save();
						$brokerage->brokerage = $second_rate;
						$brokerage->member_id = $member->id;
						$brokerage->order_id = $orderstrue->id;
						$brokerage->wid = $wid;
						$brokerage->create_time = date('Y-m-d H:i:s',time());
						$brokerage->save();						

						$memberfirst->total += $first_rate; 
						$memberfirst->save();
						$record = new Model('brokerage_record');
						$record->brokerage = $first_rate;
						$record->member_id = $memberfirst->id;
						$record->order_id = $orderstrue->id;
						$record->wid = $wid;
						$record->create_time = date('Y-m-d H:i:s',time());
						$record->save();					
					}else{
						$amount = $first_rate;
						$member->total += $first_rate; 
						$member->save();
						$brokerage->brokerage = $first_rate;
						$brokerage->member_id = $member->id;
						$brokerage->order_id = $orderstrue->id;
						$brokerage->wid = $wid;
						$brokerage->create_time = date('Y-m-d H:i:s',time());
						$brokerage->save();						

					}

				}elseif ($member->id && empty($member->fx_wxid)) {
					$amount = $first_rate;
					$member->total += $first_rate; 
					$member->save();
					$brokerage->brokerage = $first_rate;
					$brokerage->member_id = $member->id;
					$brokerage->order_id = $orderstrue->id;
					$brokerage->wid = $wid;
					$brokerage->create_time = date('Y-m-d H:i:s',time());
					$brokerage->save();				
				}				
			}

			$llsupplier = new Model('lgc_supplier_user');
			$llsupplier->find(array('id'=>$orderstrue->llshopid,'status'=>1));
			if($llsupplier->id){
				$dptc = $orderstrue->total*$pay_set->dptc/100;
				$amount += $dptc;
				$llsupplier->total+= $dptc;
				$llsupplier->save();
				$supplier_record = new Model('brokerage_record');
				$supplier_record->brokerage = $dptc;
				$supplier_record->unid = $llsupplier->id;
				$supplier_record->order_id = $orderstrue->id;
				$supplier_record->wid = $wid;
				$supplier_record->create_time = date('Y-m-d H:i:s',time());
				$supplier_record->save();
			}

			$orderstrue->status = 1;
			$orderstrue->amount = $orderstrue->total-$amount;
			$orderstrue->end_time = date('Y-m-d H:i:s',time());
			$orderstrue->save();

			$llsupplier->find(array('id'=>$orderstrue->unid,'status'=>1));
			if ($llsupplier->has_id()) {
				$llsupplier->total+= $orderstrue->amount;
				$llsupplier->save();
			}			


		}
		return true;

	}
}else{
	die('请从微信进入');
}
	