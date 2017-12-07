/*控制loading状态*/
var loading = {
   loadingId : 'loading_gif',
   show : function(){
       var target = document.getElementById(loading.loadingId);
       var position = getPosition();
       target.style.left = 0;
       target.style.top = 0;
       target.style.backgroundPositionX = position.x + 'px';
       target.style.backgroundPositionY = position.y + 'px';
       target.style.display = 'block';
       target.style.zIndex = '1001';
   },
   hideAll : function(){
       document.getElementById(loading.loadingId).style.display = 'none';
   },
   hide : function(){
       document.getElementById(loading.loadingId).style.display = 'none';
   }
}
function getPosition() {
    var x = 0;
    var y = 0;
    var screenW = 640;
    var screenH = document.documentElement.clientHeight;
    var scrollTop = document.body.scrollTop ||document.documentElement.scrollTop;
    y = screenH /2 + scrollTop;
    x = screenW /2;
    return {'x': x, 'y': y};
}
function loadingBegin(){
    loading.show();
}
function loadingFinish(){
    loading.hideAll();
}
/*控制loading状态 end*/

/*百度无线外媒着陆页使用的公共js函数*/
function trim(data){
     return data.replace(/(^\s*)|(\s*$)/g, "");  
}
     
function validateMobile(objValue) {
    var phonegi = /^((13|14|15|18)\d{9,10})?$/;
    var mes = [true, '您未填写手机号码，请填写', '手机号码不合规范,请您重新检查', '手机号码为11位数字,请您重新检查'];
    if(objValue == " ") {
        return mes[1];
    }
    if(objValue.length != 11) {
        return mes[3];
    }
    if(!phonegi.test(objValue)) {
        return mes[2];
    }
    return mes[0];
}

//验证车牌号码
function validateLicenseNo(str) {
	var cityShortName = '京|津|冀|晋|辽|吉|黑|沪|苏|浙|皖|闽|赣|鲁|豫|鄂|湘|粤|桂|琼|渝|川|蜀|黔|贵|滇|云|藏|陕|秦|甘|陇|青|宁|新|港|澳|台|内蒙古|';
	var result = true;
	if(!/^[\u4e00-\u9fa5][a-zA-Z]([a-zA-Z]|\d){5,6}$/.test(str)) {
        result = false;
    }else if(cityShortName.indexOf(str.substring(0, 1)) == -1) {
        result = false;
    }
    return result;
}

function getParams(name) {
    var reg = new RegExp('(^|&)' + name + '=([^&]*)(&|$)', 'i');
    var r = window.location.search.substr(1).match(reg);
    if(r) {
        return decodeURI(r[2]);
    }
    return null;
}

//显示当前时间
function showCurrentTime() {
      var nowDate = new Date();
      var year = nowDate.getFullYear();
      var month = nowDate.getMonth() + 1;
      month = month < 10 ? ('0' + month) : month;
      var date = nowDate.getDate();
      date = date < 10 ? ('0' + date) : date;
      var hour = nowDate.getHours();
      hour = hour < 10 ? ('0' + hour) : hour;
      var minute = nowDate.getMinutes();
      minute = minute < 10 ? ('0' + minute) : minute;
      var second = nowDate.getSeconds();
      second = second < 10 ? ('0' + second) : second;
      $('#showNowTime').html(year + '-' + month + '-' + date + ' ' + hour + ':' + minute + ':' + second);
}

//处理返回信息
function parseResponse(result, callback) {
    if(result) {
        var data = '';
        if(typeof(result)=='string'){ //stz+ 20130722
            eval('data=' + result);
            callback(data);
        }else{
            callback(result);
        }
    } else {
        App.alert('数据请求返回错误');
    }
}

function changeUrl(fileName) {
	var origin = location.origin, host = location.host;
	if(!origin){ //android 不识别 location.origin
	    var protocol = location.protocol;
	    protocol = protocol=="http:"?"http://":protocol=="https:"?"https://":"file:///";
	    origin = protocol + (host||location.hostname);
	}
	console.log('实际host: ' + host);
	if(host == "www.pingan.com" || host == 'u.pingan.com') {
		fileName = 'http://www.pingan.com' + fileName; 
	}else if(host == "stg.pa18.com" || host == "p1.pa18.com") {
		fileName = origin + fileName;
	}else {
		fileName = 'http://dmzstg1.pa18.com' + fileName;
	}
	console.log('实际请求地址: ' + fileName);
	return fileName; 
}

/**
 * Cookie操作
 */
function Cookie(key, value, options) {
    if(arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
        options = options || {};

        if(value === null || value === undefined) {
            options.expires = -1;
        }

        if( typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
        value = String(value);

        return (document.cookie = [encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value), options.expires ? '; expires=' + options.expires.toUTCString() : '', options.path ? '; path=' + options.path : '', options.domain ? '; domain=' + options.domain : '', options.secure ? '; secure' : ''].join(''));
    }
    options = value || {};
    var decode = options.raw ? function(s) {
        return s;
    } : decodeURIComponent;
    var pairs = document.cookie.split('; ');
    for(var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
        if(decode(pair[0]) === key)
            return decode(pair[1] || '');
    }
    return null;
}

window.back = function() {}


// AJAX公共配置
/*if($) {
    $.ajaxSettings.beforeSend = loadingBegin;
    //$.ajaxSettings.complete = loadingFinish;
    $.ajaxSettings.error = function(jqXHR, textStatus, errorThrown) {
        loadingFinish();
        if(!window.navigator.onLine || jqXHR.status) {
            alert('网络连接失败, 请稍后重试！');
        }
    }
}
*/