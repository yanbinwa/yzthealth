//删除用户,代理,员工等操作
/*function delete(id,o){
		var ids = [];
		$('td').find('input[type="checkbox"]:checked').each(function(){
			ids[ids.length] = $(this).val();
		});
		if(ids.length == 0){
			tusi('请选择！');
			return false;
		}
		if(confirm('确定删除吗？')){
			ajax('delete-del-'+id+'.html',{ id:ids.join(',')},function(data){
				if(data == 'ok'){
					tusi('操作成功');
					setTimeout(function(){
						goto(location.href);
					},500);
				}
			});	
		}
}
*/
$(function(){
	var picjk = setInterval(function(){
		if($('#picsethere').find('img').size()>0){
			$('#picsethere').find('img').each(function(){
				$(this).error(function(){
					$(this).attr('src','/res/demopic.jpg');
				});
				$(this).attr('src',$(this).attr('src'));
			});
			clearInterval(picjk);
		}
	},500);
});