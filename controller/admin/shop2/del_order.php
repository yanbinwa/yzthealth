<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-6-24
 * Time: 下午4:17
 */
$o = new Model('z02_order');
$wid = Session::get('wid');
if( Request::post('ids')){
    $ids = Request::post('ids');
    /* if(!is_array($ids)){
         $list = array($ids);
     }else{
         $list = $ids;
     }*/
    foreach($ids as $key => $val){
        $o->find(array("id"=>$val,"wid"=>$wid));
        if(!$o->has_id()){
            Response::write("参数不对！！！");
            exit;
        }
    }
}
$result = $o->update(array('id'=>$ids),array('stat'=>0));
if($result){
    Response::write("删除成功");
}else{
    Response::write("删除失败");
}