// JavaScript Document
//全选
function selectAll(nameVal)
{
	//获取复选框的form对象
	var formObj = $("form:has(:checkbox[name='"+nameVal+"'])");

	//根据form缓存数据判断批量全选方式
	if(formObj.data('selectType')=='none' ||formObj.data('selectType')==undefined)
	{
		$("input[name='"+nameVal+"']").each(function(){
			$(this).prop("checked",true);
		});  
		formObj.data('selectType','all');
	}
	else
	{
		$("input[name='"+nameVal+"']").each(function(){
			$(this).prop("checked",false);
		});  
		formObj.data('selectType','none');
	}
}





//添加新规格
function addNewSpec(spec_id)
{
	var url = '/admin/shop/spropedit-@spec_id@.html';
	url = url.replace('@spec_id@',spec_id?spec_id:0);

	art.dialog.open(url,{
		id:'addSpecWin',
		title:'规格设置',
		okVal:'提交',
		cancelVal:'取消',
		cancel: true,
		ok:function(iframeWin, topWin){ 
			loading('正在提交...');
			var formObject = iframeWin.document.forms['specForm'];
			$.post(formObject.action,$(formObject).serialize(),function(json){
				loading(false);
				if(json.flag == 'success')
				{
					tusi('操作成功！');
					window.location.reload();
					return true;
				}
				else
				{
					tusi(json.message);
					return false;
				}
			},'json');
		}
	});
}






//规格图片上传回调函数
window.updatePic=updatePic;
function updatePic(indexValue,srcValue)
{
	$('#spec_box tr:eq('+indexValue+') td:eq(0) .imgbox').html('<img src="{}'+srcValue+'" class="img_border" width="50px" height="50px"  />');
	$('#spec_box tr:eq('+indexValue+') td:eq(0) :hidden').val(srcValue);
	art.dialog({id:'uploadIframe'}).close();
}

//规格图片html
function getPicHtml(dataValue)
{
	var srcHtml = '';
	if(dataValue)
		var srcHtml = '<img src="{}'+dataValue+'" class="img_border" width="50px" height="50px" />';

	return '<div class="imgbox">'+srcHtml+'</div><input type="hidden" name="value[]" value="'+(dataValue?dataValue:"")+'" /><button  class="btn" rel="yyuc" relobj="elfinder" relfun="setpic1" relmultiple="1" id="upa" type="button"><span>选择图片</span></button>';
}

//规格值html
function getValHtml(dataValue)
{
	if(dataValue == undefined)
		dataValue = '';
	return '<input class="normal" type="text" name="value[]" value="'+(dataValue?dataValue:"")+'" pattern="required" alt="填写规格值" />';
}

//上传按钮html
function getUploadButtonHtml(obj)
{
	art.dialog.data('yyuc_xfiles_obj', obj);
	var specIndex = obj.parent().parent().index();
	var tempUrl = '/@system/upload/';
	//tempUrl     = tempUrl.replace('@specIndex@',specIndex);
	art.dialog.open(tempUrl,
	{
		'id':"uploadIframe",
		'title':'选择上传的图片',
		'width': 970,
		'height': 550,/*
		'ok':function(iframeWin, topWin)
		{
			//iframeWin.document.forms[0].submit();
			
			
			window.yyuc_xfiles_obj.parent().find('.imgbox').html('<img src="'+art.dialog.data('url')+'" class="img_border" width="50px" height="50px"  />');;
			window.yyuc_xfiles_obj.parent().find('input').val(art.dialog.data('url'));
			art.dialog({id:'uploadIframe'}).close();
			return false;
		}*/
	});
}

//根据显示类型返回格式
function getTr(indexValue)
{
	var typeValue = $(':radio:checked').val();

	//规格图片格式
	var specInputHtml = getValHtml();
	if(typeValue==2)
		var specInputHtml = getPicHtml();

	//数据
	var specRow = '<tr class="td_c"><td>'+specInputHtml+'</td>'
	+'<td><img class="operator" src="/media/images/shop/icon_asc.gif" alt="向上" />'
	+'<img class="operator" src="/media/images/shop/icon_desc.gif" alt="向下" />'
	+'<img class="operator" src="/media/images/shop/icon_del.gif" alt="删除" />'
	+'</td></tr>';

	return specRow;
}


//展示规格类型(点击绑定)
$(':radio').click(
	function()
	{
		//获取规格类型
		var typeValue = $(this).val();
		if(typeValue==1)
			$('.border_table thead th:eq(0)').text('规格值');
		else
			$('.border_table thead th:eq(0)').text('规格图片');

		$('#spec_box tr').each(
			function(i)
			{
				//获取文字数据并进行缓存
				var specVal = $('#spec_box tr:eq('+i+') input:text').val();
				if(specVal != $('#spec_box tr:eq('+i+')').data('specVal'))
				{
					$('#spec_box tr:eq('+i+')').data('specVal',specVal);
				}

				//获取图片数据并进行缓存
				var specPic = $('#spec_box tr:eq('+i+') input:hidden').val();
				if(specPic != $('#spec_box tr:eq('+i+')').data('specPic'))
				{
					$('#spec_box tr:eq('+i+')').data('specPic',specPic);
				}

				//文字方式切换
				if(typeValue==1)
				{
					var specVal = $('#spec_box tr:eq('+i+')').data('specVal');
					$(this).children('td:first').html(getValHtml(specVal));
				}

				//图片方式切换
				else
				{
					var specPic = $('#spec_box tr:eq('+i+')').data('specPic');
					$(this).children('td:first').html(getPicHtml(specPic));
				}
				//重新绑定按钮
				initButton(i);
			}
		);
	}
)

//添加规格按钮(点击绑定)
$('#specAddButton').click(
	function()
	{
		var specSize = $('#spec_box tr').size();
		var specRow = getTr(specSize);
		$('#spec_box').append(specRow);
		initButton(specSize);
	}
)

//按钮(点击绑定)
function initButton(indexValue)
{
	//上传图片按钮
	$('#spec_box tr:eq('+indexValue+') td button').click(
		function()
		{
			getUploadButtonHtml($(this));
		}
	);

	//功能操作按钮
	$('#spec_box tr:eq('+indexValue+') .operator').each(
		function(i)
		{
			switch(i)
			{
				//向上排序
				case 0:
				$(this).click(
					function()
					{
						var insertIndex = $(this).parent().parent().prev().index();
						if(insertIndex >= 0)
						{
							$('#spec_box tr:eq('+insertIndex+')').before($(this).parent().parent());
						}
					}
				)
				break;

				//向下排序
				case 1:
				$(this).click(
					function()
					{
						var insertIndex = $(this).parent().parent().next().index();
						$('#spec_box tr:eq('+insertIndex+')').after($(this).parent().parent());
					}
				)
				break;

				//删除排序
				case 2:
				$(this).click(
					function()
					{
						var obj = $(this);
						art.dialog.confirm('确定要删除么？',function(){obj.parent().parent().remove()});
					}
				)
				break;
			}
		}
	)
}










//规格单条删除至回收站
function spec_del(id,type){
	if(confirm('确定要删除选定的规格吗？')){
		loading('删除中...');
		ajax('sprop-del.html?type='+type,{ id:id},function(data){
			loading(false);
			tusi(data);
			location.href = location.href;
		});
	}
}


//规格批量删除至回收站
function spec_delall(type)
{
	var flag = 0;
	$('input:checkbox[name="id[]"]:checked').each(function(i){flag = 1;});
	if(flag == 0)
	{
		alert('请选择要删除的规格');
		return false;
	}
	if(confirm('确定要删除选定的规格吗？')){
		loading('删除中...');
		$("#specForm").ajaxSubmit({  
			type: 'post',  
			url: "sprop-del.html?type="+type ,  
			success: function(data){  
				loading(false);
				tusi(data);
				location.href = location.href; 
			},  
			error: function(XmlHttpRequest, textStatus, errorThrown){  
				alert( "error");  
			}  
		});  
	}
}

//规格单条恢复
function spec_restore(id){
	if(confirm('确定要恢复选定的规格吗？')){
		loading('恢复中...');
		ajax('sprop-restore.html',{ id:id},function(data){
			loading(false);
			tusi(data);
			location.href = location.href;
		});
	}
}


//规格批量恢复
function spec_restoreall()
{
	var flag = 0;
	$('input:checkbox[name="id[]"]:checked').each(function(i){flag = 1;});
	if(flag == 0)
	{
		alert('请选择要恢复的规格');
		return false;
	}
	if(confirm('确定要恢复选定的规格吗？')){
		loading('恢复中...');
		$("#specForm").ajaxSubmit({  
			type: 'post',  
			url: "sprop-restore.html",  
			success: function(data){  
				loading(false);
				tusi(data);
				location.href = location.href; 
			},  
			error: function(XmlHttpRequest, textStatus, errorThrown){  
				alert( "error");  
			}  
		});  
	}
}










window.yyuc_xfiles = function(m,n){
	art.dialog.data('url', m);
	art.dialog.data('name', n);
} 

function yyucpopclose(){
	art.dialog.data('yyuc_xfiles_obj').parent().find('.imgbox').html('<img src="'+art.dialog.data('url')+'" class="img_border" width="50px" height="50px"  />');;
	art.dialog.data('yyuc_xfiles_obj').parent().find('input').val(art.dialog.data('url'));
	art.dialog({id:'uploadIframe'}).close();
}