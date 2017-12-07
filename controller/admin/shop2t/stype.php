<?php
$typ = new Model('z02_stype');
$unid = Session::get('unid');
//判断是总部还是分销商
//$sub_return = is_sub_seller();
$wid = Session::get('wid');

if(Request::post('cz')){
	$cz = Request::post('cz');
	$pid = Request::post('pid');
	$id = Request::post('id');
	$id = Request::post('id');
	$name = Request::post('name');
	$img = Request::post('img');
	$cimg = Request::post('imgc');
	$ms = Request::post('ms');
	$fl1 = Request::post('fl1');
	$fl2 = Request::post('fl2');
	$fl3 = Request::post('fl3');
	
	if($cz=='a'){		
		$typ->pid = $pid;
		$typ->name = $name;
		$typ->img = $img;
		$typ->ms = $ms;
		$typ->fl1 = $fl1;
		$typ->fl2 = $fl2;
		$typ->fl3 = $fl3;
		$typ->unid = $unid;
		$typ->logimg = $cimg;
		$typ->wid = Session::get('wid');
		$typ->save();
	}elseif($cz=='u'){
		$typ->find($id);
		if($typ->wid == Session::get('wid')){
			$typ->id = $id;
			$typ->pid = $pid;
			$typ->name = $name;
			$typ->img = $img;
			$typ->ms = $ms;
			$typ->fl1 = $fl1;
			$typ->fl2 = $fl2;
			$typ->fl3 = $fl3;
		    $typ->unid = $unid;
			$typ->logimg = $cimg;
			$typ->wid = Session::get('wid');
			$typ->save();
		}		
	}elseif($cz=='d'){
		$typ->delete(array('id'=>$id,'wid'=>Session::get('wid')));
		Response::write('ok');
	}
	Response::write($typ->id);
}elseif(Request::post('ld')){	
	$id = Request::post('id');
	//$res = $typ->where(array('pid'=>$id,'wid'=>$sub_return['condition']))->order('id')->list_all_array();
	$res = $typ->where(array('pid'=>$id,'wid'=>Session::get('wid')))->order('id')->list_all_array();
	Response::json($res);
}else{

	//$res = $typ->where(array('pid'=>'0','wid'=>$sub_return['condition']))->list_all();
	$res = $typ->where(array('pid'=>'0','wid'=>Session::get('wid'),'unid'=>$unid))->list_all();
}

//保存
if('st'==Request::get(1))
{
    $poststr = trim($_POST['poststr']);
  
    if(!empty($poststr))
    {
        $poststrarr = explode(',',$poststr);
        foreach($poststrarr as $v)
        {   
             $childarr = explode('|',$v);
             $u = $typ->update(array('id'=>$childarr[0]),array('img'=>$childarr[1],'logimg'=>$childarr[2]));
        }
    }
    if($u)
    {
        $msg = '保存成功';
    }
    else 
    $msg = '保存失败';
    exit();
}


//读取一级类别资料