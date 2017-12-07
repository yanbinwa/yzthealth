<?php
/**
 * JS_API支付demo  v33
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
 */
ini_set('display_errors', true);
error_reporting(E_ALL);

$wid = Request::get('wid');
$wxid = Request::get('wxid');
$did_str = Request::get('orders_str');
$did_arr = explode('-', $did_str);

$payset = new Model('ai9me_pay_set');
$payset->find(array('id'=>'1'));
$dd = new Model('orders');
$totalyhh = 0;
$totalwyh = 0;
foreach ($did_arr as $key => $value) {
	$dd->find($value);
	if ($dd->id) {
		if ($dd->couponid) {
			$coupon = new Model('youhuiquan_list');
			$coupon->find(array('id'=>$dd->couponid,'state'=>0));
			$yhq = new Model('youhuiquan');
			$yhq->find(array('id'=>$coupon->yid));
			$yhje = $yhq->redcon;
			$totalyhh += $dd->total;
		}else{
			$totalwyh += intval(100*(floatval($dd->total)));
		}
	}
	$out_trade_no = "WXLL_".$value.time();
	$dd->trade_no = $out_trade_no;
	$dd->save();
}
if ($totalyhh>0&&$totalwyh==0) {
	$total = intval(100*(floatval($totalyhh)-floatval($yhje)));
}else if($totalyhh==0&&$totalwyh>0){
	$total = $totalwyh;
}


/*define(APPID, 'wxd2923637cc9b6b4b');  //appid
define(MCHID, '10016687'); //受理商ID，身份标识
define(KEY, '7fed76366d20b46e63c2c42e015d8bbf');      //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
define(APPSECRET, '55914a6c324bf50506c82e7cefe1e390');*/

define(APPID, $payset->appId);  //appid
define(MCHID, $payset->partner); //受理商ID，身份标识
define(KEY, $payset->key);      //商户支付密钥Key。审核通过后，在微信发送的邮件中查看
define(APPSECRET, $payset->appSecret);

include_once("/home/wwwroot/wxpay/WxPayHelperv33.php");

if($dd->has_id() && $payset->has_id() && $dd->status == '0'){
	$body = '颐正国际养生堂连锁店理疗订单';
	//	$body = '商品明细';
	//使用jsapi接口
	$jsApi = new JsApi();
	$openid = $wxid;
	//=========步骤2：使用统一支付接口，获取prepay_id============
	//使用统一支付接口
	$unifiedOrder = new UnifiedOrder();

	//设置统一支付接口参数
	//设置必填参数
	//appid已填,商户无需重复填写
	//mch_id已填,商户无需重复填写
	//noncestr已填,商户无需重复填写
	//spbill_create_ip已填,商户无需重复填写
	//sign已填,商户无需重复填写
	$unifiedOrder->setParameter("openid",$openid);//商品描述
	$unifiedOrder->setParameter("body",$body);//商品描述
	//自定义订单号，此处仅作举例

	//$timeStamp = time();
	$unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号
	$unifiedOrder->setParameter("total_fee",$total);//总金额
	$unifiedOrder->setParameter("notify_url",Conf::$http_path."wx/wxpayokv33-".$wid."-".$did_str.".html");//通知地址
	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型

	$prepay_id = $unifiedOrder->getPrepayId();
	//=========步骤3：使用jsapi调起支付============
	$jsApi->setPrepayId($prepay_id);

	$jsApiParameters = $jsApi->getParameters();

}
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<script type="text/javascript">

		//调用微信JS api 支付
		function jsApiCall()
		{
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					//WeixinJSBridge.log(res.err_msg);
					//alert(res.err_code+res.err_desc+res.err_msg);
					
					if(res.err_msg=='get_brand_wcpay_request:ok'){
						location.href='<?php echo Conf::$http_path;?>user/ddcenter.html?wid=<?php echo $wid;?>&wxid=<?php echo $wxid;?>';		
					}else{
						location.href='wx/yangsheng.html';
					}
				}
			);
		}
		
		function callpay()
		{
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}
		callpay();
	</script>
</head>
<body>
</body>
</html>
				<?php
				die();
				?>