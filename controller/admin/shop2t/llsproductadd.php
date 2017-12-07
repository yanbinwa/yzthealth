<?php
access_control();
$wid = Session::get('wid');


$m = new Model('z02_sproduct');
//标签查询
$tags = new Model('z02_stag');
$tagarr = $tags->where(array('wid'=>$wid))->map_array('id', 'name');

//分组查询
$group = new Model('z02_sgroup');
$grouparr = $group->where(array('wid'=>$wid))->map_array('id', 'name');



//商品分类
$tm_gg = new Model('z02_stype');
$lbres_gg = $tm_gg->where(array('pid'=>'0','wid'=>$wid,'unid'=>null))->order('ord')->list_all();
get_select_subarr_gg($lbres_gg,'');


//品牌分类
$brand = new Model('z02_brand');
$brandarr = $brand->where(array('pid'=>'0','wid'=>$wid,'unid'=>null))->order('ord')->list_all();
get_select_subarr_brand($brandarr,'');
//查看店铺的积分设置
$shopset = new Model('z02_set');
$shopset->find(array('wid'=>$wid));

if(Request::get(1))
{
	$m->find(Request::get(1));
	
	$tuan = $m->istuan;
	$m->tagss = $m->tags;
	if($m->wid == $wid)
	{
	    
		
		//分类查询
		$tm = new Model('z02_stype');
		$lbres = $tm->where(array('pid'=>'0','wid'=>$wid,'unid'=>$m->unid))->order('ord')->list_all();
		get_select_subarr($lbres,'');


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
	$m->wid = $wid;
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
	
	

	$m->store_nums = $allnum;
	$m->sh_status = 1;
    $m->tags = $m->tagss;
	$m->rowtemp = json_encode($productList);
	$m->create_time = date('Y-m-d H:i:s',time());
	$m->is_ll = 1;
	$m->save();
	tusi('保存成功');
	Redirect::delay_to('llsproductsh.html',1);
}


function get_select_subarr($ll,$leftl)
{
	global $lb_opts;
	foreach ($ll as $l){
		$lb_opts[$l->id] = $leftl.$l->name;
		$tm = new Model('z02_stype');
		$lbres = $tm->where(array('pid'=>$l->id))->order('ord')->list_all();
		get_select_subarr($lbres,$leftl.'　');
	}
}

function get_select_subarr_gg($ll,$leftl)
{
	global $lb_opts_gg;
	foreach ($ll as $l){
		$lb_opts_gg[$l->id] = $leftl.$l->name;
		$tm_gg = new Model('z02_stype');
		$lbres_gg = $tm_gg->where(array('pid'=>$l->id))->order('ord')->list_all();
		get_select_subarr_gg($lbres_gg,$leftl.'　');
	}
}


function get_select_subarr_brand($ll,$leftl)
{
	global $lb_opts_brand;
	foreach ($ll as $l){
		$lb_opts_brand[$l->id] = $leftl.$l->name;
		$tm_gg = new Model('z02_brand');
		$lbres_gg = $tm_gg->where(array('pid'=>$l->id))->order('ord')->list_all();
		get_select_subarr_brand($lbres_gg,$leftl.'　');
	}
}
