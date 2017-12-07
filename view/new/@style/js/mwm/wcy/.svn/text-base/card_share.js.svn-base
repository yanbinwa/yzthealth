 document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {

            window.shareData = {  
                "imgUrl": "http://imgcache.life.qq.com/www/misc/images/weixin_shop_logo/1620394988_share_150_150.png?update=1371612628", 
                "timeLineLink": "http://life.qq.com/qr/t13578029830381#wechat_redirect",
                "sendFriendLink": "http://life.qq.com/qr/f13578029830381#wechat_redirect",
                "weiboLink": "http://meishi.qq.com/weixin",
                "tTitle": "关注可获得「麦当劳」微生活会员卡",
                "tContent": "麦当劳",
                "fTitle": "麦当劳",
                "fContent": "关注可获得「麦当劳」微生活会员卡",
                "wContent": "#微信扫描二维码#我刚在麦当劳扫描二维码，轻松获得微生活会员卡，酷炫又好玩！" 
            };
            // 发送给好友
            WeixinJSBridge.on('menu:share:appmessage', function (argv) {
                WeixinJSBridge.invoke('sendAppMessage', { 
                    "img_url": window.shareData.imgUrl,
                    "img_width": "640",
                    "img_height": "640",
                    "link": window.shareData.sendFriendLink,
                    "desc": window.shareData.fContent,
                    "title": window.shareData.fTitle
                }, function (res) {
                    _report('send_msg', res.err_msg);
                })
            });

            // 分享到朋友圈
            WeixinJSBridge.on('menu:share:timeline', function (argv) {
                WeixinJSBridge.invoke('shareTimeline', {
                    "img_url": window.shareData.imgUrl,
                    "img_width": "640",
                    "img_height": "640",
                    "link": window.shareData.timeLineLink,
                    "desc": window.shareData.tContent,
                    "title": window.shareData.tTitle
                }, function (res) {
                    _report('timeline', res.err_msg);
                });
            });

            // 分享到微博
            WeixinJSBridge.on('menu:share:weibo', function (argv) {
                WeixinJSBridge.invoke('shareWeibo', {
                    "content": window.shareData.wContent,
                    "url": window.shareData.weiboLink,
                }, function (res) {
                    _report('weibo', res.err_msg);
                });
            });
        }, false)