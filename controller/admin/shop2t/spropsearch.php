<?php
$db = DB::get_db();
$wid = Session::get('wid');
$unid = Session::get('unid');

$m = new Model('z02_sprop');

$data = array();
$goods_id = Request::get('id');
$specId   = '';

if($goods_id)
{
	$tb_goods = new IModel('z02_sproduct');
	$tb_goods->find('id = '.$goods_id);
	$data['goodsSpec'] = json_decode($tb_goods->spec_array);
	if($data['goodsSpec'])
	{
		foreach($data['goodsSpec'] as $item)
		{
			$specId .= $item['id'].',';
		}
	}
}

if($specId)
{
	$sql = "select * from z02_sprop where id in (".trim($specId,',').")";
	$data['specData'] = $db->query($sql);
}
