<?php
if(Request::get('wxid')){
    Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
    Session::set('wid',Request::get('wid'));
}
if(Session::has('wid')){
    $wid = Session::get('wid');
    $wxid = Session::get('wxid');
    $pid = Request::get(1,'0');
    //开启微客服
    $kefu = new Model('pubs');
    $kefu->find($wid);
    $wgw_kefu = $kefu->wsc_kefu;

    $fu=new Model('micro_membercard_member_list');
    $fu->find(array('wxid'=>$wxid,'wid'=>6797969));
    if($fu->id){
        $fuck=$fu->member_level;
        Session::set('fuck',$fuck);

    }else{
        $fuck=-1;
    }
    //开启QQ客服
    $qq_kefu = json_decode($kefu->qq_kefu,true);
    if($qq_kefu['wsckefu'] == 1 && $qq_kefu['num'])
    {
        $qq_kefu_num = explode('|',$qq_kefu['num']);
    }
    //上方幻灯片
    $pro = new Model('z02_sproduct');
    $tjres = $pro->where("wid = $wid and zx =1 and  pic2!='' and  pic2!='http://www.weixinguanjia.cn/media/images/shop/mhdp.png'  and status=0  and sh_status=>1 ")->limit(6)->order('sort desc')->list_all();

    //商品分类
    $fl = new Model('z02_stype');
    $fls = $fl->where(array('wid'=>$wid,'pid'=>$pid))->order('ord,id')->limit(8)->list_all();

    //10个最新  10个最热  10个折扣
    $pro = new Model('z02_sproduct');
    $tjzx = $pro->where(array('wid'=>$wid,'status'=>0,'zx'=>1,'sh_status'=>1,'is_ll'=>0))->limit(10)->order('sort desc')->list_all();

    $pro = new Model('z02_sproduct');
    $tjzr = $pro->where(array('wid'=>$wid,'rm'=>'1','status'=>0,'sh_status'=>1,'is_ll'=>0))->limit(10)->order('sort desc')->list_all();

    $pro = new Model('z02_sproduct');
    $tjzk = $pro->where(array('wid'=>$wid,'show_zk'=>'1','status'=>0,'sh_status'=>1,'is_ll'=>0))->limit(10)->order('sort desc')->list_all();

    $selecth = 1;
    $model = new Model('z02_set');
    $model->find(array('wid'=>$wid));

    $model->jianjie = str_replace(array("\r\n", "\r", "\n"), "", $model->jianjie);

    $signPackage = GetWxSignPackage($wid);


}else{
    die('请从微信进入');
}