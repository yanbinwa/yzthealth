<?php
//创客分红
$t = Request::get(1);
$m = new Model('cx_fx_fl_info_listwithlev');
if($t==1)
{
  $pagename = '总部';
}
if($t==2)
{
  $pagename = '讲师';
}
if($t==3)
{
  $pagename = '门店OEM';
}
if($t==4)
{
  $pagename = '销售中心';
}
$where['role'] = $t;

$p = new Pagination();
$res = $p->model_list($m->where($where)->order("id desc"));


if($t==1) //总部
{
  $zbm = new Model('cx_dlscenter2_zb');
  $cksarr = $zbm->where(array())->map_array('id', 'name');
  foreach($res as $v)
  {
     $v->ckname = $cksarr[$v->roleid];
  }
  //总金额
  $amount = $m->where($where)->sum('fl_amount');
}


if($t==2) //营销中心
{
  $dlm = new Model('cx_dlscenter2_js');
  $dlarr = $dlm->where(array())->map_array('id', 'name');
  foreach($res as $v)
  {
     $v->ckname = $dlarr[$v->roleid];
  }
  
  //总金额
  $amount = $m->where($where)->sum('fl_amount');
}
if($t==3) //门店
{
  $dlm = new Model('cx_z02_set');
  $dlarr = $dlm->where()->map_array('wid', 'name');
  foreach($res as $v)
  {
     $v->ckname = $dlarr[$v->roleid];
  }
  
  //总金额
  $amount = $m->where($where)->sum('fl_amount');
}
if($t==4) //销售中心
{
  $dlm = new Model('cx_fx_wd_list');
  $dlarr = $dlm->where(array('is_xszx'=>1))->map_array('id', 'shop_name');
  foreach($res as $v)
  {
     $v->ckname = $dlarr[$v->roleid];
  }
  //总金额
  $amount = $m->where($where)->sum('fl_amount');
}




