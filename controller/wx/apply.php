<?php

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$opid = Session::get('opid');
if (!empty($wid) && !empty($wxid)) {
    $web_set = new Model('web_set');
    $web_set->find(array('id'=>2));
    
    $apply = new Model('apply_cooperation');
    $apply->find(array('wxid'=>$wxid,'wid'=>$wid));
    if($apply->id&&$apply->pay_status&&!$apply->status){
       Redirect::to('auditing.html');
    }
    if ($apply->status&&$apply->pay_status) {
        Redirect::to('tongguo.html');
    }
    if ($apply->try_post()&&$_POST['apply_type']) {
        $validCode = $_POST['validCode'];
        $apply_type = $_POST['apply_type'];
        $sendCode = new Model('member_send_code_log');
        $sendCode->where(array('tel'=>$apply->telephone,'status'=>0))->order('id desc')->find();  
        if($sendCode->has_id() && $sendCode->code == $validCode){
            $apply->wid = $wid;
            $apply->wxid = $wxid;
            $apply->apply_type = $apply_type;
            $apply->create_time = date('Y-m-d H:i:s',time());
            $apply->save();
            $sendCode->status = 1;
            $sendCode->save();
            if ($apply->id) {      
                Redirect::to(Conf::$http_path.'wxpay/payApply-'.$apply->id.'.html?showwxpaytitle=1&wid='.$wid.'&wxid='.$wxid);
            }
            die();

        }else{
           tusi('验证码错误');
        }

    }
    	

    
}