<?php
$m = new Model('z02_sproduct');
//标签查询
$tags = new Model('z02_stag');
$tagarr = $tags->where(array('wid'=>Session::get('wid')))->map_array('id', 'name');

//分组查询
$group = new Model('z02_sgroup');
$grouparr = $group->where(array('wid'=>Session::get('wid')))->map_array('id', 'name');

//分类查询
$tm = new Model('z02_stype');
$lbres = $tm->where(array('pid'=>'0','wid'=>Session::get('wid')))->order('ord')->list_all();
get_select_subarr($lbres,'');

$wid = Session::get('wid');


//查看店铺的积分设置
$shopset = new Model('z02_set');
$shopset->find(array('wid'=>Session::get('wid')));



if(Request::get(1))
{
	$m->find(Request::get(1));
	$tuan = $m->istuan;
	$m->tagss = $m->tags;
	if($m->wid == Session::get('wid'))
	{
		$mp = new Model('z02_sprop');
		if(!empty($m->specdata))
		{
			$specarr = explode(',', $m->specdata);
			$specdata = array();
			foreach ($specarr as $v)
			{
				$mp->find($v);
				if($mp->is_del==0)
				{
					$specdata[$v] = array(
						     'id'=>$mp->id,
						     'name'=>$mp->name,
						     'type'=>$mp->type
					);
				}
			}
		}
		if(!empty($m->rowtemp))
		{
			$productdata = json_decode($m->rowtemp);
			foreach ($productdata as $vv)
			{
				if(!empty($vv->spec_array))
				{
					foreach ($vv->spec_array as $key=>$v1)
					{
						$spec_a = explode('_', $v1);
						$mp->find($spec_a[0]);
							if($mp->is_del==0)
							{
								$vv->spec_array[$key] = array(
							     'id'=>$mp->id,
							     'name'=>$mp->name,
							     'type'=>$mp->type,
								 'value'=>$spec_a[1]
								);
							}
					}
				}
			}
		}
	}
	else
	{
		die();
	}
}


if($m->try_post())
{
	$m->wid = Session::get('wid');
	$spec_arrays = implode(',', $_POST['spec_arrays']);
	$m->specdata = $spec_arrays;

	$goods_no   = $_POST['_goods_no'];
	$spec_array = $_POST['_spec_array'];
	$store_nums = $_POST['_store_nums'];
	$market_price = $_POST['_market_price'];
	$sell_price = $_POST['_sell_price'];
	$cost_price = $_POST['_cost_price'];
	$weight = $_POST['_weight'];

	
	//$cc = count($goods_no);
	$productList = array();
	//for ($i=0;$i<$cc;$i++)
	foreach ($goods_no as $key=>$vp)
	{
		$allnum = $allnum + $store_nums[$key];
		$productList[$key] = array(
	                       'goods_no'=>$goods_no[$key],
						   'spec_array'=> $spec_array[$key],
						   'store_nums'=>$store_nums[$key],
						   'market_price'=>$market_price[$key],
						   'sell_price'=> $sell_price[$key],
						   'cost_price'=>$cost_price[$key],
						   'weight'=>$weight[$key]
		);
	}

	asort($market_price);
	foreach ($market_price as $v1)
	{
		$m->yj = $v1;
		break;
	}
	asort($sell_price);
	foreach ($sell_price as $v)
	{
		$m->lowest = $v;
		break;
	}
	
	/*$mcc = count($market_price);
	$scc = count($sell_price);
	$$market_price[$mcc];
	$sell_price[$scc];
	*/
	//$mmax = array_search(max($market_price), $market_price);
    //$mx = $market_price[$mmax];
    //$smin = array_search(min($sell_price), $sell_price);
    //$mn = $sell_price[$smin];
    
    //$number = $mn/$mx;
   // $number = number_format($number, 3);
	
	//$m->zk = $number*10;

	
	$m->store_nums = $allnum;
	$m->create_time = date('Y-m-d H:i:s',time());
    $m->tags = $m->tagss;
	$m->rowtemp = json_encode($productList);
	$m->save();
	tusi('保存成功');
	Redirect::delay_to('sproduct.html',1);
}


// if($wid==3723)
// {
	
	
	page::view("sproductadd2");
	
// }



function get_select_subarr($ll,$leftl)
{
	global $lb_opts;
	foreach ($ll as $l){
		$lb_opts[$l->id] = $leftl.$l->name;
		$tm = new Model('z02_stype');
		$lbres = $tm->where(array('pid'=>$l->id,'wid'=>Session::get('wid')))->order('ord')->list_all();
		get_select_subarr($lbres,$leftl.'　');
	}
}