<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$SR = $_SESSION['HCE_SR'];
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		var top = $('#divBody').offset().top;
		var height = $(this).height();
		var h = height - top - 3;

		$('#divBody').height(h);
		$('#lblName').text(opener.parm['name']);

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./care_svc_history_search.php'
		,	data:{
				'jumin':opener.parm['jumin']
			,	'name':opener.parm['name']
			,	'from':opener.parm['from']
			,	'to':opener.parm['to']
			,	'SR':'<?=$SR;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);
			}
		,	complite:function(result){
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">대상자 서비스이력(중복확인)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>대상자명</th>
			<td id="lblName" class="left last"></td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="130px">
		<col width="80px" span="2">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">서비스</th>
			<th class="head">시작일자</th>
			<th class="head">종료일자</th>
			<th class="head">기관</th>
			<th class="head">중복</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div id="divBody" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="130px">
			<col width="80px" span="2">
			<col width="50px">
			<col width="50px">
			<col>
		</colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>