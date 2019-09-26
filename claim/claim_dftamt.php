<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'../claim/dftamt_search.php'
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#TBL_LIST tbody').html(html);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(yymm){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'../claim/dftamt_del.php'
		,	data :{
				'yymm':yymm
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					lfSearch();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">미납내역</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="90px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">청구년월</th>
			<th class="head">사용년월</th>
			<th class="head">미납금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="overflow-x:hidden; overflow-y:scroll; height:234px;">
	<table id="TBL_LIST" class="my_table" style="width:100%;">
		<colgroup>
			<col width="80px">
			<col width="90px">
			<col width="80px">
			<col>
		</colgroup>
		<tbody></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>