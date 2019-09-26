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

		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	window.onunload = function(){
		var val = 0;

		$('tr',$('#tbodyList')).each(function(){
			val += __str2num($('td',this).eq(2).text());
		});

		opener.win.lfSetIE('I', opener.month, val);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_compay_import_search.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
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

	function lfSave(){
		if (!$('#txtName').val()){
			$('#txtName').focus();
			alert('명칭을 입력하여 주십시오.');
			return;
		}

		if (!$('#txtAmt').val()){
			$('#txtAmt').focus();
			alert('금액을 입력하여 주십시오.');
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./bm_compay_import_set.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'gbn'	:'I'
			,	'name'	:$('#txtName').val()
			,	'amt'	:$('#txtAmt').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('#txtName').val('')
					$('#txtAmt').val('0')

					lfSearch();
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfRemove(seq){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./bm_compay_import_remove.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'seq'	:seq
			,	'gbn'	:'I'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					lfSearch();
				}else if (result == 9){
					alert('데이타 전송중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">본사수입 등록 및 수정</div><?
$colgroup = '
	<col width="40px">
	<col width="200px">
	<col width="100px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">명칭</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center">
				<input id="txtName" type="text" value="" style="width:100%;">
			</td>
			<td class="center">
				<input id="txtAmt" type="text" value="" class="number" style="width:100%;">
			</td>
			<td class="center">
				<span class="btn_pack small" style="float:left; margin-left:5px;"><button onclick="lfSave();">저장</button></span>
			</td>
		</tr>
	</tbody>
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