//商品标题模板
var goodsHeadTemplateSource = ' <tr>'
+	'<th >商品货号</th>'
+	'<%var isProduct = false;%>'
+	'<%for(var item in templateData){%>'
+   '<%item = templateData[item]%>'
+	'<%isProduct = true;%>'
+	'<input type=\'hidden\' name="spec_arrays[]" value=\'<%=item[\'id\']%>\'  />'

+	'<th><%=item[\'name\']%></th>'
+	'<%}%>'
+	'<th>库存</th>'
+	'<th>市场价格</th>'
+	'<th>销售价格</th>'
+	'<th>成本价格</th>'
+	'<th>重量</th>'
+	'<%if(isProduct == true){%>'
+	'<th>操作</th>'
+	'<%}%>'
+   '</tr>';



//商品内容模板
var goodsRowTemplateSource = ' <%var i=0;%>'
+   '<%for(var item in templateData){%>'
+   '<%item = templateData[item]%>'
+   '<tr class=\'td_c\'>'
+	'<td style="width:120px" ><input name="_goods_no[<%=i%>]" required="required" type="text" value="<%=item[\'goods_no\'] ? item[\'goods_no\'] : item[\'products_no\']%>" style="width:120px" /></td>'
+	'<%var isProduct = false;%>'
+	'<%var specArrayList = parseJSON(item[\'spec_array\'])%>'
+	'<%for(var result in specArrayList){%>'
+	'<%result = specArrayList[result]%>'
+	'<input type=\'hidden\' name="_spec_array[<%=i%>][]"  value="<%=result.id%>_<%=result.value%>"  />'
+	'<%isProduct = true;%>'
+	'<td style="width:80px" >'
+		'<%if(result[\'type\'] == 1){%>'
+			'<%=result[\'value\']%>'
+		'<%}else{%>'
+			'<img class="img_border" width="30px" height="30px" src="<%=result[\'value\']%>">'
+		'<%}%>'
+	'</td>'
+	'<%}%>'
+	'<td  style="width:80px" ><input  name="_store_nums[<%=i%>]" type="text" required="required" value="<%=item[\'store_nums\']?item[\'store_nums\']:100%>" style="width:80px" /></td>'
+	'<td  style="width:80px" ><input  name="_market_price[<%=i%>]" type="text" required="required" value="<%=item[\'market_price\']%>" style="width:80px" /></td>'
+	'<td style="width:80px" >'
+		'<input type=\'hidden\' name="_groupPrice[<%=i%>]" value="<%=item[\'groupPrice\']%>" style="width:80px" />'
+		'<input  name="_sell_price[<%=i%>]" type="text" required="required" value="<%=item[\'sell_price\']%>" style="width:80px" />'
+	'</td>'
+	'<td  style="width:80px" ><input  name="_cost_price[<%=i%>]" type="text" required="required" empty value="<%=item[\'cost_price\']%>" style="width:80px" /></td>'
+	'<td  style="width:80px" ><input  name="_weight[<%=i%>]" type="text" required="required" empty value="<%=item[\'weight\']%>" style="width:80px" /></td>'
+	'<%if(isProduct == true){%>'
+	'<td  style="width:50px" ><a href="javascript:void(0)" onclick="delProduct(this);"><img class="operator" src="/media/images/shop/icon_del.gif" alt="删除" /></a></td>'
+	'<%}%>'
+'  </tr>'
+'  <%i++;%>'
+'  <%}%>';



//初始化货品表格
function initProductTable(goodsHeadTemplateSource,goodsRowTemplateSource)
{
	//默认产生一条商品标题空挡
	var goodsHeadrender = template.compile(goodsHeadTemplateSource);
	var goodsHeadHtml = goodsHeadrender({'templateData':[]});
	$('#goodsBaseHead').html(goodsHeadHtml);

	//默认产生一条商品空挡
	var goodsRowrender = template.compile(goodsRowTemplateSource);
	var goodsRowHtml = goodsRowrender({'templateData':[[]]});
	$('#goodsBaseBody').html(goodsRowHtml);
}

//删除货品
function delProduct(_self)
{
	$(_self).parent().parent().remove();
	if($('#goodsBaseBody tr').length == 0)
	{
		initProductTable();
	}
}

//添加规格
function selSpec()
{
	//货品是否已经存在
	if($('input:hidden[name^="_spec_array"]').length > 0)
	{
		alert('当前货品已经存在，无法进行规格设置。如果需要重新设置规格请您手动删除当前货品');
		return;
	}

	var tempUrl = 'spropsearch.html?goods_id=@goods_id@';
	var goods_id = $('[name="id"]').val();
	tempUrl = tempUrl.replace('@goods_id@',goods_id);

	art.dialog.open(tempUrl,{
		title:'设置商品的规格',
		okVal:'保存',
		ok:function(iframeWin, topWin)
		{
			//添加的规格
			var addSpecObject = $(iframeWin.document).find('[id^="vertical_"]');
			if(addSpecObject.length == 0)
			{
				initProductTable();
				return;
			}
	
			//开始遍历规格
			var specValueData = {};
			var specData      = {};
			addSpecObject.each(function()
			{
				$(this).find('input:hidden[name="specJson"]').each(function()
				{
					var json = $.parseJSON(this.value);
					if(!specValueData[json.id])
					{
						specData[json.id]      = {'id':json.id,'name':json.name,'type':json.type};
						specValueData[json.id] = [];
					}
					specValueData[json.id].push(json['value']);
				});
			});
	
			//生成货品的笛卡尔积
			var specMaxData = descartes(specValueData,specData);
	
			//从表单中获取默认商品数据
			var productJson = {};
			$('#goodsBaseBody tr:first').find('input[type="text"]').each(function(){
				productJson[this.name.replace(/^_(\w+)\[\d+\]/g,"$1")] = this.value;
			});
	
			//生成最终的货品数据
			var productList = [];
			for(var i = 0;i < specMaxData.length;i++)
			{
				var productItem = {};
				for(var index in productJson)
				{
					//自动组建货品号
					if(index == 'goods_no')
					{
						//值为空时设置默认货号
						if(productJson[index] == '')
						{
							productJson[index] = defaultProductNo;
						}
	
						if(productJson[index].match(/(?:\-\d*)$/) == null)
						{
							//正常货号生成
							productItem['goods_no'] = productJson[index]+'-'+(i+1);
						}
						else
						{
							//货号已经存在则替换
							productItem['goods_no'] = productJson[index].replace(/(?:\-\d*)$/,'-'+(i+1));
						}
					}
					else
					{
						productItem[index] = productJson[index];
					}
				}
				productItem['spec_array'] = specMaxData[i];
				productList.push(productItem);
			}
			//创建规格标题
			var render1 = template.compile(goodsHeadTemplateSource);
			var aa = render1({'templateData':specData});
			$('#goodsBaseHead').html(aa);
			//创建货品数据表格
			var goodsRowrender1 = template.compile(goodsRowTemplateSource);
			var goodsRowHtml1 = goodsRowrender1({'templateData':productList});
			$('#goodsBaseBody').html(goodsRowHtml1);
		}
	});
}

//笛卡儿积组合
function descartes(list,specData)
{
	//parent上一级索引;count指针计数
	var point  = {};

	var result = [];
	var pIndex = null;
	var tempCount = 0;
	var temp   = [];

	//根据参数列生成指针对象
	for(var index in list)
	{
		if(typeof list[index] == 'object')
		{
			point[index] = {'parent':pIndex,'count':0}
			pIndex = index;
		}
	}

	//单维度数据结构直接返回
	if(pIndex == null)
	{
		return list;
	}

	//动态生成笛卡尔积
	while(true)
	{
		for(var index in list)
		{
			tempCount = point[index]['count'];
			temp.push({"id":specData[index].id,"type":specData[index].type,"name":specData[index].name,"value":list[index][tempCount]});
		}

		//压入结果数组
		result.push(temp);
		temp = [];

		//检查指针最大值问题
		while(true)
		{
			if(point[index]['count']+1 >= list[index].length)
			{
				point[index]['count'] = 0;
				pIndex = point[index]['parent'];
				if(pIndex == null)
				{
					return result;
				}

				//赋值parent进行再次检查
				index = pIndex;
			}
			else
			{
				point[index]['count']++;
				break;
			}
		}
	}
}
