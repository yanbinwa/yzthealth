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
	$fu=new Model('micro_membercard_member_list');
	$fu->find(array('wxid'=>$wxid,'wid'=>$wid));

	$fuck=$fu->member_level;



	$tuan = 0;
	if(Request::get(mm) == 1){
		$tuan = 1;
	}
	$zhi =0;
	if(Request::get('num') > 0){
		$zhi = 1;
	}
	
	/**************获取用户的积分信息，优惠券信息。*****************/
	$memberdetail = new Model("micro_membercard_member_list");
	$memberdetail->find(array("wxid"=>$wxid ,"wid"=>$wid));

	
		$lb = new Model('youhuijuan_list');
		$where = array('wid'=>$wid,'openid'=>$wxid,'state'=>'0');
		$rs = $lb->where($where)->order('receivetime desc')->list_all();
		foreach($rs as $k=>$v){
			$lbs = new Model('youhuijuan');
			$rs[$k]->info = $lbs->where(array('wid'=>$v->wid,'id'=>$v->yid))->find();

		}

	
		
	
	
	/**************获取店铺的基本设置****************/
	$shopset = new  Model("z02_set");
	$shopset->find(array("wid" => $wid));
	if($shopset->shangpinjifen==1)
	{
		$jifentag=1;
	}
	if($shopset->dianpujifen==1)
	{
		$jifentag=2;
	}

	//判断是否允许使用余额支付
    if($shopset->balance_permit == 1 && $memberdetail->id){
        $balance = $memberdetail->balance;
    }

	/******************* lzy 加入购物车 11-3*******************/
	if($_GET['piid']!=''){
		$sql  = "INSERT INTO `z02_sgwc`(`wid`, `wxid`, `pid`, `gid`, `num`, `ctime`, `istuan`) VALUES(
		'".$wid."','".$wxid."','".$_GET['piid']."','".$_GET['guid']."','".$_GET['num']."','".date('Y-m-d H:i:s')."','0' )";
		$que  = mysql_query($sql);
		//echo $sql;die;
		Redirect::to('tobuy');
	}
	/**************************************/
	//支付选择
	$payset = new Model('ai9me_pay_set');
	$payset->where(array('token'=>$wid,'pc_enabled'=>'1'));
	$alipay = false;
	$wxpay = false;
	$linepay = false;
	$payres = $payset->list_all();
	foreach ($payres as $pre){
		if($pre->pc_type=='alipay'){
			
			$alipay = true;
		}
		if($pre->pc_type=='wxpay'){
			$wxpay = true;
		}
		if($pre->pc_type=='linepay'){
			$linepay = true;
		}
		if($pre->pc_type=='tenpay'){
			$tenpay = true;
		}
	}
	$tedzid = '0';
	if('buy'==Request::get(2)){
		if(Request::get(nn) == 1){
			$gwca1 = new Model('z02_sgwc');
			$gwca1->delete(array('wxid'=>$wxid));
			//如果是团购的话，加入购物车
			$gwca1->find(array('wxid'=>$wxid));
			if(!$gwca1->has_id()){
				$data = json_decode(Request::post('alldata'));
				foreach($data->xm as $k=>$v){
					if($v->num > 0){
						$gwca1 = new Model('z02_sgwc');
						$gwca1->wid = $wid;
						$gwca1->wxid = $wxid;
						$gwca1->pid = Request::get(1);
						$gwca1->gid = $v->id;
						$gwca1->num = $v->num;
						$gwca1->ctime = DB::raw('now()');
						$gwca1->istuan = 1;
						$gwca1->save();
					}
				}
			}		
			
		}else{
			//商城的商品加入购物车部分
			$gwca = new Model('z02_sgwc');
			$gwca->find(array('wid'=>$wid,'wxid'=>$wxid,'pid'=>Request::get(1),'gid'=>Request::get('gid')));
			if(!$gwca->has_id()){
				$gwca->wid = $wid;
				$gwca->wxid = $wxid;
				$gwca->pid = Request::get(1);
				$gwca->gid = Request::get('gid');
				$gwca->ctime = DB::raw('now()');
				$gwca->num = Request::get('num');
				if(Request::get(mm) == 1){
					$gwca->istuan = 1;
				}
				$gwca->save();
			}
			$tedzid = $gwca->id;
		}
		
	}
	//取出购物车的数据
	$gwc = new Model('z02_sgwc');
	if($tedzid!='0'){
		$glist = $gwc->where(array('id'=>$tedzid))->list_all();
	}else{
		$glist = $gwc->where(array('wid'=>$wid,'wxid'=>$wxid))->list_all();
	}
	
	$propss = new Model('z02_sprop');
	$prop_arr = $propss->where(array('wid'=>$wid))->map_array('id', 'name');
	$zjff = 0;
	foreach ($glist as $gl){		
		$p = new Model('z02_sproduct');
		$p->find(array('id'=>$gl->pid,'wid'=>$wid));
		$zjff=$zjff+ $p->jifen;
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
		
		$gl->jifen = $p->jifen;
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
	$zjf = $zjff;
	$prid = Request::get(1);
	//上方幻灯片
	$p = new Model('z02_sproduct');
	$p->find(array('id'=>$prid,'wid'=>$wid));
	//列出所有收获地址
	$dzlist = new Model('micro_membercard_dz_list');
	$dzres = $dzlist->where(array('wid'=>$wid,'wxid'=>$wxid))->order('is_mr desc,id desc')->list_all();
	
	$chinaares = new Model('chinaarea');
	$areamap = $chinaares->map_array('id', 'name');

	if(empty($dzres)){
		//获取客户端ip
		$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
		$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
		// 获取ip所在地
		$userAddrArray =YYUC::local_ip($user_IP);
		$db = DB::get_db();
		$sql = "select * from chinaarea where name like '%".$userAddrArray[2]."%'";
		$areaInfo = $db->query($sql);
		$ord = $areaInfo[0]['ord'];
	}else{
		$ord = $dzres[0]->s_s;
	}

	//默认地址邮费
	$db = DB::get_db();
	//普通SQL
	$sql = "select * from postage_add_city as pac
			left join postage_group as pg on pac.group_id = pg.id
			where pac.city_id=".$ord." and pac.wid=".$wid." limit 1";
	$postageInfo = $db->query($sql);

	//以上都没有  采用默认邮费
	if(empty($postageInfo)){
		$m = new Model('z02_set');
		$m->find(array('wid'=>$wid));
		$postageInfo[0]['postages']=$m->default_postage;
		$postageInfo[0]['addone']=$m->addone;
	}

	////////////wid 11-1 推荐加入购物车产品 @ lzy////////
	
	//if($wid==147){

	//计算包邮价格
	$set = new Model('z02_set');
	$set->find(array('wid'=>$wid));
	
 	if ($wid==3702) {
		Page::view("tobuy4");
 	}
 	else 
 	{
 		Page::view("tobuy31");
 	}

		
		
}
else
{
	
}





function get_guigejos($z){
	$a = json_decode($z);
	return $a[0]->goods_no;
	
}

