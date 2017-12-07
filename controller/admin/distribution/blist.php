<?php
access_control();
$unid = Session::get('unid'); //代理商id
if (!$unid) {
	Redirect::to('/dls/login');
}
$where = " d.did = $unid ";
if(Request::post('title')){
	$title = Request::post('title');
	$where.= " and m.uname like '%".Request::post('title')."%'";
}

$status = array('0'=>'正常','2'=>'冻结');
$db = DB::get_db();
$sql = "select m.id,m.uname,m.telephone,m.true_name,m.status,m.rtime,m.is_freez from member_distribut as d ";
$sql.= "left join member as m on d.mid = m.id ";
$sql.= "where $where order by  m.id desc";
$p = new Pagination();
$orders = $p->sql_list($sql); 

$m = new Model('member_distribut');
$count = $m->where(array('did'=>$unid))->count();
