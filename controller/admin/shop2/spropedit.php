<?php
$m = new Model('z02_sprop');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != Session::get('wid')){
		die();
	}
}

if($m->try_post()){
	$m->wid = Session::get('wid');

	//规格数据
	$valueArray = Request::post('value');

	//要插入的数据
	if(is_array($valueArray) && isset($valueArray[0]) && $valueArray[0]!='')
	{
		$valueArray = array_unique($valueArray);
		foreach($valueArray as $key => $rs)
		{
			if($rs=='')
			{
				unset($valueArray[$key]);
			}
		}

		if(!$valueArray)
		{
			$isPass = false;
			$errorMessage = "请上传规格图片";
		}
		else
		{
			$value = json_encode($valueArray);
		}
	}
	else
		$value = '';



	$m->value = $value;
	$m->type = Request::post('type');


	//校验
	$isPass = true;
	if($value=='')
	{
		$isPass = false;
		$errorMessage = '规格值不能为空,请填写规格值或上传规格图片';
	}

	if($m->name=='')
	{
		$isPass = false;
		$errorMessage = '规格名称不能为空';
	}

	if($isPass==false)
	{
		echo json_encode(array('flag' => 'fail','message' => $errorMessage));
		exit;
	}
	else
	{
		$m->save();
		//获取自动增加ID
		$editData['id'] = $m->id;
		echo json_encode(array('flag' => 'success','data' => $editData));
		exit();
	}
}

/*

//批量删除规格
function spec_del()
{
	$id = Request::post('id');
	if(!empty($id))
	{
		$obj = new IModel('spec');
		$obj->setData(array('is_del'=>1));
		$obj->update(Order_Class::getWhere($id));
		$this->redirect('spec_list');
	}
	else
	{
		$this->redirect('spec_list',false);
		Util::showMessage('请选择要删除的规格');
	}
}
//彻底批量删除规格
function spec_recycle_del()
{
	$id = Request::post('id');
	if(!empty($id))
	{
		$obj = new IModel('spec');
		$obj->del(Order_Class::getWhere($id));
		$this->redirect('spec_recycle_list');
	}
	else
	{
		$this->redirect('spec_recycle_list',false);
		Util::showMessage('请选择要删除的广告位');
	}
}
//批量还原规格
function spec_recycle_restore()
{
	$id = Request::post('id');
	if(!empty($id))
	{
		$obj = new IModel('spec');
		$obj->setData(array('is_del'=>0));
		$obj->update(Order_Class::getWhere($id));
		$this->redirect('spec_recycle_list');
	}
	else
	{
		$this->redirect('spec_recycle_list',false);
		Util::showMessage('请选择要还原的广告位');
	}
}
//规格图片删除
function spec_photo_del()
{
	$id = Request::post('id','post');
	if(isset($id[0]) && $id[0]!='')
	{
		$obj = new IModel('spec_photo');
		$id_str = '';
		foreach($id as $rs)
		{
			if($id_str!='')
			{
				$id_str.=',';
			}
				$id_str.=$rs;

			$photoRow = $obj->getObj('id = '.$rs,'address');
			if(file_exists($photoRow['address']))
			{
				unlink($photoRow['address']);
			}
		}

		$where = ' id in ('.$id_str.')';
		$obj->del($where);
		$this->redirect('spec_photo');
	}
	else
	{
		$this->redirect('spec_photo',false);
		Util::showMessage('请选择要删除的id值');
	}
}
*/