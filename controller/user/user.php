<?php
if(Request::get('wxid')){
    Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
    Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')&&Session::has('wxid')){
    $wxid = Session::get('wxid');
    $wid = Session::get('wid');
    $fans = new Model('fans');
    $fans->find(array('wxid'=>$wxid,'wid'=>$wid));
    $member = new Model('member');
    $member->find(array('wxid'=>$wxid,'wid'=>$wid));

    $orders = new Model('orders');
    $orders_arr_num = $orders->where(array('wxid'=>$wxid,'status'=>1,'pay_status'=>1))->count();
    $orders_array_num = $orders->where(array('wxid'=>$wxid,'status'=>0,'pay_status'=>1))->count();   

    $renzmember = new Model('member');
    $renzmember->where(array('wxid'=>$wxid,'wid'=>$wid,'status'=>2))->find();

}

