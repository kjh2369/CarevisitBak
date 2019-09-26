<?
	##################################################################
	#
	# 상태변화
	#
	##################################################################
?>
<script language='javascript'>
<!--

function go_stat_list(){
	$.ajax({
		type:'POST'
	,	url :'./stat_list.php'
	,	data:{
			'jumin':$('#jumin').val()
		}
	,	success:function(data){
			$('#svc_stat').html(data);
			set_button(2);
		}
	}).responseXML;
}

function go_stat_reg(regDt){
	$.ajax({
		type:'POST'
	,	url :'./stat_reg.php'
	,	data:{
			'jumin':$('#jumin').val()
		,	'regDt':regDt
		}
	,	success:function(data){
			$('#svc_stat').html(data);
			set_button(3);
			__init_form(document.f);
		}
	}).responseXML;
}

function go_stat_del(regDt){
	if (!confirm('삭제 후 북구가 불가능합니다.\n정말로 삭제하시겠습니까?')) return;

	$.ajax({
		type:'POST'
	,	url :'./stat_fun.php'
	,	data:{
			'jumin':$('#jumin').val()
		,	'regDt':regDt
		,	'type' :'DELETE'
		}
	,	success:function(result){
			if (result == 1){
				alert('정상적으로 처리되었습니다.');
				go_stat_list();
			}else if (result == 9){
				alert('데이타 삭제중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
			}else{
				alert(result);
			}
		}
	}).responseXML;
}

function go_stat_show(regDt){
	var	arguments = 'root=sugupja'
				  + '&dir=P'
				  + '&fileName=stat'
				  + '&fileType=pdf'
				  + '&target=show.php'
				  + '&showForm='
				  + '&code='+$('#code').val()
				  + '&jumin='+$('#jumin').val()
				  + '&regDt='+regDt
				  + '&param=';

	__printPDF(arguments);
}

function show_counsel_stat(){
	//go_stat_list();
	//show_svc_layer('stat');
}

-->
</script>

<div id="svc_stat" style="display:none;">

</div>