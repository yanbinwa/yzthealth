<?php
access_control();
$db  = DB::get_db();
$menulist = new Model('member');

$kw = trim(Request::get('title'));
$getcount = $menulist->where($where)->count();
//gettoday
$now = date("Y-m-d",time());
$startime = "$now 0:0:0";
$endtime = "$now 23:59:59";
$todaysql = "select id from member where rtime between '$startime' and '$endtime'";
// echo $todaysql;
$todaycount = $db->query($todaysql);
$tcount = count($todaycount);

if($kw!='')
{ 
    $where['or'] = array('uname@~'=>$kw,'telephone@~'=>$kw,'true_name@~'=>$kw);
}

if(Request::get('status')){
	$status = Request::get('status');
	if($status == 1){
		$where = array('status'=>'0');
	}
	if($status == 2){
		$where = array('status'=>'1');
	}
	if($status == 3){
		$where = array('status'=>'2');
	}
	if($status == 4){
		$where = array('status'=>'3');
	}
}

$eorders = $menulist->where($where)->list_all();

foreach($eorders as $k=>$v){
    $mm = new Model('member');
	if($v->tjrnumber!='')
	{
	   	$mm->find(array('tjm'=>$v->tjrnumber));
		if($mm->has_id())
		{
		   $eorders[$k]->tjr = $mm->uname;
		}
		else
		{
		   $eorders[$k]->tjr = '----';
		}
	}
	else
	{
	   $eorders[$k]->tjr = '----';
	}
	if($v->is_freez == 0){
		$v->zhzt = "正常";
	}else{
		$v->zhzt = "冻结";
	}
	if($v->status == 0){
		$v->is_status = "未提交信息";
	}
	if($v->status == 1){
		$v->is_status = "审核中";
	}
	if($v->status == 2){
		$v->is_status = "通过";
	}
	if($v->status == 3){
		$v->is_status = "不通过";
	}
}

if('a'==Request::get('etype'))
{
	$arr = $eorders;
	$keynames=array('用户名','真实姓名','联系电话','推荐人','账户状态','实名认证状态','注册时间');//
	$array_key=array('uname','true_name','telephone','tjr','zhzt','is_status','rtime');//
	down_excel($arr, $keynames,$array_key, $name = "用户列表.xls",0);
	exit();
}
//delete
if('del'==Request::get(1)){
	$menulist->find(Request::get(2));
	if($menulist->wid==Session::get('wid')){
		$menulist->remove();
		tusi('操作成功');
		Redirect::delay_to("list",1);
	}
	else
	tusi('操作失败');
}

$p = new Pagination();
$res = $p->model_list($menulist->where($where)->order('id desc'));
foreach($res as $k=>$v){
    $mm = new Model('member');
	if($v->tjrnumber!='')
	{
	   	$mm->find(array('tjm'=>$v->tjrnumber));
		if($mm->has_id())
		{
		   $res[$k]->tjr = $mm->uname;
		}
		else
		{
		   $res[$k]->tjr = '----';
		}
	}
	else
	{
	   $res[$k]->tjr = '----';
	}
	
}








