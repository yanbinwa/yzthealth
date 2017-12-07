<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$sta = 0;
	if('n'==Request::get(1))
	$sta = 0;
	if('y'==Request::get(1))
	$sta = 1;

	$m = new Model('micro_membercard_yhq_set_list');
	$rs = $m->where(array('wxid'=>$wxid,'tty'=>1,'status'=>$sta))->order('id desc')->list_all();
	$mm = new Model('micro_membercard_yhq_list');
	if ($wid == 6765368 ) {
		// 第一种
		// $db = DB::get_db();
		// $sql = "SELECT * FROM micro_membercard_yhq_set_list msl LEFT JOIN micro_membercard_yhq_list yl ";
		// $sql.= "ON msl.ttid = yl.id ";
		// $sql.="WHERE wxid='$wxid' AND tty=1 AND status=$sta";
		// $rs =$db->query($sql);
		// foreach ($rs as $key=>$v)
		// {
		// 	$bttime = explode('-', $v['bttime']);
		// 	$v->bttime = $bttime[1];
		// 	if (strtotime($bttime[1]) <= time() ) {
		// 		unset($rs[$key]);
		// 		continue;
		// 	}
		// }
		// 第二种
		$ttidArray = $m->where(array('wxid'=>$wxid,'tty'=>1,'status'=>$sta))->map_array('id','ttid');
		$mm = $mm->where(array('wid'=>$wid,'id'=>array_values($ttidArray)))->map_array_kmap('id', 'name,pic,bttime,des');
		foreach ($rs as $key => $value) {
			if(isset($mm[$value->ttid]) && !empty($mm[$value->ttid]) ){
				$rs[$key]->name = $mm[$value->ttid]['name'];
				$rs[$key]->pic = $mm[$value->ttid]['pic'];
				$rs[$key]->des = $mm[$value->ttid]['des'];
				$bttime = explode('-', $mm[$value->ttid]['bttime']);
				$rs[$key]->bttime = $bttime[1];
				if (strtotime($bttime[1]) <= time() ) {
					unset($rs[$key]);
					continue;
				}
			}
		}
	}
	else
	{
		$ttidArray = $m->where(array('wxid'=>$wxid,'tty'=>1,'status'=>$sta))->map_array('id','ttid');
		$mm = $mm->where(array('wid'=>$wid,'id'=>array_values($ttidArray)))->map_array_kmap('id', 'name,pic,bttime,des');
		if (!empty($rs)) {
			foreach ($rs as $key => $value) {
				if(isset($mm[$value->ttid]) && !empty($mm[$value->ttid]) ){
					$rs[$key]->name = $mm[$value->ttid]['name'];
					$rs[$key]->pic = $mm[$value->ttid]['pic'];
					$rs[$key]->des = $mm[$value->ttid]['des'];
					$bttime = explode('-', $mm[$value->ttid]['bttime']);
					$rs[$key]->bttime = $bttime[1];
					if (strtotime($bttime[1]) <= time() ) {
						unset($rs[$key]);
						continue;
					}
				}
			}
		}
	}
}else{
	die();
}

