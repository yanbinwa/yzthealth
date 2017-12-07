<?php
$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
$searchword = Request::get('searchword');
if (!empty($wid) && !empty($wxid)) {
    $where.= "is_lls='1' and status='1'";    
    $fans = new Model('fans');
    $fans->find(array('wxid'=>$wxid));
    $chinaarea = new Model('chinaarea');
    if($fans->id){
        $chinaarea->find(array('name@~'=>$searchword));
        if ($chinaarea->id) {
            $area = 1;
            if($chinaarea->typ == 1){
            $where.= " and location_p like '%".$searchword."%' ";
            }elseif ($chinaarea->typ == 2) {
                $where.= " and location_c like '%".$searchword."%' ";
            }elseif ($chinaarea->typ == 3) {
                $where.= " and location_a like '%".$searchword."%' ";
            }
        }else{
            $chinaarea->find(array('name@~'=>$fans->city));
            if($chinaarea->has_id()){
                $where.= " and location_c like '%".$fans->city."%'";
            } 
            $supplier = new Model('lgc_supplier_user');
            $sup_arr = $supplier->where(array('is_lls'=>1,'status'=>1,'lxr@~'=>$searchword))->list_all_array(); 
            if (count($sup_arr)>0) {
                $where.=" and lxr like '%".$searchword."%' "; 
            }else{
                 $sproduct = new Model('z02_sproduct');
                 $sup_arr = $sproduct->where(array('is_ll'=>1,'sh_status'=>1,'name@~'=>$searchword))->list_all_array(); 
                 if(!empty($sup_arr)){
                    foreach ($sup_arr as $key => $value) {
                        $unid[] = $value['unid'];
                    }
                    $shopid_arr = array_flip(array_flip($unid)); 
                    foreach ($shopid_arr as $k => $v) {
                        $shop_arr[] = $v;
                    }

                 }
            }
                         

        }
        
    }    
    

    $db = DB::get_db();
    if (Request::get(1) =='dropload') {
        $sta = intval(Request::get('page'));
        if (empty($sta)) {
            $page = 0;
        }else{
            $page = $sta*10;           
        }
        
        if (!empty($shop_arr)) {
            $count =count($shop_arr);
            if ($page<=$count) {
                $j = 0;
                for ($i=$page; $i < $count; $i++) { 
                    $sql = "SELECT id,lxr,jianjie,address,dz,jd,wd,order_number from lgc_supplier_user WHERE id='".$v."'";
                    $sup_arr = $db->query($sql);

                    $jianjie = strip_tags($sup_arr[0]['jianjie']);
                    $supplier_array[$j]['jianjie'] = mb_substr($jianjie,0,38,'utf-8'); 
                    $supplier_array[$j]['id'] = $sup_arr[0]['id'];
                    $supplier_array[$j]['lxr'] = $sup_arr[0]['lxr'];
                    $supplier_array[$j]['address'] = $sup_arr[0]['address'];
                    $supplier_array[$j]['dz'] = $sup_arr[0]['dz'];
                    $supplier_array[$j]['jd'] = $sup_arr[0]['jd'];
                    $supplier_array[$j]['wd'] = $sup_arr[0]['wd'];
                    $supplier_array[$j]['order_number'] = $sup_arr[0]['order_number'];                   
                    $j++;
                }
            }
            
            
        }else{
            $sql = "SELECT id,lxr,jianjie,address,dz,jd,wd,order_number from lgc_supplier_user WHERE ".$where." order by id desc limit $page,10";
            $supplier_array = $db->query($sql);
            foreach($supplier_array as $k=>$v){
                $jianjie = strip_tags($v['jianjie']);
                $supplier_array[$k]['jianjie'] = mb_substr($jianjie,0,38,'utf-8');
            }            
        }
        
        
        echo json_encode($supplier_array);
        die;
    }
}
