<?php
$uid = Session::get('uid');
$wid = Session::get('wid');
$typ = new Model('z02_stype');
if(Request::post('cz')){
	$cz = Request::post('cz');
	$pid = Request::post('pid');
	$id = Request::post('id');
	$id = Request::post('id');
	$name = Request::post('name');
	$img = Request::post('img');
	$cimg = Request::post('imgc');
	$ms = Request::post('ms');
	if($cz=='a'){		
		$typ->pid = $pid;
		$typ->name = $name;
		$typ->img = $img;
		$typ->ms = $ms;
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
	$res = $typ->where(array('pid'=>$id,'wid'=>Session::get('wid')))->order('id')->list_all_array();
	Response::json($res);
}else{
	$wid =Session::get('wid');
 	if($wid=='6763163'){
		$res = $typ->where(array('pid'=>'0','wid'=>Session::get('wid')))->order("id desc")->list_all();
	}elseif($wid=='6771186'){
		$res = $typ->where(array('pid'=>'0','wid'=>$wid,'uid'=>$uid))->list_all();
	}else{
		$res = $typ->where(array('pid'=>'0','wid'=>Session::get('wid')))->list_all();
	}
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
             if ($wid==6771186) {
             	$u = $typ->update(array('id'=>$childarr[0]),array('img'=>$childarr[1],'logimg'=>$childarr[2],'uid'=>$uid));
             }else{
             	$u = $typ->update(array('id'=>$childarr[0]),array('img'=>$childarr[1],'logimg'=>$childarr[2]));
             }
             
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