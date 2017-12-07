<?php
access_control();
$uid = Session::get('uid');
$wid = Session::get('wid');
$unid = Session::get('unid');

$m = new Model('lgc_supplier_user');
$m->find(array('id'=>$unid));
$major = new Model('z02_major');
$majorInfo = $major->where(array('status'=>'0'))->map_array('id','name');

if($m->try_post()){
	$m->load_from_post();
	$m->save();
	tusi('保存成功');

}

if ($m->is_lls == 1) {
	Page::view('mytherapist');
}
