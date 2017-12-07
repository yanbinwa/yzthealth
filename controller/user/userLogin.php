<?php

$wid = Request::get('wid')?Request::get('wid'):Session::get('wid');
$wxid = Session::get('wxid');
if (!empty($wid) && !empty($wxid)) {
    $u = new SampleModel();
    if ($u->try_post()) {
        $supplier = new Model('lgc_supplier_user');
        $supplier->find(array('uns' => $u->uns));
        if ($supplier->has_id()) {
            if ($supplier->passwords == $u->passwords) {
                $supplier->wxid = $wxid;
                $supplier->save();
                Redirect::to('/wx/yangsheng');
            } else {
                tusi('登录信息不正确');
            }
        } else {
            tusi('用户不存在');
        }
    }
}