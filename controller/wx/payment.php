<?php

$engineerId = trim(Request::get('engineerId')); 
$productId = Request::get('productId'); 

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$opid = Session::get('opid');
$wechatset = new Model('web_set');
$remind = 0;
$wechatset->find(array('id'=>2));
if (!empty($wid) && !empty($wxid)) {
	$web_set = new Model('web_set');
	$web_set->find(array('id'=>2));
	$location_c = Session::get('location_c');
	$chinaarea = new Model('chinaarea');
	$chinaarea->find(array('id'=>$location_c));
 	$fans = new Model('fans');
    $fans->find(array('wxid'=>$wxid));	

	$sproduct = new Model('z02_sproduct');
	$productId_arr = explode('-', $productId);
	foreach ($productId_arr as $key => $value) {
		$sproduct->where(array('id'=>$value,'is_ll'=>1,'sh_status'=>1))->find();
		$total += floatval($sproduct->lowest);
	}
	/*$spr = $sproduct->where(array('id'=>$productId,'is_ll'=>1,'sh_status'=>1))->find();*/
	//获取理疗店列表
    $supplier = new Model('lgc_supplier_user');
	$supplier->find(array('id'=>$engineerId));
    if($supplier->id && $supplier->is_lls){

		$supplierarray = $supplier->where(array('status'=>1,'is_lls'=>0,'location_c@~'=>$chinaarea->name))->limit('0,50')->list_all_array();
		if (!empty($supplierarray)) {
				foreach ($supplierarray as $k => $v) {
		        $distance = getDistance($fans->jd,$fans->wd,$v['jd'],$v['wd'],2);
		        $sup[$k]['title'] = $v['shopname'].' '.$distance.' km';
		        $sup[$k]['value'] = $v['id'];
		        
		    }
		    $supjson = json_encode($sup); 
		}else{
			$supjson = '[{"title":"该地区暂无此服务","value":""}]';
			$remind = 1;
		}
	    
	      
    }else{
    	$supjson = '[{"title":"该地区暂无此服务","value":""}]';
    }

    //获取当前用户信息
	$member = new Model('member');
	$member->where(array('wxid'=>$wxid))->find();
	//获取优惠券列表
	$youhuiquan = new Model('youhuiquan_list');
	$youhuiquanset = new Model('youhuiquan');
	$youhuiquan_arr = $youhuiquan->where(array('openid'=>$wxid,'state'=>0))->list_all_array();

	foreach ($youhuiquan_arr as $key => $value) {
		$youhuiquanset->find(array('id'=>$value['yid'],'state'=>0));
		$cha = $youhuiquanset->maxcon-$total;
		if ($youhuiquanset->id && $cha>0) {
			unset($youhuiquan_arr[$key]);
		}

	}
	$count = count($youhuiquan_arr);
	if ($count > 0) {
		$yhq = new Model('youhuiquan');
		$yhqed = new Model('youhuiquan');
		foreach ($youhuiquan_arr as $k => $v) {
			$yhq->where(array('id'=>$v['yid'],'state'=>0))->find();
			$youhuiquan_arr[$k]['title'] = '满'.$yhq->maxcon.'立减'.$yhq->redcon;

			$yhqed->where(array('id'=>$v['yid'],'endtime@<'=>date('Y-m-d H:i:s',time()),'state'=>0))->find();
			if($yhqed->id){
				unset($youhuiquan_arr[$k]);
			}
			$yhq_arr[$k]['title'] =  $youhuiquan_arr[$k]['title'];
			$yhq_arr[$k]['value'] =  $youhuiquan_arr[$k]['id'];
		}
		foreach ($yhq_arr as $key => $value) {
			$yhq_qxh[] = $value;
		}
		
	}
$yhqjson = json_encode($yhq_qxh);

if (!empty($_POST)) {
	if(preg_match("/^1[34578]\d{9}$/", $_POST['telephone'])){
		$engineerId = $_POST['engineerId']; 
		$productId = $_POST['productId'];  
		$productId_arr = explode('-', $productId);

		$sproduct = new Model('z02_sproduct');
		foreach ($productId_arr as $k => $v) {

			$sproduct->where(array('unid'=>$engineerId,'id'=>$v,'sh_status'=>1,'is_ll'=>1,'status'=>0))->find();

			if ($sproduct->id) {
				$orders = new Model('orders');
				$orders->proid = $sproduct->id;
				$orders->total = floatval($sproduct->lowest);
				$orders->created_at = date('Y-m-d H:i:s',time());
				$orders->unid = $engineerId;
				$orders->good_name = $sproduct->name;
				$orders->full_name = $_POST['full_name']; 
				$orders->telephone = $_POST['telephone']; 
				$orders->ll_time = $_POST['ll_time'];
				$orders->couponid = $_POST['couponid'];
				$orders->beizhu = $_POST['remarks'];
				$orders->llshopid = $_POST['llshopid'];
				$orders->fx_wxid = $opid;
				$orders->num = 1;
				$orders->wxid = $wxid;
				$orders->order_type = 1;
				$orders->save();
				if ($orders->id) {		
					$orders_str_arr[] = $orders->id;
				}
				
			
			}else{
				$z02_sproduct = new Model('z02_sproduct');
				$z02_sproduct->where(array('unid'=>null,'id'=>$v,'sh_status'=>1,'is_ll'=>1,'status'=>0))->find();
				if ($z02_sproduct->id) {
					$orders = new Model('orders');
					$orders->proid = $z02_sproduct->id;
					$orders->total = floatval($z02_sproduct->lowest);
					$orders->created_at = date('Y-m-d H:i:s',time());
					$orders->unid = $engineerId;
					$orders->good_name = $z02_sproduct->name;
					$orders->full_name = $_POST['full_name']; 
					$orders->telephone = $_POST['telephone']; 
					$orders->ll_time = $_POST['ll_time'];
					$orders->couponid = $_POST['couponid'];
					$orders->beizhu = $_POST['remarks'];
					$orders->llshopid = $_POST['llshopid'];
					$orders->fx_wxid = $opid;
					$orders->num = 1;
					$orders->wxid = $wxid;
					$orders->order_type = 1;
					$orders->save();
					if ($orders->id) {	
						$orders_str_arr[] = $orders->id;
					}
				
				}
			}
		}
		if (!empty($orders_str_arr)) {
			$orders_str = implode('-',$orders_str_arr);
			$result['status'] = 1;	
			$result['url'] = Conf::$http_path.'wxpay/topayv33.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid.'&orders_str='.$orders_str;			
		}else{
			$result['status'] = 3;
			$result['error'] = '数据异常,请重新下单';
		}
				
				
	}else{
		$result['status'] = 2;
		$result['error'] = '手机号格式有误';
		/*tusi("手机号格式有误");*/
	}
		response::json($result);
		die();
	}	
}	