<?php
if(Request::get('wxid')){
	Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
	Session::set('wid',Request::get('wid'));
}

if(Session::has('wxid') && Session::has('wid')){
	$wid = Session::get('wid');
	$wxid= Session::get('wxid');

	$date = trim($_GET['date']);
	$datearr = explode('-', $date);

	$year = intval($datearr[0])==0?date('Y',time()):intval($datearr[0]);
	$month = intval($datearr[1])==0?date('m',time()):intval($datearr[1]);
	

	$m1 = new Model('micro_membercard_recharge_log');
	$m2 = new Model('micro_membercard_pay_log');

	//入
	$ru = $m1->where("year(create_time)='".$year."' and month(create_time)='".$month."' and wid='".$wid."' and wxid='".$wxid."' and price!=0 and status=0")->sum('price');
	//出
	$chu = $m2->where("year(create_time)='".$year."' and month(create_time)='".$month."'and wid='".$wid."'  and wxid='".$wxid."' and price!=0 ")->sum('price');

	//入
	$rulist = $m1->where("year(create_time)='".$year."' and month(create_time)='".$month."' and wid='".$wid."' and wxid='".$wxid."' and price!=0 and status=0 ")->list_all();
	//出
	$chulist = $m2->where("year(create_time)='".$year."' and month(create_time)='".$month."'and wid='".$wid."'  and wxid='".$wxid."' and price!=0 ")->list_all();

	$all = array_merge($rulist,$chulist);
	$all = bubble_sort($all,'create_time');

	$d = array();
	for($i=0;$i<6;$i++)
	{
		if($i==0)
		$time = time();
		else
		$time = strtotime("-$i month", time());
		
		$ym = date('Y-m', $time);
		$d[]=$ym;
	}

}else{
	die();
}

function bubble_sort($array,$item)
{
	$count = count($array);
	if ($count <= 0) return false;
	for($i=0; $i<$count; $i++)
	{
		for($j=$count-1; $j>$i; $j--)
		{
			//如果后一个元素小于前一个，则调换位置
			if ($array[$j]->$item > $array[$j-1]->$item)
			{
				$tmp = $array[$j];
				$array[$j]= $array[$j-1];
				$array[$j-1] = $tmp;
			}
		}
	}
	return $array;
}