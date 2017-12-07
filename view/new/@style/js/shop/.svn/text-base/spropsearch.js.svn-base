//规格标签按钮
var specButtonTemplateSource = ''
+	'<%for(var item in templateData){%>'
+		'<%include(\'specButtonLiTemplate\',{ \'item\':templateData[item]})%>'
+	'<%}%>';

//规格标签按钮
var specButtonLiTemplateSource = ''
+	'<li id="specButton<%=item[\'id\']%>">'
+		'<a href="javascript:void(0);" style="display:inline;padding-right:8px;" onclick="selectTab(<%=item[\'id\']%>);" hidefocus="true"><%=item[\'name\']%></a>'
+		'<label class="radio"><img src="/media/images/shop/icon_del.gif" class="delete" title="删除" onclick="delSpec(<%=item[\'id\']%>,this);" /></label>'
+	'</li>';


//水平规格列表
var horizontalSpecTemplateSource = ''
+	'<%for(var item in templateData){%>'
+		'<%include(\'horizonalSpecDlTemplate\',{ \'item\':templateData[item]})%>'
+	'<%}%>';


//水平规格单行
var horizonalSpecDlTemplateSource = ''
+	'<%var className = item[\'type\']==1 ? \'w_27\':\'w_45\'%>'
+	'<dl class="summary clearfix" id="horizontal_<%=item[\'id\']%>" style=\'display:none\'>'
+		'<dt>点击添加所需<%=item[\'name\']%>：<span>如果没有您需要的<%=item[\'name\']%>？请到规格列表中编辑<%=item[\'name\']%></span></dt>'
+		'<dd class="<%=className%>">'
+			'<%var valueList = parseJSON(item[\'value\']);%>'
+			'<%for(var result in valueList){%>'
+			'<div class="item">'
+				'<a href="javascript:selectSpec({ \'id\':<%=item[\'id\']%>,\'value\':\'<%=valueList[result]%>\',\'name\':\'<%=item[\'name\']%>\',\'type\':<%=item[\'type\']%>});">'
+				'<%if(item[\'type\']==1){%>'
+					'<%=valueList[result]%>'
+				'<%}else{%>'
+					'<img src="<%=valueList[result]%>" width="30px" height="30px"/>'
+				'<%}%>'
+				'</a>'
+			'</div>'
+			'<%}%>'
+		'</dd>'
+	'</dl>';


//垂直规格列表
var verticalSpecTemplateSource = ''
+	'<%for(var item in templateData){%>'
+	'<%item = templateData[item]%>'
+	'<tbody id="vertical_<%=item[\'id\']%>" style=\'display:none\'>'
+		'<%var tempSpecArray = item[\'value\'].split(\',\')%>'
+		'<%for(var result in tempSpecArray){%>'
+		'<%result = tempSpecArray[result]%>'
+			'<%if(result){%>'
+				'<%include(\'verticalRowTemplate\',{\'id\':item[\'id\'],\'name\':item[\'name\'],\'type\':item[\'type\'],\'value\':result})%>'
+			'<%}%>'
+		'<%}%>'
+	'</tbody>'
+	'<%}%>';

//垂直规格单行
function  verticalRowTemplateSource (specJson)
{
	var verticalSpecTemplateSource = ''
	+	'<tr class=\'td_c\'>'
	+		'<td>'
	+			'<input type="hidden" name="specJson" value=\'{"id":"'+specJson.id+'","name":"'+specJson.name+'","type":"'+specJson.type+'","value":"'+specJson.value+'"}\' />';
	
	if(specJson.type==1)
	{
		verticalSpecTemplateSource += specJson.value;
	}
	else
	{
		verticalSpecTemplateSource += '<img width="30px" height="30px" src="'+specJson.value+'" class="img_border" />';
	}
	
	verticalSpecTemplateSource +=		'</td>'
	verticalSpecTemplateSource +=		'<td>'
	verticalSpecTemplateSource +=			'<img class="operator" src="/media/images/shop/icon_asc.gif" onclick="positionUp($(this).parent().parent(),$(this).parent().parent().parent());" alt="向+上" />'
	verticalSpecTemplateSource +=			'<img class="operator" src="/media/images/shop/icon_desc.gif" onclick="positionDown($(this).parent().parent(),$(this).parent().parent().parent());" alt="+向下" />'
	verticalSpecTemplateSource +=			'<img class="operator" src="/media/images/shop/icon_del.gif" onclick="itemRemove($(this).parent().parent());" alt="删除" />'
	verticalSpecTemplateSource +=		'</td>'
	verticalSpecTemplateSource +=	'</tr>';
	return verticalSpecTemplateSource;
}




/**
 * 从已有的规格进行点选
 * @param specJson js对象 id,name,value,type
 */
function selectSpec(specJson)
{
		$('#verticalBox').show();
		//某规格小容器是否存在 
		var specTbody = $('#vertical_'+specJson.id);
		if(specTbody.length == 0)
		{
			var verticalRowHtml = verticalRowTemplateSource (specJson);
			var verticalSpecHtml = "<tbody  id='vertical_"+specJson.id+"'>"+verticalRowHtml+"</tbody>";
			$('#verticalBox .border_table').append(verticalSpecHtml);
		}
		else
		{
			//规格值是否已经存在
	        var matchValue = '"value":"'+specJson.value+'"';
			if(specTbody.find('input:hidden[value*='+matchValue+']').length > 0)
			{
				alert('此规格值已经添加过了');
				return;
			}
			var verticalRowHtml = verticalRowTemplateSource (specJson);
			specTbody.append(verticalRowHtml);
		    $('#vertical_'+specJson.id).show();
		}
}

/**
 * 选择当前Tab
 * @param spec_id 规格ID
 * @param _self 按钮本身
 */
function delSpec(spect_id,_self)
{
	art.dialog.confirm('确定要删除么？',function(){
		//移除tab规格标签按钮
		$(_self).parent().parent().remove();

		//移除水平规格
		$('#horizontal_'+spect_id).remove();

		//移除垂直规格
		$('#vertical_'+spect_id).remove();

		defaultHoverSpecButton();
	});
}

//默认激活规格按钮
function defaultHoverSpecButton()
{
	//已经没有待选择规格
	if($('.tab>li').length == 0)
	{
		$('#verticalBox').hide();
	}
	else
	{
		//默认激活第一个规格
		$('.tab>li:first a').trigger('click');
	}
}

//添加模型规格
function addSpec()
{
	art.dialog.open('spropselect.html', {
		title: '选择规格',
		okVal:'保存',
		ok:function(iframeWin, topWin){
			var specChecked = $(iframeWin.document).find('[name="spec"]:checked');
			if(specChecked.length == 1)
			{
				var specJson = $.parseJSON(specChecked.val());

				//监测规格是否已经存在
				if($('#horizontal_'+specJson.id).length > 0)
				{
					alert('规格已经被添加，不能重复添加');
					return false;
				}

				//规格按钮标签
				var specButtonLirender = template.compile(specButtonLiTemplateSource);
				var specButtonLiHtml = specButtonLirender({'item':specJson});
				$('.tab').append(specButtonLiHtml);
				

				//规格水平列表展示
				var horizonalSpecDlrender = template.compile(horizonalSpecDlTemplateSource);
				var horizonalSpecDlHtml = horizonalSpecDlrender({'item':specJson});
				$('#horizontalBox').append(horizonalSpecDlHtml);
				
				
				

				//激活新添加的规格
				$('.tab>li:last a').trigger('click');
			}
		}
	});
}

/**
 * 选择当前Tab
 * @param spec_id 规格ID
 */
function selectTab(spec_id)
{
	//隐藏垂直规格外部
	if($('.tab>li').length == 0)
	{
		$('#verticalBox').hide();
	}
	else
	{
		$('#verticalBox').show();
	}

	//按钮高亮
	$('.tab>li').removeClass('selected');
	$('#specButton'+spec_id).addClass('selected');

	//水平规格展示
	$('[id^="horizontal_"]').hide();
	$('#horizontal_'+spec_id).show();

	//垂直规格展示
	$('[id^="vertical_"]').hide();
	$('#vertical_'+spec_id).show();
}

//项上升
function positionUp(_self,container)
{
	var childrenIndex = container.children().index(_self);
	if(childrenIndex == 0)
	{
		return;
	}
	_self.insertBefore(container.children().get(childrenIndex-1));
}

//项下降
function positionDown(_self,container)
{
	var childrenIndex = container.children().index(_self);
	if(childrenIndex == container.children().length - 1)
	{
		return;
	}
	_self.insertAfter(container.children().get(childrenIndex+1));
}

//项删除
function itemRemove(_self)
{
	art.dialog.confirm('确定要删除么？',function(){_self.remove()});
}