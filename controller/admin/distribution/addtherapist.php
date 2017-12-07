<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$unid = Session::get('unid'); //代理商id


$banquanm = new Model('banquan');
$banquanm->find(1);

$m = new Model('lgc_supplier_user');
$major = new Model('z02_major');
$majorInfo = $major->where(array('status'=>'0'))->map_array('id','name');
$dengji = array('1'=>'注册商户','2'=>'一级商户','3'=>'二级商户','4'=>'三级商户','5'=>'四级商户','6'=>'五级商户','7'=>'六级商户','8'=>'七级商户','9'=>'八级商户','10'=>'九级商户');
if(Request::get(1)){
	$id = Request::get(1);
	$where_arr = array(
		'id' => $id,
		'belongs_id' => $unid,
	);
	$m->find($where_arr);
	if($m->wid != $wid){
		die();
	}
}
$sex = array('1'=>'男','0'=>'女');
$status = array('0'=>'未审核','1'=>'审核通过','2'=>'禁用');
if($m->try_post()){
	/*
	$mm = new Model('member');
	$mm->find(array('glshopid'=>$m->id));
	if($mm->has_id())
	{
	   $mm->uname = $m->uns;
	   $mm->pwd = $m->passwords;
	   $mm->telephone = $m->lxrtel;
	   $mm->email = $m->email;
	   $mm->glshopid = $m->id;
	   $mm->l_sheng = $m->location_p;
	   $mm->l_shi = $m->location_c;
	   $mm->l_xianqu = $m->location_a;
	   $mm->rtime = date('Y-m-d H:i:s',time());
	   $mm->save();
	}
	else
	{
	   $mm->uname = $m->uns;
	   $mm->pwd = $m->passwords;
	   $mm->telephone = $m->lxrtel;
	   $mm->email = $m->email;
	   $mm->glshopid = $m->id;
	   $mm->l_sheng = $m->location_p;
	   $mm->l_shi = $m->location_c;
	   $mm->l_xianqu = $m->location_a;
	   $mm->rtime = date('Y-m-d H:i:s',time());
	   $mm->save();
	}
	*/
    
	
	$m->wid = $wid;
	$m->status = 0;
	$m->belongs_id = $unid;
	$m->is_lls = 1;
	$m->create_time = time();
	$m->save();
	
	Redirect::to('therapist');
}
