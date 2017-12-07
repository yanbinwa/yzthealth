var specListTemplateSource=''
+'<%for(var item in templateData){%>'
+'<%item = templateData[item]%>'
+'<li>'
+	'<label><input type="radio" autocomplete="off" name="spec" onclick="selSpect(this,<%=item[\'id\']%>)" value=\'{"id":"<%=item[\'id\']%>","name":"<%=item[\'name\']%>","type":"<%=item[\'type\']%>","value":"<%=encodeJSON(item[\'value\'])%>"}\' /><%=item[\'name\']%><%if(item[\'note\']){%>[<%=item[\'note\']%>]<%}%></label>'
+'</li>'
+'<%}%>';

var specLiTemplateSource=''
+'<li>'
+	'<label><input type="radio" autocomplete="off" name="spec" onclick="selSpect(this,<%=item[\'id\']%>)" value=\'{"id":"<%=item[\'id\']%>","name":"<%=item[\'name\']%>","type":"<%=item[\'type\']%>","value":"<%=encodeJSON(item[\'value\'])%>"}\' /><%=item[\'name\']%><%if(item[\'note\']){%>[<%=item[\'note\']%>]<%}%></label>'
+'</li>';

//商品规格列表
var showSpecTemplateSource=''
+'<%var valueList = parseJSON(templateData[\'value\']);%>'
+'<%for(var result in valueList){%>'
+'<li>'
+	'<%if(templateData[\'type\'] == 1){%>'
+	'<span><%=valueList[result]%></span>'
+	'<%}else{%>'
+	'<span class="pic"><img src=\'<%=valueList[result]%>\' width=\'30px\' height=\'30px\' /></span>'
+	'<%}%>'
+'</li>'
+'<%}%>';



//选择规格属性
function selSpect(_self,id)
{
	_self.focus();

	//设置当前选中规格的样式
	$('ul>li').removeClass('current');
	$(_self).parent().parent().addClass('current');
	loading('获取预览数据...');

	//Ajax获取规格的详细信息
	$.getJSON("spropselect-"+id+".html",{'random':Math.random()},function(data)
	{
		if(data)
		{
			var showSpecrender = template.compile(showSpecTemplateSource);
			var showSpecHtml = showSpecrender({'templateData':data});
			$('.goods_spec_box').html(showSpecHtml);
			loading(false);	
		}
	});
}

//添加新规格
function addNewSpec()
{
	art.dialog.open('spropedit-0.html', {
		id:'addSpecWin',
	    title:'添加新规格',
	    okVal:'添加',
	    ok:function(iframeWin, topWin){
	    	var formObject = iframeWin.document.forms['specForm'];
			$.getJSON(formObject.action,$(formObject).serialize(),function(json){
				if(json.flag == 'success')
				{
					var specLirender = template.compile(specLiTemplateSource);
					var specLiHtml = specLirender({'item':json.data});
					$('#specs').append(specLiHtml);

		    		//最后一项出发激活事件
		    		$('[name="spec"]:last').trigger('click');
		    		return true;
				}
				else
				{
					alert(json.message);
					return false;
				}
			});
	    }
	});
}