/**
 * 公共配置文件
 * 包含公共常量, 配置和方法
 */
;(function() {
    /**
     * 应用公共对象
     */
    window.App = new Object(); //移到 init.js去
    //版本号
    App.Version = 1.0; 
    
    if(!navigator.cookieEnabled){
        alert('请确认您当前的浏览器已开启Cookie，如果您是IOS设备，请在  设置=>safari=>接受Cookie 中选择“总是”');
    }
    /**
     * 常量定义
     */
    var ua = navigator.userAgent.toUpperCase();
    
    
    //特殊浏览器的触发事件处理
    if(!$.fn.tap||ua.indexOf('OPERA') != -1){
        $.fn.tap = $.fn.click;
    }
    // 微信
    if (ua.indexOf('MICROMESSENGER') != -1) {
        $.fn.tap = $.fn.click;
    }
    
    // 服务器地址
    App.ServerHost = '/';
    if (window.location.hostname == 'localhost') {
        App.ServerHost = 'http://dmzstg1.pa18.com/';        
    }
    
    // 后台地址
    App.RequestHost = '/upingan/';
    /**
     * Web Service接口定义
     */
    var webServiceUrls = {
        mayRedBagInit : 'ebusiness/auto/mayRedBagInit.do',                 //初始化接口
        mayRedBagDoOne : 'ebusiness/auto/mayRedBagDoOne.do',               //领取红包接口(10元)
        mayRedBagDoTwo : 'ebusiness/auto/mayRedBagDoTwo.do',               //抢红包接口(50元)
        mayRedBagvalidateCode : 'ebusiness/auto/mayRedBagvalidateCode.do', //验证码验证接口
        randCodeImage : 'ebusiness/auto/rand-code-imgage.do',              //验证码生成接口
        activitySendPwd : 'ebusiness/auto/activitySendPwd.do',             //发送手机动态码
        mayRedBagQuery : 'ebusiness/auto/mayRedBagQuery.do',               //查询我的积分接口
        mayRedBagShare : 'ebusiness/auto/mayRedBagShare.do',               //分享链接生成接口
        myIndex: 'upingan/myIndex.do',										//世界杯：判断是否第一次进入
        robScore: 'upingan/robScore.do',									//世界杯:抢积分接口
        evenignCampaignAttend: 'upingan/evenignCampaignAttend.do',			//世界杯:生成分享链接
        campaignGenerateCode: 'upingan/CampaignGenerateCode.do',			//世界杯:生成动态码
        leadAttendScore: 'upingan/leadAttendScore.do',						//世界杯:领用积分接口
        queryCityByProvince: 'do/queryCityByProvince'                       //行驶城市 
    }
    
    App.getWebServiceUrl = function(name) {
        return App.ServerHost + webServiceUrls[name];
    }
    
    App.getRequestUrl = function(name) {
        return App.RequestHost + webServiceUrls[name];
    }
    
    /**
     *  获取jsonp的路径
     *  example: App.getJSONPUrl('rsupport/vehicle/brand/');    
     */
    App.getJSONPUrl = function(fileName){
        var oldfileName = fileName;
        var origin = location.origin, host = location.host;
        if(!origin){ //android 不识别 location.origin
            var protocol = location.protocol;
            protocol = protocol=="http:"?"http://":protocol=="https:"?"https://":"file:///";
            origin = protocol + (host||location.hostname);
        }
        console.log(host);
        if(host == "www.pingan.com" || host == 'u.pingan.com') {        //生产测试路径
            fileName = 'http://www.pingan.com/' + fileName; 
        } else if(host == "stg.pa18.com" || host == "p1.pa18.com") {    //旧环境测试地址
            fileName = origin + fileName;
        } else {    //新域名测试地址 , 比如 http://dmzstg1.pa18.com/upingan/
            fileName = 'http://stg.pa18.com/' + fileName;
        }
        // if(App.IS_ANDROID) fileName = oldfileName; //android 用当前根路径为起点。。。
        return fileName;
    }
    
    
})();
    
    