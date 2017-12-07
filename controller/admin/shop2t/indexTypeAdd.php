<?php
access_control();
$wid = Session::get('wid');

if(Session::has('unid'))
{
   Session::clear();
}


$m = new Model('z02_stype');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid){
		die();
	}
}
$stype = $m->where(array('wid'=>$wid,'unid'=>NULL,'pid'=>0))->list_all_array();
foreach ($stype as $k => $v) {
	$stypearr[$v['id']] = $v['name'];
}

if($m->try_post()){
	$stypeModel = new Model('z02_stype');
	$stypeModel->find(array('id'=>$m->id));
	if ($stypeModel->has_id()) {

		$stypeModel->is_show = 1;
		$stypeModel->i_pic = $m->i_pic;
		$stypeModel->sort = $m->sort;
		$stypeModel->save();
		tusi('保存成功');
		Redirect::delay_to('indexType.html',1);
	}

}