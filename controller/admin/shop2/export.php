<?php 
access_control();
$wid = Session::get('wid');

$filename = date('Y-m-d H:i:s');		//文件名
$letter = array('A','B','C','D','E','F','G','H','I','J','K','L');

// 订单导出
if ('order'==request::get('do')) {
	$orders = new Model('z02_order');
	$keyword = trim($_GET['keyword']);
	$sstatus = intval(Request::get('sstatus'));
	$where = array('wid'=>$wid,'is_del'=>'0');
	if(!empty($keyword))
	{
		$where['id@~'] = $keyword;
	}

	if($sstatus!=0)
	{
		if($sstatus==1)
		{
			$where['wlstatus'] = 1;
		}
		else
		{
			$where['wlstatus'] = 0;
		}
	}
	if ($_GET['start_at']) {
		$start_at = $_GET['start_at'];
		//echo $_GET['start_at'];exit;
		$where['create_time@>='] = $start_at;
		//$where['dishes@~'] = $dishesName;
	}
	if ($_GET['end_at']) {
		$end_at = $_GET['end_at'];
		$where['create_time@<='] = $end_at;
	}

	/*$db = DB::get_db();
	$sql = "select o.id,m.shr,m.phone,m.s_addr,m.yb,o.jg,o.create_time,o.status,o.wlstatus from z02_order as o join micro_membercard_dz_list as m on o.addid = m.id ";
	$sql.= "where ".$where;*/
	$orders = $orders->where($where)->order('id desc')->list_all_array();

	foreach ($orders as $k => $v) {
		$data[$k]['id'] = $v['id'];
		$micro = new Model('micro_membercard_dz_list');
		$micro->find($v['addid']);
		$data[$k]['name'] = $micro->shr;
		$data[$k]['phone'] = $micro->phone;

		$prow = json_decode($v['prolist'],true);
		foreach ($prow as  $value) {
			$dishes = new Model('z02_sproduct');
			$dishes->find($value['pid']);
			$data[$k]['info'] .= $dishes->name.'：'.$value['num'].'个，';
		}
		$data[$k]['totalCount'] = $v['jg'];

		$area = new Model('chinaarea');
		$area->find(array('ord' =>$micro->s_p));
		$data[$k]['addr'] = $area->name;
		$area->find(array('ord' =>$micro->s_s));
		$data[$k]['addr'].= $area->name;
		$area->find(array('ord' =>$micro->s_x));
		$data[$k]['addr'].= $area->name;
		$data[$k]['addr'].= $micro->s_addr;

		$data[$k]['yb'] = $micro->yb;
			
		if ($v['status'] == 0){
			$data[$k]['order_status'] = '未支付';
		}elseif($v['status'] == 1){
			$data[$k]['order_status'] = '已支付';
		}elseif($v['status'] == 2){
			$data[$k]['order_status'] = '已完成';
		}elseif($v['status'] == 3){
			$data[$k]['order_status'] = '申请退款';
		}elseif($v['status'] == 4){
			$data[$k]['order_status'] = '已退款';
		}elseif($v['status'] == 5){
			$data[$k]['order_status'] = '退款失败';
		}
			

		if ($v['wlstatus']== 1 && $v['iskd'] == 0 && $v['kd_no'] == ''){
			$data[$k]['wlstatus'] = '无需物流(已处理)';
		}elseif($v['wlstatus'] == 0){
			$data[$k]['wlstatus'] = '未发货';
		}else{
			$data[$k]['wlstatus'] = '已发货';
			
		}
		if ($v['kdtype'] == -1 || $v['kdtype'] =='') {
			$data[$k]['logistics'] ='';
		}else{
			$data[$k]['logistics'] = '物流公司:'.$v['kdtype'].'-快递单号:'.$v['kd_no'];
		}
		
		$data[$k]['transaction'] = $v['trade_no'];

		$data[$k]['create_time'] = $v['create_time'];

	}
	

	$tableheader = array('订单编号','客户','手机号码','订单详情','价格','地址','邮编','订单状态','发货状态','物流信息','交易单号','下单时间');

	//print_r($data);exit;
	$notation = array('0'=>'A');     //设置那些字段不需要科学计数法
	$excel = new Excel();
	$excel::export($filename,$letter,$tableheader,$data,$notation);
	exit;
}

$db = DB::get_db();
$sql = "select FROM_UNIXTIME(time,'%Y年%m月%d'),turnover,cost,(turnover-cost) from wcy_bill where wid=?";
$data = $db->query($sql,array($wid)); 

$tableheader = array('日期','营业额','成本','利润');
$excel = new Excel();
$excel::export($filename,$letter,$tableheader,$data);
exit;

 ?>