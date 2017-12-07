<?php
access_control();
$unid = Session::get('unid'); //代理商id
$kw = trim($_POST['title']);
$wheres['belongs_id'] = $unid;
$wheres['is_lls'] = 0;
if($kw!='')
{
	$wheres['shopname@~'] = $kw;
}
$m = new Model('lgc_supplier_user');

$add = new Model('chinaarea');
$addInfo = $add->map_array('id','name');

$linkurl = Conf::$http_path.'business/login.html';

//统计:已审核 待审核总数
$deal = new Model('lgc_supplier_user');
$is_deal = $deal->where(array('belongs_id'=>$unid,'status'=>1,'is_lls'=>0))->count();

$no_deal = $deal->where(array('belongs_id'=>$unid,'status'=>0,'is_lls'=>0))->count();

$p = new Pagination();
$r = $p->model_list($m->where($wheres)->order('id desc'));

