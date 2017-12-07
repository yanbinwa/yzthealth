<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	$pubs = new Model('pubs');
	$pubs->find($wid);
	$pid = Request::get(1,'0');
	//上方幻灯片
	$pro = new Model('z02_sproduct');
	$tjres = $pro->where(array('wid'=>$wid,'tuij'=>1,'pic2@<>'=>'','typgg'=>$pid,'status'=>0,'sh_status'=>1,'is_ll'=>0))->limit(6)->order('rand()')->list_all();
	//商品分类
	$fl = new Model('z02_stype');
	$fls = $fl->where(array('wid'=>$wid,'pid'=>$pid))->order('ord,id')->list_all();
	$fl->name = '商品分类';
	if($pid !='0'){
		$fl = new Model('z02_stype');
		$fl->find($pid);
	}
	$selectc = 1;
	//商品
	$pro = new Model('z02_sproduct');
	$tjs = $pro->where(array('wid'=>$wid,'typgg'=>$pid,'status'=>0,'sh_status'=>1,'is_ll'=>0))->order('sort desc,id desc')->list_all();
}else{
	die('请从微信进入');
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
function reverse($str){
  //if($str!=0){
  	$m=new Model('z02_stype');
    $m->where(array('id'=>$str))->find();
    if($str=='17009' || $m->pid=='17009'){
     	return true;
     }else{
    	return false;
    }
 	unset($m);
  //}
}

