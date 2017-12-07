<?php
access_control();
$unid = Session::get('unid');
$wid = Session::get('wid');

$kw = trim($_POST['title']);

$where['is_lls'] = array('1');
if($kw!='')
{
	$where['or'] = array('lxrtel@~'=>$kw,'lxr@~'=>$kw);
}

$m = new Model('lgc_supplier_user');
if('del'==Request::get(1)){
	$id = Request::get(2);
	$m->find($id);
	$m->delete();
	
}
$add = new Model('chinaarea');
$addInfo = $add->map_array('id','name');

$linkurl = Conf::$http_path.'business/login.html';

//统计:已审核 待审核总数
$deal = new Model('lgc_supplier_user');
$is_deal = $deal->where(array('status'=>1,'is_lls'=>1))->count();

$no_deal = $deal->where(array('status'=>0,'is_lls'=>1))->count();


$p = new Pagination();
$r = $p->model_list($m->where($where)->order('id desc'));
$belongs_dlsname = new Model('distribution');
foreach ($r as $key => $value) {
	if($value->belongs_id){
		$belongs_dlsname->find($value->belongs_id);
		$value->belongs_dlsname = $belongs_dlsname->name;
	}else{
		$value->belongs_dlsname = "----";
	}
	
}

