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
	$prid = Request::get(1);
	if(Request::get(mm) == 1){
		$tuan = 1;
	}
	
	//上方幻灯片
	$p = new Model('z02_sproduct');
	$p->find($prid);
	//判断该产品团购是否开始
	$kaishi = strtotime($p->tkssj);
	if($kaishi > time()){
		echo '团购还没开始，请等待！';
		exit;
	}	
	
	$ggres = array();

	//解析商品规格
	$ggarr = $p->rowtemp;
	if(trim($ggarr)!=''){
		$ggarr = json_decode($ggarr);
	    $count = count($ggarr);
		//取出所有规格数组
		$propss = new Model('z02_sprop');
		$prop_arr = $propss->where(array('wid'=>$wid))->map_array('id', 'name');
		if($count == 1){
			$ggres = $ggarr;
		}else{
			foreach ($ggarr as $kv=>$v1){
				$ggtmp = $v1->spec_array;
				foreach ($ggtmp as $k=>$v){
					$vk = explode('_', $v);
					$ggres[$kv] .= $vk[1];
				}
			}
		}
	}
	
}else{
	
}

