<?php
$wid = Session::get('wid');
$m = new Model('artice_list');

//信息公开目录  57
$ml = array(
  '1'=>'机构职能',
  '2'=>'法律法规及规范性文件',
  '3'=>'规划计划',
  '4'=>'土地管理',
  '5'=>'地质矿产管理',
  '6'=>'地质环境管理',
  '7'=>'综合管理'
);


//调研

$tkm = new Model('micro_survey_tk');
$tkmrs = $tkm->where()->map_array('id','question');


$id = intval(Request::get(1));
$cid = intval(Request::get(2));

//分类查询sdfsdf
$tm = new Model('cata_list');
$lbres = $tm->where(array('pid'=>'0'))->order('sorts desc')->list_all();
$lb_opts['0'] = '无父级分类';
get_select_subarr($lbres,'');

if($id!=0)
{
	$m->find($id);
}

if($m->try_post())
{
   	if($_FILES['files'])
	{
	   $m->files = upload($_FILES['files']);
	}
	$m->maketime = date('Y-m-d',time());
	$m->save();
	tusi('保存成功');
	Redirect::delay_to("newslist-c-".$m->cateid.".html",1);
}

if($cid!=0)
{
	$m->cateid = $cid;
}
	

function get_select_subarr($ll,$leftl)
{
    global $lb_opts;
	foreach ($ll as $l){
		$lb_opts[$l->id] = $leftl.$l->cataname;
		$tm = new Model('cata_list');
		$lbres = $tm->where(array('pid'=>$l->id))->order('sorts desc')->list_all();
		get_select_subarr($lbres,$leftl.'　|-');
	}
}

  /* 允许上传的文件类型 */
function upload($file)
{
    $allow_file_types = '|swf|doc|docx|csv|zip|rar|txt|pdf|chm|pem|';
	/* 判断用户是否选择了文件 */
	if ((isset($file['error']) && $file['error'] == 0) || (!isset($file['error']) && $file['tmp_name'] != 'none'))
	{
		/* 检查上传的文件类型是否合法 */
		$filename=explode(".",$file['name']);//把上传的文件名以“.”好为准做一个数组。  
		if (strpos($allow_file_types,$filename[1]) === false)
		{
			die('上传类型不正确');
		}
		else
		{
		   
			//$filename=explode(".",$file['name']);//把上传的文件名以“.”好为准做一个数组。  
			//$wid = Session::get('wid');
			//$name=$filename[0].md5($wid);//取文件名t替换  
			//$name='.'.$filename[1];
			

            $name=iconv("UTF-8","gb2312", $file['name']);
			$file_name =YYUC_FRAME_PATH.YYUC_PUB.'/files/'.$name;
			/* 判断是否上传成功 */
			if (move_uploaded_file($file['tmp_name'], $file_name))
			{
				return $file['name'];
			}
			else
			{
				die('存储失败');
				//sys_msg(sprintf($_LANG['msg_upload_failed'], $file['name'], $file_var_list[$code]['store_dir']));
			}
		}
	}
	
}