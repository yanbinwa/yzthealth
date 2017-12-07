<?php
$m = new Model('user');

if(Request::get(1)){
	$res = $m->find(Request::get(1));
}
if($m->try_post()){

	//添加时验证该用户名是否已经存在
	if(!$m->id)
	{
		$un = $m->un;
		$check = $m->where(array('un'=>$un))->find();
		if($check->id)
		{
			$tips = "该用户名已经存在";
		}
		else
		{
			//向user表中插入记录
			$m->level_id = 5;
			$m->mend_time = date("Y-m-d H:i:s",strtotime("+1 year"));
			$m->rtime = date('Y-m-d H:i:s',time());
			$m->save();
			$userid = $m->id;
			
			//向分销商表user_sub中同步记录
			$ms = new Model('user_sub');
			$ms->userid= $userid;
			$ms->parentid = Session::get('uid');
			$ms->create_time = time();
			$ms->save();
			Redirect::to('subusers');
		}
	}
	else
	{
		//更新user表中记录
		$useradd = $m->save();
		Redirect::to('subusers');
	}
	
		
	
}