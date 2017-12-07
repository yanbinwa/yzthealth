<?php
$wid= Session::get('wid');
$m = new Model('drsmscode_6768392');




if('del'==Request::get(1)){
    $id = Request::post('id');
	$m->find($id);
	$m->remove();
}

if('qk'==Request::get(1)){
    $m->delete(" 1=1");
    die('0');
}


if('dr'==Request::get(1) && Request::post()){
	
	
	$snstype = Request::post('snstype'); // 充值卡类型
	
	$succ_result=0;
	$error_result=0;
	$file=$_FILES['filename'];
	$max_size="1048576"; //最大文件限制（单位：byte）
	$fname=$file['name'];
	$ftype=strtolower(substr(strrchr($fname,'.'),1));
	//文件格式
	$uploadfile=$file['tmp_name'];
	
	if($_SERVER['REQUEST_METHOD']=='POST'){
		if(is_uploaded_file($uploadfile))
		{
			if($file['size']>$max_size)
			{
				$alert = 'Import file is too large';
			}
			if($ftype!='csv')
			{
				$alert = 'Import file type is error';
			}
		}
		else
		{
			$alert = 'The file is not empty';
		}
	}
	
	if(empty($alert))
	{
	    $data1 = array();
		$file = fopen($uploadfile,'r');
		while ($data = fgetcsv($file)) {
			$num = count($data);
			for ($c=0; $c < $num; $c++) {
				$data1[] = $data[$c];
			}
		}
		fclose($uploadfile);
		$k=0;
		$om = new Model('drsmscode_6768392');
        foreach($data1 as $v)
		{
		    unset($om->id);
			$om->openid = $v;
			$om->smstype = $snstype;
			if($om->save())
			{
			   $k++;
			}
		}
		
		$alert ="成功导入:".$k."条";
	
   	}
}

if('0'!=intval(Request::get(1)) && Request::get(1)!='')
{
   $w = array('smstype'=>intval(Request::get(1)));
}


$p = new Pagination();
$rs = $p->model_list($m->where($w)->order('id desc'));

$allcount = $m->where()->count();

$status = array('0'=>'未使用','1'=>'已使用');

$ccarray =array();
$codem = new Model('drsmscode_6768392');
foreach($llarray as $k=>$v)
{
     $cc = $codem->where(array('status'=>0,'smstype'=>$k))->count();
	 $ccarray[$k]['cc'] = $cc;
}

