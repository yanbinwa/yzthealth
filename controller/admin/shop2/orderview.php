<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('z02_order');
$pm = new Model('z02_sproduct');
$orid = Request::get(1);
if(Request::get(1) == 'ti'){
	//提交更改信息
	$or = new Model('z02_order');
	$id = Request::post('id');
	$bz = Request::post('bz');
	$or->update(array('id'=>$id),array('bz'=>$bz));
	echo 'ok';
	exit;
}

if(Request::get(1)){
	$pay_type = array('0'=>'货到付款','1'=>'支付宝','2'=>'财付通','3'=>'微支付','8'=>'银联支付');
	$pay_status = array('0'=>'未支付','1'=>'已支付','2'=>'已完成','3'=>'退款');
	$o_status   = array('0'=>'未发货','1'=>'已发货','2'=>'已收到');
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
	//[{"pid":"31","name":"\u5370\u5ea6\u9ebb\u56e2","gid":"9","num":1,"dj":"2.00"}]
	$prolist = json_decode($m->prolist,true);
	
	foreach ($prolist as $key=>$v)
	{
		$pm->find($v['pid']);
		$prolist[$key]['pic'] = $pm->pic;
		//产品规格 [{"goods_no":"YK0999-1","spec_array":["21_\u4e2d\u5305\u88c5"],"store_nums":"100","market_price":"2156","sell_price":"2300","cost_price":"2066","weight":"111"}]
		$gglist = json_decode($pm->rowtemp);
		if(count($gglist) > 0){
			foreach($gglist as $kk=>$gg){
				if($gg->goods_no == $v['gid']){
					if(count($gg->spec_array)>0){
						$ggres = new Model('z02_sprop');
						foreach($gg->spec_array as $k=>$gs){
							$gsarr = explode('_', $gs);
							$res = $ggres->find($gsarr[0]);
							$gg->spec_array[$k] = $res->name.':'.$gsarr[1];
						}
					}
					$prolist[$key]['gg'] = $gg->spec_array;
					break;
				}
			}
		}
	}
	$dzm = new Model('micro_membercard_dz_list');
	$dzm->find($m->addid);

	$addarr = new Model('chinaarea');
	$addres = $addarr->map_array('ord', 'name');
	$dzm->addr = $addres[$dzm->s_p].$addres[$dzm->s_s].$addres[$dzm->s_x].$dzm->s_addr;
	
	$om = new Model('openid');
	$om->where(array('wid'=>$wid,'wxid'=>$m->wxid))->order('id desc ')->limit(1)->list_all();
	
	
	$lm = new Model('drsmscode_senderro_log');
	$lrs = $lm->where(array('order'=>Request::get(1)))->list_all();

	//优惠券
	if($wid==6765368){
		$yhj = new Model('youhuijuan_list');
		$yhjs = new Model('youhuijuan');
		$where = array('id'=>$m->yhjid);
		$info = $yhj->where($where)->find();
		$info->info = $yhjs->where(array('id'=>$info->yid))->find();
	}
		
	
}



