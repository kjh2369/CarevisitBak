<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./care_svc_category_search.php'
		,	data:{
				'SR':opener.SR
			,	'show':'LIST'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#ID_CATEGORY').html(html);
				$('tr',$('#ID_CATEGORY')).unbind('mouseover').bind('mouseover',function(){
					$(this).css('background-color','EAEAEA');
				}).unbind('mouseout').bind('mouseout',function(){
					$(this).css('background-color','FFFFFF');
				}).unbind('click').bind('click',function(){
					opener.code = $(this).attr('code');

					if ($(this).attr('code') != 'ALL'){
						opener.name = lfCategoryFullname($(this).attr('code'));
					}else{
						opener.name = '';
					}
					self.close();
				});
			}
		,	error:function(error){
				alert('err');
			}
		}).responseXML;
	}

	function lfCategoryFullname(code){
		var name = '';

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./care_svc_category_fun.php'
		,	data :{
				'SR'	:opener.SR
			,	'gbn'	:'FULLNAME'
			,	'code'	:code
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				name = data;
			}
		,	error:function(){
			}
		}).responseXML;

		return name;
	}
</script>
<div class="title title_border">카테고리 찾기</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">카테고리명칭</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody id="ID_CATEGORY"></tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>