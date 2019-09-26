<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		var obj = __GetTagObject($('#tbodyList'),'DIV');
		$(obj).height(__GetHeight($(obj)));

		lfLoadOrgNm();
		lfSearch();
	});

	function lfLoadOrgNm(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_load_info.php'
		,	data :{
				'orgNo'	:opener.orgNo
			}
		,	beforeSend:function(){
			}
		,	success:function(data){
				var col = __parseVal(data);
				$('#lblOrgNm').text(col['orgNm']);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_admin_out_detail_search.php'
		,	data :{
				'orgNo'	:opener.orgNo
			,	'year'	:opener.year
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">지출상세내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">센터명</th>
			<td class="left" id="lblOrgNm"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="300px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">월</th>
			<th class="head">계정</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="width:100%; height:100px; overflow-x:hidden;overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="50px">
			<col width="300px">
			<col width="100px">
			<col>
		</colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>