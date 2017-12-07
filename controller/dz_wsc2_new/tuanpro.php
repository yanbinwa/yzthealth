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
	$kai = 1;
	$kaishi = strtotime($p->tkssj);
	if($kaishi > time()){
		$kai = 0;
	}
	$pics = json_decode($p->pics);
	
	//配送地区
	$local = Request::local();
	if($local){
		$local = $local[1];
	}
	
	$yf = $p->yf=='0'?'免邮费':$p->yf.'元';
	
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
	
	//剩余时间计算
	function time2string($jssj){
	$jssj = strtotime($jssj);
	$second = abs($jssj - time());
	$day = floor($second/(3600*24));
	$second = $second%(3600*24);//除去整天之后剩余的时间
	$hour = floor($second/3600);
	$second = $second%3600;//除去整小时之后剩余的时间 
	$minute = floor($second/60);
	$second = $second%60;//除去整分钟之后剩余的时间 
	//返回字符串
	return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
	}

	//评论列表
	$com = new Model('z02_comm');
	$com->where(array('wid'=>$wid,'pid'=>$prid,'is_show'=>'1'))->order('id desc');
	$cres = $com->list_all();
}else{
	
}

