<?php

log::warn('wxidaaaaa:'.Request::get(2));
log::warn('widaaaaa:'.Request::get(1));

if(Request::get('wxid') || Request::get(2)){
    if(Request::get(2)!='')
	Session::set('wxid',Request::get(2));
	else
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid') || Request::get(1)){

    if(Request::get(1)!='')
	Session::set('wid',Request::get(1));
	else
	Session::set('wid',Request::get('wid'));
}


if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	
	
	
	$propss = new Model('z02_sprop');
	$prop_arr = $propss->where(array('wid'=>$wid))->map_array('id', 'name');

	$zfzt = array('0'=>'未支付','1'=>'已支付','2'=>'已完成','3'=>'退款');
	$wlzt = array('0'=>'未发货','1'=>'已发货','2'=>'已签收');
	//取出所有订单
	$dd = new Model('z02_order');
	$dlist = $dd->where(array('wid'=>$wid,'wxid'=>$wxid,'is_del'=>'0'))->order('id desc,status,wlstatus')->list_all();

	$chinaares = new Model('chinaarea');
	$areamap = $chinaares->map_array('id', 'name');
	foreach ($dlist as $dl){
		//取出订单商品数据
		$glist = json_decode($dl->prolist);
		$dl->glist = $glist;
		$dz = new Model('micro_membercard_dz_list');
		$dz->find($dl->addid);
		$dl->dzres = $dz;
		foreach ($glist as $gl){
			$p = new Model('z02_sproduct');
			$p->find($gl->pid);
			if(!$p->has_id()){
				continue;
			}
			$gl->name = $p->name;
			$gl->jg = $p->lowest;
			$gl->yj = $p->yj;
			$gl->kc = $p->store_nums;
			$gl->pic = $p->pic;
			$gl->yf = $p->yf;
			$gl->gglist = array();
			if($gl->gid != '0'){
				$rowtemp = json_decode($p->rowtemp);
				foreach ($rowtemp as $rt){
					if($rt->goods_no==$gl->gid){
						$gl->jg = $rt->sell_price;
						$gl->yj = $rt->market_price;
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
}else{

}

