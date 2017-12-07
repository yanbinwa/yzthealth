<?php
$wid = Session::get('wid');
$wxid = Session::get('wxid');

if($wid && $wxid){

	$userInfo = get_wx_info($wxid,$wid);
	$list = new Model('z02_order');
	$zfzt = array('0'=>'未支付','1'=>'已支付','2'=>'已完成','3'=>'申请退款','4'=>'已退款','5'=>'退款失败');

	if($wid == '6765368' || $wid == '6781364'){
		$new_status = new Model('wsc_shop2_status');
		$rsnew_status = $new_status->where(array('wid'=>$wid))->list_all_array();
		$wlzt = array('0'=>$rsnew_status[0]['name'],'1'=>$rsnew_status[1]['name'],'2'=>$rsnew_status[2]['name']);
	}else{
		$wlzt = array('0'=>'未发货','1'=>'已发货','2'=>'已签收');
	}

	$where['wid'] = $wid;
	$where['wxid'] = $wxid;
	$where['is_del'] = 0;
	$status = Request::get(1);

	if($status == '2'){
		$where['status'] = '0';
		$where['wlstatus'] = '0';
		$where['isover'] = '0';
		$is_pending = 1;
	}elseif($status =='3'){
		$where['status'] = '1';
		$where['wlstatus'] = '0';
		$where['isover'] = '0';
		$is_send = 1;
	}elseif($status == '4') {
		$where['wlstatus'] = '1';
		$where['isover'] = '0';
		$is_receipt = 1;
	}elseif($status=='5'){
		$where['status'] = array('1','2','3','4');
		$where['wlstatus'] = '1';
		$where['isover'] = '1';
		$is_isOver =1;
		$is_all = '';
	}else{
		$is_all = 1;
	}


	$fu=new Model('micro_membercard_member_list');
	$fu->find(array('wxid'=>$wxid,'wid'=> $wid));

	$fuck=$fu->member_level;

	$order = $list->where($where)->order('id desc,status,wlstatus')->list_all();

	$chinaares = new Model('chinaarea');
	$areamap = $chinaares->map_array('id', 'name');
	foreach ($order as $dl){
		//取出订单商品数据
		$glist = json_decode($dl->prolist);
		$dl->glist = $glist;
		$dz = new Model('micro_membercard_dz_list');
		$dz->find($dl->addid);
		$dl->dzres = $dz;
		$dl->shr = $dz->shr;
		$dl->phone = $dz->phone;
		foreach ($glist as $gl){
			$p = new Model('z02_sproduct');
			$p->find($gl->pid);
			if(!$p->has_id()){
				continue;
			}
			$gl->name = $p->name;
			$gl->one = $p->lowest;
			$gl->pt = $p->yj;
			$gl->tow = $p->er_price;
			$gl->thr = $p->san_price;
			$gl->kc = $p->store_nums;
			$gl->pic = $p->pic;
			$gl->yf = $p->yf;
			$gl->gglist = array();
			if($gl->gid != '0'){
				$rowtemp = json_decode($p->rowtemp);
				foreach ($rowtemp as $rt){
					if($rt->goods_no==$gl->gid){
						$gl->one = $rt->sell_price;
						$gl->pt = $rt->market_price;
						$gl->tow = $rt->cost_price;
						$gl->thr = $rt->san_price;
						$gl->kc = $rt->store_nums;

						$ssarr = $rt->spec_array;
						foreach ($ssarr as $ss){
							$sstss = explode('_', $ss);
							$gl->gglist[$prop_arr[$sstss[0]]] = $sstss[1];
						}
						break;
					}
				}
			}
		}
	}
// 	echo '<pre>';
// print_r($order);
// echo '</pre>';
	$model = new Model('z02_set');
	$model->find(array('wid'=>$wid));
	$selectf = 1;
	page::view('me');
}
else {
	Redirect::to('index.html?wid='.$wid);
}

?>