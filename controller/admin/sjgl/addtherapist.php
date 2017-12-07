<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');

$m = new Model('lgc_supplier_user');
$major = new Model('z02_major');
$majorInfo = $major->where(array('status'=>'0'))->map_array('id','name');
$dengji = array('1'=>'注册商户','2'=>'一级商户','3'=>'二级商户','4'=>'三级商户','5'=>'四级商户','6'=>'五级商户','7'=>'六级商户','8'=>'七级商户','9'=>'八级商户','10'=>'九级商户');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
}
$sex = array('1'=>'男','0'=>'女');

$belongs_dls = new Model('distribution');

$dailiarray = $belongs_dls->where(array('status'=>1))->map_array('id','name');
$dailiarray[''] = '--请选择--';

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
	$db = DB::get_db();
	if($_POST["ins"] && $_POST["ins"] == "insert"){
		$sql = "select id from lgc_supplier_user where uns='".$m->uns."'";
		$rs = $db->query($sql);
		$id = $rs[0]['id'];
		if($id){
			tusi("用户名重复");
		}else{
			$m->wid = $wid;
			$m->is_lls = 1;
			$m->create_time = time();
			$m->create_time = time();
			$m->shopname = $m->lxr;
			$m->save();
			Redirect::to('therapist');
		}
	}else{
		$m->wid = $wid;
		$m->is_lls = 1;
		$m->create_time = time();
		$m->gender = $_POST['gender'];
		$m->shopname = $m->lxr;
		$m->save();
		Redirect::to('therapist');
	}

}
