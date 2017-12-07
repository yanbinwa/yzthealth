<?php
$unid = Session::get('unid');

$orders = new Model('orders');

/*if('del'==Request::get(1))
{
	$id = Request::post('id');
	$sta = Request::post('val');
	$m->find($id);
	if($m->wid != $wid){
		die();
	}else{
		$m->update(array('id'=>$id),array('is_show'=>$sta));
	}
}*/
$p = new Pagination();
$res = $p->model_list($orders->where(array('unid'=>$unid,'remark_status'=>1,'status'=>1,'pay_status'=>1,'proid'=>Request::get(1)))->order('id desc'));


