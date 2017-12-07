<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wid')){
//	page::view('pro');
	$wid = Session::get('wid');

	$kefu = new Model('pubs');
	$kefu->find($wid);


	$signPackage = GetWxSignPackage($wid);


	$wxid = Session::get('wxid');
	$prid = Request::get(1);


	$fu=new Model('micro_membercard_member_list');
	$fu->find(array('wxid'=>$wxid,'wid'=>$wid));

	$fuck=$fu->member_level;

	if(Request::get(mm) == 1){
		$tuan = 1;
	}
	//上方幻灯片
	$p = new Model('z02_sproduct');

	$p->find(array('wid'=>$wid,'id'=>$prid));
	if (!$p->id) {
		Redirect::to('./index.html');
	}
	$pics = json_decode($p->pics);
	
	//配送地区
	$local = Request::local();

	if($local){
		$local = $local[1];
	}
	//获取客户端ip
	$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
	$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
	//获取ip所在地
	$userAddrArray =YYUC::local_ip($user_IP);

	$db = DB::get_db();
	$sql = "select * from chinaarea where name like '%".$userAddrArray[2]."%'";
	$areaInfo = $db->query($sql);
	
	//普通SQL
	$sql = "select pg.postages,pg.addone from postage_add_city as pac
			left join postage_group as pg on pac.group_id = pg.id
			where pac.city_id=".$areaInfo[0]['ord']." and pac.wid=".$wid;

	$postageInfo = $db->query($sql);

	if (isset($postageInfo[0]['postages']) && !empty($postageInfo[0]['postages'])) {
		$yf = $postageInfo[0]['postages'];
	}else{
		$m = new Model('z02_set');
		$m->find(array('wid'=>$wid));
		$yf = $m->defalut_postage;
	}
	
	$tagss = new Model('z02_stag');
	$tag_arr = $tagss->where(array('wid'=>$wid))->map_array('id', 'name');

	function wsc_gettags($p,$backall = false){
		global $tag_arr;
		if(trim($p->tags)!=''){
			$wid = Session::get('wid');
			$tarr = String::split($p->tags);
			$thearr = array();
			foreach ($tarr as $ak=>$av){
				$thearr[] = $tag_arr[$av];
			}
			if($backall){
				return $thearr;
			}else{
				return $thearr[0];
			}
	
		}else{
			return null;
		}
	}
	$tags = wsc_gettags($p,true);
	
	$ggres = array();
	$ggres2 = array();
	//解析商品规格
	$ggarr = $p->rowtemp;
	if(trim($ggarr)!=''){
		$ggarr = json_decode($ggarr);
		//取出所有规格数组
		$propss = new Model('z02_sprop');
		$prop_arr = $propss->where(array('wid'=>$wid))->map_array('id', 'name');
		$ggtmp = $ggarr[0];
		$ggtmp = $ggtmp->spec_array;
		foreach ($ggtmp as $k=>$v){
			$vk = explode('_', $v);
			$ggres[$vk[0]] = $prop_arr[$vk[0]];
		}
		//补充每个里的规格
		foreach ($ggarr as $kv=>$v1){
			$ggtmp = $v1->spec_array;
			foreach ($ggtmp as $k=>$v){
				$vk = explode('_', $v);
				if(!isset($ggres2[$vk[0]])){
					$ggres2[$vk[0]] = array();
				}
				$ggres2[$vk[0]][$vk[1]] = $vk[1];
			}
		}
	}
	$shareurl = Conf::$http_path.'dz_wsc2_new/pro-'.$prid.'.html?wid='.$wid;

	
	//评论列表
	$com = new Model('z02_comm');
	$com->where(array('wid'=>$wid,'pid'=>$prid,'is_show'=>'1'))->order('id desc');
	$cres = $com->list_all();
	
}else{

}

