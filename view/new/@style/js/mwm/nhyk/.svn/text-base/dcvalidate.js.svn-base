
	var Valid = function(form,options){}
	
	Valid.util = {
		getLength:function(value){
			return $.trim(value).length;
		}
	}
	
    //验证方法
	Valid.check = {
		//必填
		required: function(value) 
		{
			return Valid.util.getLength(value) > 0;
		}
		//邮箱
		,email: function( value, element ) 
		{
			return /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(value);
		}
		//网址
		,url: function( value, element ) 
		{
			return /^(\w+:\/\/)?\w+(\.\w+)+.*$/.test(value);
		}
		//日期，时间
		,date:function( value, element ) 
		{
			return !/Invalid|NaN/.test(new Date(value).toString());
		}
		//数字
		,number: function( value, element ) 
		{
			return /^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value);
		}
		//浮点数
		,float: function( value, element ) 
		{
			return /^(([1-9]{1}\d*)|([0]{1}))(\.(\d)*)?$/.test(value);
		}
		//字符最小长度
		,minlength: function( value, params ) 
		{
			var length = $.isArray( value ) ? value.length : Valid.util.getLength($.trim(value));
			return length >= params;
		}
		//字符最大长度
		,maxlength: function( value, params ) 
		{
			var length = $.isArray( value ) ? value.length : Valid.util.getLength($.trim(value));
			return length <= params;
		}
		//字符长度范围
		,rangelength: function( value, params ) 
		{
			var length = $.isArray( value ) ? value.length : Valid.util.getLength($.trim(value), element);
			return length >= params[0] && length <= params[1];
		}
		//数值最小值
		,min: function( value, params ) 
		{	
			return  Number(value) >= Number(params);
		}
		//数值最大值
		,max: function( value, params ) 
		{
			return value <= params;
		}
		//数值范围
		,range: function( value, params ) 
		{
			return value >= params[0] && value <= params[1];
		}
		//中文验证
		,zh:function(value)
		{
			return  /^[\u4E00-\u9FA5\uf900-\ufa2d]+$/.test(value);
		}
		//金额验证
		,money:function(value,params)
		{
				nonzero = false,//true 不允许为零 false 允许为零
				reg,bool;
			
			if (params[0] == '+') {
				reg = /^(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,2})?$/;
				nonzero = true;
			} else if (params[0] == '-') {
				reg = /^(-)(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,2})?$/;
				nonzero = true;
			} else {
				reg = /^(-)?(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,2})?$/;
			}
			
			if (params[1] == 0 || params[1] === true)
				nonzero = false;
			
			if (nonzero == true) //不允许为零
				bool = reg.test(value) && !/^([0]{1})(\.[0]*)?$/.test(value);
			else 
				bool = reg.test(value) ;
			
			return bool;
		}
		//重量验证,保留三位小数
		,weight:function(value,params)
		{
			var rtn =  this.getrtn(),
				nonzero = false,//true 不允许为零 false 允许为零
				reg,bool;
		
			if (params[0] == '+') {
				reg = /^(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,3})?$/;
				nonzero = true;
			} else if (params[0] == '-') {
				reg = /^(-)(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,3})?$/;
				nonzero = true;
			} else {
				reg = /^(-)?(([1-9]{1}\d*)|([0]{1}))(\.(\d){1,3})?$/;
			}
		
			if (params[1] == 0 || params[1] === true)
				nonzero = false;
		
			if (nonzero == true) //不允许为零
				bool = reg.test(value) && !/^([0]{1})(\.[0]*)?$/.test(value);
			else 
				bool = reg.test(value) ;
		
			return bool;
		}
		//ip地址验证
		,ip:function(value)
		{
			return /^\d+\.\d+\.\d+\.\d+$/.test(value);
		}
		//身份证验证15,18 位
		,card:function(value,params)
		{
			return  /^\d{15}|\d{18}$/.test(value);
		}
		//手机验证支持13,14,15,18
		,mobile:function(value,params)
		{
			return /^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$/.test(value);
		}
		//固定电话验证021-2514197，021-25141979
		,phone:function(value,params)
		{
			return /^\d{3}-\d{8}|\d{4}-\d{7}$/.test(value);
		}
		//正则表达式验证
		,pattern:function(value,params)
		{
			if (typeof params === 'string') {
				params = new RegExp(params);
			}
			return params.test(value);
		}
		//邮编 6位
		,post:function(value,params)
		{
			return /^[0-9]{6}$/.test(value);
		}
		//两值相等
		,equalTo: function( value, params ) 
		{
			return value == params;
		}
		//验证用户名:中文、数字、字母或下划线组合，默认2至20位字符
		,username:function(value, params )
		{
			var	pattern = "([u4e00-u9fa5]|[a-zA-Z0-9_])";
			pattern += 	params == "true" ? "{2,20}" : "{"+params+"}";
			pattern = new RegExp('^'+pattern+'$',"g");
			return pattern.test(value);
		}
		//验证密码:中文、数字、字母或下划线组合，默认4至20位字符
		,password:function(value, params )
		{
			var pattern = "([u4e00-u9fa5]|[a-zA-Z0-9_])";
			pattern += 	params == "true" ? "{4,20}" : "{"+params+"}";
			pattern = new RegExp('^'+pattern+'$',"g");
			return pattern.test(value);;
		}
		//ajax验证
		,remote: function( value, params ) {}
		
	}

	

			



