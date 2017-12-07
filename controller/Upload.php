<?php
Conf::$session_prefix = 'sdzzbabcd';
if(Session::has('mid'))
{

	$mid = $_POST['mid'];
	$cid = $_POST['id'];

	$picname = $_FILES['mypic'];
	// list($type, $data) = explode(',', $picname); 
	$type = $picname['type'];	


	if(strstr($type,'image/jpeg')!==''){  
	    $ext = '.jpg';  
	}elseif(strstr($type,'image/gif')!==''){  
	    $ext = '.gif';  
	}elseif(strstr($type,'image/png')!==''){  
	    $ext = '.png';  
	}else{
		$ext = '.jpg';  
	}
	
	$name = 'sfzzp-'.$mid.'-'.$cid.$ext;

	//上传路径
	$path = "./website/idcard";

	$osspath = 'ups/axlg/idcard/'.$mid;

	$image_info = getimagesize($_FILES['mypic']['tmp_name']);
	$data = chunk_split(base64_encode(file_get_contents($_FILES['mypic']['tmp_name'])));
	$path = $path.'/'.$name;
	if (is_dir($path)) {
		unlink($path);
		file_put_contents($path, base64_decode($data), true); 
	}else{
		file_put_contents($path, base64_decode($data), true); 
	}

	$aloss = new ALIOSS();
	$aloss->upload_file_by_file($osspath.'/'.$name , $path);
	unlink($path);
	$url = Conf::$alioss_url.$osspath.'/'.$name.'?'.time();	//阿里云真实地址


// 
	$model = new Model('member');

	$model->find(array('id'=>$mid));
	if($cid == '1'){
		$model->file = $url;
	}elseif($cid =='2'){
		$model->file1 = $url;
	}elseif($cid =='3'){
		$model->file2 = $url;
	}
	
	$model->save();

	$arr['img'] = $url;
	echo json_encode($arr);die;
	
}
