<?php
access_control();
$wid = Session::get('wid');


$m = new Model('z02_sproduct');
if('del'==Request::get(1))
{
	$id = Request::post('id');
	$m->find($id);
	if($m->wid != $wid ){
		die();
	}else{
		$m->remove();
	}
	
	$m->update(array('id'=>$id),array('status'=>'1'));
}
else
{
	
	$where = array('wid'=>$wid,'is_ll'=>'1');

	$amount = $m->where($where)->count();
	
	if('w'==Request::get(1))
	{
	   $where['sh_status'] = 0;
	}
	
	$title = trim($_GET['title']);
	if($title!='')
	{
		$where['name@~'] = $title;
	}
	
	
	$p = new Pagination();
	$res = $p->model_list($m->where($where)->order('id desc'));
	
	
	$where['sh_status']= 0;
	$no_amount = $m->where($where)->count();
	
	$commm = new Model('z02_comm');
	
	
	$shoptype = new Model('z02_shop_list');
	foreach ($res as $v)
	{
	   $shoptype->find(array('supplierid'=>$v->unid));
	   $v->shopname = $shoptype->shopname;
	   $cc = $commm->where(array('wid'=>$wid,'pid'=>$v->id))->count();
	   $v->cc = $cc;
	}
}


$zx = array('1'=>'最新');
$tj = array('1'=>'特价');
$rm = array('1'=>'热卖');
$tuij = array('1'=>'推荐 ');
$wptj = array('1'=>'旺铺推荐');
$zgtj = array('1'=>'掌柜推荐');

