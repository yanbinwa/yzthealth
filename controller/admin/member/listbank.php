<?php
access_control();
$db  = DB::get_db();
$menulist = new Model('member');

$kw = trim($_POST['title']);
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
$eorders = $menulist->where($where)->list_all();
$p = new Pagination();
$res = $p->model_list($menulist->where($where)->order('id desc'));
/*foreach($res as $k=>$v){
    $mm = new Model('member');
    $bankCared = new Model('banks_card');
	$bankCared->find(array('mid'=>$v->id));
	$v->id_card = $bankCared->id_card;
	$v->card_num = $bankCared->card_num;
	$v->bank_name = $banktype[$bankCared->bank_id];
}*/


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


