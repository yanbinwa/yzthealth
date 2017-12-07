<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}
if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid = Session::get('wxid');
	if('del'==Request::get(1)){
		$dd = new Model('z02_order');
		$dd->find(Request::post('id'));
		if($dd->wlstatus=='0' && $dd->status=='0' && $dd->pay_type!='0' && $dd->wid==$wid){
			$dd->remove();
			Response::write('ok');
		}
		Response::write('no');
	}
	elseif('ok'== Request::get(1))
	{
		$did = Request::post('id');
		$ddata = json_decode(Request::post('data'));
		$dd = new Model('z02_order');
		$dd->find($did);
		
			//消费送积分
			$db = DB::get_db();
			$sql = "update micro_membercard_member_list set integral=integral+" .$dd->songjifen. " where wxid='".$wxid."'";
			$db->query($sql);
	
		$dd->completion_time = DB::raw('now()');
		$dd->isover = '1';
		if($dd->pay_type =='0'){
			$dd->status = '1';
		}
		$dd->save();
		foreach ($ddata as $k=>$v){
			$comm = new Model('z02_comm');
			$comm->wid = $wid;
			$comm->wxid = $wxid;
			$comm->orderid = $did;
			$comm->pid = $v[0];
			$comm->con = $v[1];
			$comm->is_show = '0';
			$comm->create_time = DB::raw('now()');
			$comm->save();
		}
		
		Response::write('ok');
	}
	elseif ('drawback' == Request::get(1)) {
		$did = Request::post('id');
		
		$dd = new Model('z02_order');
		$dd->find($did);
		
		//消费送积分
		$db = DB::get_db();
		$sql = "update micro_membercard_member_list set integral=integral-" .$dd->songjifen. " where wxid='".$wxid."'";
		$db->query($sql);
	
		$dd->completion_time = DB::raw('now()');
		$dd->status = '3';
		$dd->save();
		
		Response::write('ok');
	}
}else{
	
}