<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$add = Request::get(2);

	$fu=new Model('micro_membercard_member_list');
	$fu->find(array('wxid'=>$wxid,'wid'=>$wid));
	$fuck=$fu->member_level;
	//判断把数据加到购物车
	if($add == 'add'){
		$gwca = new Model('z02_sgwc');
		$gwca->find(array('wid'=>$wid,'wxid'=>$wxid,'pid'=>Request::get(1),'gid'=>Request::get('gid')));

		if(!$gwca->id){
			$gwca->wid = $wid;
			$gwca->wxid = $wxid;
			$gwca->pid = Request::get(1);
			$gwca->gid = Request::get('gid');
			$gwca->ctime = DB::raw('now()');
			$gwca->num = Request::get('num');
			if(Request::get('mm') == 1){
				$gwca->istuan = 1;
			}
		}else{
			$gwca->num = Request::get('num') + $gwca->num;
		}
		$gwca->save();
		if ('ajax' == Request::get('ajax')) {
			$return['success']=true;
			echo json_encode($return);exit;
		}
		/*Redirect::to('gwc');*/
	}elseif('del'==Request::get(2)){
		$gwca = new Model('z02_sgwc');
		$gwca->delete(array('id'=>Request::get(1)));
		/*Redirect::to('gwc');*/
	}
//取出购物车的数据
	$gwc = new Model('z02_sgwc');
	$glist = $gwc->where(array('wid'=>$wid,'wxid'=>$wxid))->list_all();
	$propss = new Model('z02_sprop');
	$prop_arr = $propss->where(array('wid'=>$wid))->map_array('id', 'name');
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
	$prid = Request::get(1);
	//上方幻灯片
	$p = new Model('z02_sproduct');
	$p->find($prid);
	//列出所有收获地址
	$dzlist = new Model('micro_membercard_dz_list');
	$dzres = $dzlist->where(array('wid'=>$wid,'wxid'=>$wxid))->order('ismr desc,id desc')->list_all();

	$chinaares = new Model('chinaarea');
	$areamap = $chinaares->map_array('id', 'name');
	$selectg = 1;
}else{
	die('请从微信进入');
}
