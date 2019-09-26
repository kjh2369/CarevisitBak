<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		var obj = __GetTagObject($('#tbodyList'),'DIV');
		$(obj).height(__GetHeight(obj));

		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_charge_acct_list.php'
		,	data :{
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

	function lfSel(cd,nm){
		opener.cd = cd;
		opener.nm = nm;

		self.close();
	}
</script>
<div class="title title_border">계정코드 리스트</div><?
$colgroup = '
	<col width="40px">
	<col width="60px">
	<col width="200px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">계정코드</th>
			<th class="head">명칭</th>
			<th class="head">비고</th>
		</tr>
	</thead>
</table>
<div style="width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="tbodyList"></tbody>
	</table>
</div>
<?
	include_once('../inc/_footer.php');
?>