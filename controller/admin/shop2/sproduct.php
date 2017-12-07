<?php
$uid = Session::get('uid');
$wid = Session::get('wid');
$m = new Model('z02_sproduct');
if('del'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->remove();
	}
	
	$m->update(array('id'=>$id),array('status'=>'1'));
}
elseif('down'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('status'=>'1'));
	}
}
elseif('up'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('status'=>'0'));
	}
}
else
{
	$typ = new Model('z02_stype');
	$typearr = $typ->where(array('wid'=>$wid))->order('ord desc')->map_array('id', 'name');

	$where = array('wid'=>$wid);
	
	$title = trim($_GET['title']);
	if($title!='')
	{
		$where['name@~'] = $title;
	}
	$p = new Pagination();
	$user = new Model('user');
	$user->find($uid);

	$res = $p->model_list($m->where($where)->order('id desc'));
	$commm = new Model('z02_comm');
	foreach ($res as $v)
	{
	   $cc = $commm->where(array('wid'=>$wid,'pid'=>$v->id))->count();
	   $v->cc = $cc;
	}
}


$zx = array('1'=>'最新');
$tj = array('1'=>'特价');
$rm = array('1'=>'热卖');
$tuij = array('1'=>'推荐 ');