<?php
if('del'==Request::get(1))
{
	$ids = explode(',', Request::post('id'));
	
	$ms = new Model('user_sub');
	$pubs = new Model('pubs');
	foreach ($ids as $id){
		$m = new Model('user');
		$m->find($id);
		//if($m->wid==Session::get('wid')){
			$m->remove();	
	        $ms->where(array('userid'=>$id))->find();
			$ms->remove();
			
			
			$pubs->where(array('uid'=>$id))->find();
			$pubs->remove();			
		//}
		
		
	}
	Response::write('ok');
}
else
{
	$parent_id = Session::get('uid');
	$m = new Model('user_sub');
	$res_m = $m->where(array('parentid'=>Session::get('uid')))->list_all();
	
	$id_arr = array();
	foreach ($res_m as $key=>$value)
	{
		$id_arr[] = $value->userid;
	}
	
	$db = DB::get_db();
	$sql ="select * from user where id in "."(".implode(',',$id_arr).") order by id desc";
	//$res  = $db->query($sql);
	
	$p = new Pagination();
	$res = $p->sql_list($sql,true);
	
}

