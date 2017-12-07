<?php

$wid = Session::get('wid');
$unid = Session::get('unid');


$m = new Model('z02_sprop');
if(Request::get(1)){
	$m->find(Request::get(1));
	if($m->wid != $wid ){
		die();
	}
}

if($m->try_post()){
	$m->wid = $wid;
	$m->unid = $unid;
     
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

