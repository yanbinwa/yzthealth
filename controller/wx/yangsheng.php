<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/24
 * Time: 14:01
 */

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$opid = Request::get('opid')?Request::get('opid'):Session::get('opid');
if(!$wid)
    die('请从微信进入');
Session::set('wid', $wid);
Session::set('opid', $opid);
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
if(Session::get('wxid')){
    $wxid = Session::get('wxid');
    $wechatset = new Model('web_set');
    $wechatset->find(array('id'=>2));
    $firsted = 0;
    if($_POST['action']=='location'){
        $fans = new Model('fans');
        $fans->find(array('wxid'=>$wxid));
        if($fans->has_id()){
            $fans->jd = $_POST['jd'];
            $fans->wd = $_POST['wd'];
            $fans->save();
        }

    }
    if(Request::get('city')||Request::get('district')){
        $city = Request::get('city');
        $district = Request::get('district');
        $address = Request::get('address');
        $fans = new Model('fans');
        $fans->find(array('wxid'=>$wxid));
        if($fans->has_id()){
            $fans->city = $city;
            $fans->district = $district;
            $fans->address = $address;
            $fans->save();
        }
        $chinaarea = new Model('chinaarea');
        $chinaarea->find(array('name'=>$district));
        if($chinaarea->has_id()){
            Session::set('location_area',$district);
/*            $where['location_a@~'] = $district;
            $wherell['location_a@~'] = $district;*/
            Session::set('location_a',$chinaarea->id);
            Session::set('location_c',$chinaarea->pid);
            Session::set('firsted','1');
            $firsted = Session::get('firsted');
        }else{
            $chinaarea->find(array('name'=>$city));
            if($chinaarea->has_id()){
                Session::set('location_city',$city);
                /*$where['location_c@~'] = $city;
                $wherell['location_c@~'] = $city;*/
                Session::set('location_c',$chinaarea->id);
                Session::set('firsted','1');
                $firsted = Session::get('firsted');
            }
        }
    }
    $fans = new Model('fans');
    $fans->find(array('wxid'=>$wxid));

    $lls_arr = array();
    $lld_arr = array();
    $db = DB::get_db();
    if (Request::get(1) =='dropload') {
        $location_area = Session::get('location_area');
        $location_city = Session::get('location_city');

        $sta = intval(Request::get('page'));
        if (empty($sta)) {
            $sta = 0;
        }
        $sta = $sta*5;
        if (!empty($location_area)) {
            $sql = "SELECT shopname,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='0' and location_a like '%".$location_area."%' order by id asc limit $sta,5";
        }else if(!empty($location_city)){
            $sql = "SELECT shopname,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='0' and location_a like '%".$location_city."%' order by id asc limit $sta,5";
        }        
        $supplierarray = $db->query($sql);
        if (empty($supplierarray)) {
             $sql_all = "SELECT shopname,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='0' order by id asc limit $sta,5";
            $supplierarray = $db->query($sql_all);
        }

        foreach ($supplierarray as $k => $v) {
            $lld_arr[$k]['shopname'] = mb_substr( $v['shopname'], 0, 6,'utf-8');
            $lld_arr[$k]['distance'] = getDistance($fans->jd,$fans->wd,$v['jd'],$v['wd'],2);
            $lld_arr[$k]['dz'] = $v['dz'];
            $lld_arr[$k]['id'] = $v['id'];
        }
        $sortd = array(  
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
            'field'     => 'distance',       //排序字段  
        );  
        $arrSortd = array();  
        foreach($lld_arr AS $uniqdid => $rowd){  
            foreach($rowd AS $key=>$value){  
                $arrSortd[$key][$uniqdid] = $value;  
            }  
        }  
        if($sortd['direction']){  
            array_multisort($arrSortd[$sortd['field']], constant($sortd['direction']), $lld_arr);  
        }         

        if (!empty($location_area)) {
            $sqls = "SELECT lxr,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='1' and location_a like '%".$location_area."%' order by id asc limit $sta,5";
        }else if(!empty($location_city)){
            $sqls = "SELECT lxr,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='1' and location_a like '%".$location_city."%' order by id asc limit $sta,5";
        }          
        $lls_array = $db->query($sqls);
        if (empty($lls_array)) {
            $sql_all = "SELECT lxr,dz,id,jd,wd from lgc_supplier_user where hot='1' and status='1' and is_lls='1' order by id asc limit $sta,5";
            $lls_array = $db->query($sql_all);
        }    
        foreach ($lls_array as $k => $v) {
            $lls_arr[$k]['lxr'] = mb_substr( $v['lxr'], 0, 6,'utf-8');
            $lls_arr[$k]['distance'] = getDistance($fans->jd,$fans->wd,$v['jd'],$v['wd'],2);
            $lls_arr[$k]['dz'] = $v['dz'];
            $lls_arr[$k]['id'] = $v['id'];
        }
        $sorts = array(  
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
            'field'     => 'distance',       //排序字段  
        );  
        $arrSorts = array();  
        foreach($lls_arr AS $uniqsid => $rows){  
            foreach($rows AS $key=>$value){  
                $arrSorts[$key][$uniqsid] = $value;  
            }  
        }  
        if($sorts['direction']){  
            array_multisort($arrSorts[$sorts['field']], constant($sorts['direction']), $lls_arr);  
        }         
        $ll_array = array();
        $ll_array['lld'] = $lld_arr;
        $ll_array['lls'] = $lls_arr;
        echo json_encode($ll_array);
        die;
    }

    

}else{

    $f = new Model('fans');
    $f = $f->find(Session::get('fansid'));
    if (!Session::get('fansid') ||$f->wid != $wid){
        //高级接口获取用户信息
        $Wxapi->get_authorize_url(conf::$http_path.'wx/yangsheng.html');
    }
}
