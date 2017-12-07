<?php
$wid = Session::get('wid');
$unid = Session::get('unid');

$m = new Model('z02_sproduct');
if('del'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid || $m->unid!=$unid ){
		die();
	}else{
		$m->remove();
	}
	$m->update(array('id'=>$id),array('status'=>'1'));
}
elseif('up'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid || $m->unid!=$unid){
		die();
	}else{
		$m->update(array('id'=>$id),array('status'=>'0'));
	}
}
else
{
	$where = array('wid'=>$wid,'unid'=>$unid,'is_ll'=>'1');
	
	$title = trim($_GET['title']);
	if($title!='')
	{
		$where['name@~'] = $title;
	}
	$p = new Pagination();
	$res = $p->model_list($m->where($where)->order('id desc'));
	$commm = new Model('z02_comm');
	foreach ($res as $v)
	{
	   $cc = $commm->where(array('wid'=>$wid,'pid'=>$v->id))->count();
	   $v->cc = $cc;
	}
	
	
    if(Session::has('pwid'))
	{
	   $typewhere = "(wid = $wid or wid = ".Session::get('pwid').")";
	}
	else
	$typewhere = "wid = $wid and unid=$unid";
	
	
	
	$typ = new Model('z02_stype');
	$typearr = $typ->where($typewhere)->order('ord desc')->map_array('id', 'name');
	
}


$zx = array('1'=>'最新');
$tj = array('1'=>'特价');
$rm = array('1'=>'热卖');
$tuij = array('1'=>'推荐 ');
$wptj = array('1'=>'旺铺推荐');
$zgtj = array('1'=>'掌柜推荐');