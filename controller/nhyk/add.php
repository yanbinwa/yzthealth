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
	
	$m = new Model('micro_membercard_dz_list');
	if('old'==Request::get(1))
	{
		$mm = new Model('micro_membercard_member_info');
		$mm->find(array('wxid'=>$wxid));

		$m->s_addr = $mm->s_addr;
		$m->s_p = $mm->s_p;
		$m->s_s = $mm->s_s;
		$m->s_x = $mm->s_x;
	}
	else
	{
		$m->find(Request::get(2));
	}


	if($m->try_post())
	{
		$m->wid = $wid;
		$m->wxid = $wxid;
		$m->s_p = trim($_POST['province_id']);
		$m->s_s = trim($_POST['city_id']);
		$m->s_x = trim($_POST['zone_id']);
		$m->create_time = date('Y-m-d H:i:s',time());



		if($m->is_mr==1)
		{
			$rs = $m->where(array('wid'=>$wid,'wxid'=>$wxid,'is_mr'=>1))->list_all();
			if(count($rs)!=0)
			{
				foreach ($rs as $v)
				{
					if($v->id==$m->id)
					{
						if($m->save())
						{
							$return = array('status'=>1,'msg'=>'保存成功');
						}
					}
					else
					{
						$return = array('status'=>0,'msg'=>'只能设置一个默认地址');
					}
				}
			}
			else
			{
				if($m->save())
				{
					$return = array('status'=>1,'msg'=>'保存成功');
				}
			}
		}
		else
		{
			if($m->save())
			{
				$return = array('status'=>1,'msg'=>'保存成功');
			}
		}

		echo json_encode($return);
		die;
	}


	$ismr = array('0'=>'不默认','1'=>'默认');

	$model = new Model('z02_set');
	$model->find(array('wid'=>$wid));
	$temp = $model->temp_type;



}else{
	die();
}
