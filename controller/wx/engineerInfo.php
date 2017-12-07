<?php
    
if (Request::get('wid')) {
    $wid = Request::get('wid');
    Session::set('wid', $wid);
}else{
     $wid = Session::get('wid');
}
if(Request::get('opid')){
    $opid = Request::get('opid');
    Session::set('opid', $opid);
    
}else{
    $opid = Session::get('opid');
}
$wxid = Session::get('wxid');

$pubs = new Model('pubs');
$pubs->find($wid);
$signPackage = GetWxSignPackage($wid);
$Wxapi = new Wxapi($pubs->cusid, $pubs->cussec);
if ($code = Request::get('code')) {
    $token = $Wxapi->get_access_token($code);
    $userinfo = $Wxapi->get_user_info($token['access_token'], $token['openid']);
    $openid = $userinfo['openid'];

    $fans = new Model('fans');
    $fans->find(array('wid' => $wid, 'wxid' => $openid));
    if (!$fans->id) {
        $fans->headimg = $userinfo['headimgurl'];
        $fans->nickname = $userinfo['nickname'];
        $fans->wxid = $userinfo['openid'];
        $fans->city = $userinfo['city'];
        $fans->province = $userinfo['province'];
        $fans->country = $userinfo['country'];
        $fans->wid = $wid;
        $fans->save();
    }
    if($fans->id){
        $fans->headimg = $userinfo['headimgurl'];
        $fans->nickname = $userinfo['nickname'];
        $fans->wxid = $userinfo['openid'];
        $fans->city = $userinfo['city'];
        $fans->province = $userinfo['province'];
        $fans->country = $userinfo['country'];
        $fans->wid = $wid;
        $fans->save();        
        Session::set('fansid',$fans->id);
        Session::set('wxid',$openid);
    }

}
$webSet = new Model('web_set');
$webSet->find(array('id'=>2));

if (!empty($wid) && !empty($wxid)) {
	$firsted = 0;
    if($_POST['action']=='location'){
        $fans = new Model('fans');
        $fans->find(array('wxid'=>$wxid));
        if($fans->has_id()){
            $fans->jd = $_POST['jd'];
            $fans->wd = $_POST['wd'];
            $fans->city = $_POST['city'];
            $fans->address = $_POST['address'];
            $fans->save();
        }
        $chinaarea = new Model('chinaarea');
        $chinaarea->find(array('name'=>$_POST['city']));
        if ($chinaarea->has_id()) {
            Session::set('location_c',$chinaarea->id);
            Session::set('firsted','1');
            $firsted = Session::get('firsted');            
        }        

    }	
    $lid = Request::get(1);
    $orders = new Model('orders');
    /*评论总数*/
    $orders_arr = $orders->where(array('status'=>1,'pay_status'=>1,'remark_status'=>1,'unid'=>$lid))->list_all_array();
    $remark_count = count($orders_arr);
    /*好评总数*/
    $orders_good_remark = $orders->where(array('status'=>1,'pay_status'=>1,'remark_status'=>1,'remark_type'=>1,'unid'=>$lid))->list_all_array();
    $good_remark_count = count($orders_good_remark);    
    /*好评度*/ 
    $favorable_rate = floor(($good_remark_count/$remark_count)*100); 
    /*订单总数*/
    $ordersed_arr = $orders->where(array('status'=>1,'pay_status'=>1,'unid'=>$lid))->list_all_array();
    $ordersed_count = count($ordersed_arr);    

    $supplier = new Model('lgc_supplier_user');
    $engineerInfo = $supplier->where(array('id'=>$lid,'is_lls'=>1,'status'=>1))->find();
    if ($supplier->id) {
        if($favorable_rate<20){
            $supplier->estar = 1;
        }
        if (20 < $favorable_rate&&$favorable_rate < 40) {
            $supplier->estar = 2;
        }
        if (40<$favorable_rate&&$favorable_rate<60) {
            $supplier->estar = 3;
        }
        if (60<$favorable_rate&&$favorable_rate<80) {
            $supplier->estar = 4;
        }
        if(80<$favorable_rate){
            $supplier->estar = 5;
        }
        $supplier->order_number = $ordersed_count;
        $supplier->save();        
    }    
    
	$sproduct = new Model('z02_sproduct');
	$sproduct_array = $sproduct->where(array('unid'=>$lid,'is_ll'=>1,'sh_status'=>1,'status'=>0))->order('id')->list_all();
    //总平台添加商品
    $spt_array = $sproduct->where(array('unid'=>null,'is_ll'=>1,'sh_status'=>1,'status'=>0))->order('id')->list_all();    

		
	
}else{
    $f = new Model('fans');
    $f = $f->find(Session::get('fansid'));
    if (!Session::get('fansid') ||$f->wid != $wid){
        //高级接口获取用户信息
        $Wxapi->get_authorize_url(conf::$http_path.'wx/engineerInfo.html');
    }
}


	