<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;

		var obj = __GetTagObject($('#tbodyList'),'DIV');
		$(obj).height(__GetHeight($(obj)) - 25);

		$('input:text').each(function(){
			__init_object(this);
		});

		lfSearch();
	});

	window.onunload = function(){
		var val = {};

		for(var i=1; i<=6; i++){
			val['amt'+i] = 0;
			val['cnt'+i] = 0;
		}
		val['amtX'] = 0;
		val['cntX'] = 0;

		$('tr',$('#tbodyList')).each(function(){
			if ($('td',this).eq(1).text() == '기타매출'){
				val['amt1'] += __str2num($('td',this).eq(3).text());
				val['cnt1'] ++;
			}else if ($('td',this).eq(1).text() == '치매수당매출'){
				val['amt2'] += __str2num($('td',this).eq(3).text());
				val['cnt2'] ++;
			}else if ($('td',this).eq(1).text() == '사복가산금매출'){
				val['amt3'] += __str2num($('td',this).eq(3).text());
				val['cnt3'] ++;
			}else if ($('td',this).eq(1).text() == '매출미수'){
				val['amtX'] += __str2num($('td',this).eq(3).text());
				val['cntX'] ++;
			}else if ($('td',this).eq(1).text() == '장기근속수당'){
				val['amt4'] += __str2num($('td',this).eq(3).text());
				val['cnt4'] ++;
			}else if ($('td',this).eq(1).text() == '치매관리자'){
				val['amt5'] += __str2num($('td',this).eq(3).text());
				val['cnt5'] ++;
			}else if ($('td',this).eq(1).text() == '장기근속관리자'){
				val['amt6'] += __str2num($('td',this).eq(3).text());
				val['cnt6'] ++;
			}
		});

		opener.win.lfSetIn(__str2num(opener.month), val);
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_salary_other_search.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			}
		,	beforeSend:function(){
			}
		,	success:function(html){
				$('#tbodyList').html(html);

				var totamt = 0;

				$('#tbodyList tr').each(function(){
					totamt += __str2num($('td', this).eq(3).text());
				});

				$('#totamt').text(__num2str(totamt));
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
		,	url  :'./bm_salary_other_save.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'gbn'	:$('#cboGbn').val()
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
		,	url  :'./bm_salary_other_remove.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'seq'	:seq
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
<div class="title title_border">기타수입 등록 및 수정</div><?
$colgroup = '
	<col width="40px">
	<col width="150px">
	<col width="200px">
	<col width="100px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">구분</th>
			<th class="head">명칭</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center">
				<select id="cboGbn" style="float:left; width:auto;">
					<option value="2">치매수당매출</option>
					<option value="5">치매관리자</option>
					<option value="3">사복가산금매출</option>
					<option value="4">장기근속수당</option>
					<option value="6">장기근속관리자</option>
					<option value="1">기타매출</option>
					<!--option value="X">매출미수</option-->
				</select>
			</td>
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
<table class="my_table" style="width:100%; border-top:1px solid #cccccc;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center">합계</td>
			<td class="center"></td>
			<td class="center"><div id="totamt" class="right">0</div></td>
			<td class="center"></td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>