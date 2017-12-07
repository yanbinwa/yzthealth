<?php
$db = DB::get_db();
$wid = Session::get('wid');
$m = new Model('z02_sprop');
if('del'==Request::get(1)){
	$id = Request::post('id');
	if(is_array($id))
	{
		$id_str = '';
		foreach($id as $rs)
		{
			if($id_str!='')
			{
				$id_str.=',';
			}
				$id_str.=$rs;
		} 
		
		if('recycle'==Request::get('type')){
			//进入回收站
			$sql = 'UPDATE z02_sprop SET is_del = "1" WHERE wid ="'.$wid.'" and id in ('.$id_str.')';
			$db->query($sql);
			exit('成功移入回收站！');
		}else{
			//彻底删除
			$sql = 'DELETE FROM z02_sprop WHERE wid ="'.$wid.'" and id in ('.$id_str.')';
			$db->query($sql);
			exit('删除成功！');
		}
		
	}else{
		

		$m->find($id);
		if($m->wid != Session::get('wid')){
			exit('无法删除！');
		}else{
			//进入回收站
			if('recycle'==Request::get('type')){
				$m->is_del =1;
				$m->save();
				exit('成功移入回收站！'); 
			//彻底删除
			}else{
				$m->delete();
				exit('删除成功！');
			}
		}
	}

}elseif('restore'==Request::get(1)){
	$id = Request::post('id');
	if(is_array($id))
	{
		$id_str = '';
		foreach($id as $rs)
		{
			if($id_str!='')
			{
				$id_str.=',';
			}
				$id_str.=$rs;
		} 

		//数据恢复
		$sql = 'UPDATE z02_sprop SET is_del = "0" WHERE wid ="'.$wid.'" and id in ('.$id_str.')';
		$db->query($sql);
		exit('成功恢复！');

	}else{
		

		$m->find($id);
		if($m->wid != Session::get('wid')){
			exit('无法恢复！');
		}else{
				$m->is_del =0;
				$m->save();
				exit('成功恢复！'); 
		}
	}
// 查找对应属性值
}elseif('search'==Request::get(1)){
	$data = array();
	$goods_id = Request::get('id');
	$specId   = '';
	
	if($goods_id)
	{
		$tb_goods = new IModel('z02_sproduct');
		$tb_goods->find('id = '.$goods_id);
		$data['goodsSpec'] = json_decode($tb_goods->spec_array);
		if($data['goodsSpec'])
		{
			foreach($data['goodsSpec'] as $item)
			{
				$specId .= $item['id'].',';
			}
		}
	}
	
	if($specId)
	{
		$sql = "select * from z02_sprop where id in (".trim($specId,',').")";
		$data['specData'] = $db->query($sql);
	}
	exit('ddd');
}else{
	if('recycle'==Request::get(1)){
		$m->where(array('wid'=>Session::get('wid'),'is_del'=>1));
	}else{
		$m->where(array('wid'=>Session::get('wid'),'is_del'=>0));
	}
	$p = new Pagination();
	$res = $p->model_list($m);
}