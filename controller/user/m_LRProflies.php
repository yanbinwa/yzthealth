<?php
if(Request::get('wxid')){
    Session::set('wxid',Request::get('wxid'));
}
if(Request::get('wid')){
    Session::set('wid',Request::get('wid'));
}

if(Session::has('wid')&&Session::has('wxid')){
    $wid = Session::get('wid');
    $wxid = Session::get('wxid');
    $opid = Session::get('opid');
    $bank_arr = array('1'=>'中国工商银行','2'=>'中国农业银行','3'=>'中国建设银行','4'=>'中国银行');
    $member = new Model('member');
    $member->where(array('wid'=>$wid,'wxid'=>$wxid))->find();
    if (!$member->id) {
        Redirect::to('register');
    }

    if($_POST){

        $idcard = request::post('lblUserCard');
        $bank_name = request::post('bank_name');
        $bank_card = request::post('bank_card');
        $bank_type = request::post('bank_type');
        $mid = request::post('mid');
        if(!empty($idcard))
        {
            if (isCreditNo($idcard)) {
                $m = new Model('member');
                $m->find(array('wid'=>$wid,'idcard'=>$idcard));
                if($m->has_id() && $m->id!=$mid)
                {
                    tusi('此信息已被其他账号实名过！');
                }else{
                    $location_c = Session::get('location_c');
                    $chinaarea = new Model('chinaarea');
                    $chinaarea->find(array('id'=>$location_c));
                    $m->find($mid);
                    $m->idcard = $idcard;
                    $m->bank_name = $bank_name;
                    $m->bank_card = $bank_card;
                    $m->fx_wxid = $opid;
                    $m->l_sheng = $chinaarea->pid;
                    $m->l_shi = $location_c;
                    $m->bank_type = $bank_type;
                    $m->status = '1';
                    if($m->save()){
                        tusi('提交成功请耐心等待审核');
                        Redirect::to('m_LRProflies.html');
                    }
                }
            }else{
                tusi('请输入合法身份证号码！');
            }
            
        }else{
            tusi('请将信息填写完整！');
        }
    }

}

/**
 * 判断是否为合法的身份证号码
 * @param $mobile
 * @return int
 */
function isCreditNo($vStr){
  $vCity = array(
    '11','12','13','14','15','21','22',
    '23','31','32','33','34','35','36',
    '37','41','42','43','44','45','46',
    '50','51','52','53','54','61','62',
    '63','64','65','71','81','82','91'
  );
  if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
  if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
  $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
  $vLength = strlen($vStr);
  if ($vLength == 18) {
    $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
  } else {
    $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
  }
  if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
  if ($vLength == 18) {
    $vSum = 0;
    for ($i = 17 ; $i >= 0 ; $i--) {
      $vSubStr = substr($vStr, 17 - $i, 1);
      $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
    }
    if($vSum % 11 != 1) return false;
  }
  return true;
}