<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');

if (!empty($wid) && !empty($wxid)) {
	$wechatset = new Model('web_set');
	$wechatset->find(array('id'=>2));
	$orderId = Request::get(1);
	$lgc_supplier = new Model('lgc_supplier_user');
	$lgc_supplier->find(array('wxid'=>$wxid));
	if ($lgc_supplier->id) {
		$orders = new Model('orders');
		$orders->find(array('id'=>$orderId,'unid'=>$lgc_supplier->id));
		if ($orders->id) {
			if ($orders->pay_status == 1) {
				if ($orders->status == 0) {
					$order_status = '待消费';
				}else{
					$order_status = '已完成';
					$remark= $orders->remark_status == 1 ? '':' 未评论';
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
			$member = new Model('member');
			$member->find(array('wxid'=>$orders->wxid));
			if ($member->id) {
				$cusTel = $member->telephone;
			}
		}else{
			$orders->find(array('id'=>$orderId,'llshopid'=>$lgc_supplier->id));
			if ($orders->id) {
				$lxlls = 1;
				if ($orders->pay_status == 1) {
					if ($orders->status == 0) {
						$order_status = '待消费';
					}else{
						$order_status = '已完成';
						$remark= $orders->remark_status == 1 ? '':' 未评论';
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
				$member = new Model('member');
				$member->find(array('wxid'=>$orders->wxid));
				if ($member->id) {
					$cusTel = $member->telephone;
				}
			}else{
				die('非法操作');
			}
			
		}
	}
	
}else{
	die('请从微信进入');
}
	