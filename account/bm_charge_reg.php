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

		val['amt'] = 0;
		val['cnt'] = 0;

		$('tr',$('#tbodyList')).each(function(){
			val['amt'] += __str2num($('td',this).eq(2).text());
			val['cnt'] ++;
		});

		opener.win.lfSetOut(__str2num(opener.month), val);
	}

	function lfShowAcct(){
		var objModal = new Object();
		var url = './bm_charge_acct.php';
		var style = 'dialogWidth:600px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win = window;
		objModal.cd = '';
		objModal.nm = '';

		window.showModalDialog(url, objModal, style);

		if (objModal.cd){
			$('#lblAcctCd').attr('cd',objModal.cd).text(objModal.cd+' - '+objModal.nm);
		}
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./bm_charge_reg_search.php'
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
					totamt += __str2num($('td', this).eq(2).text());
				});

				$('#totamt').text(__num2str(totamt));
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		if (!$('#lblAcctCd').attr('cd')){
			alert('계정코드를 선택하여 주십시오.');
			lfShowAcct();
			if (!$('#lblAcctCd').attr('cd')) return;
		}

		if (!$('#txtAmt').val()){
			$('#txtAmt').focus();
			alert('금액을 입력하여 주십시오.');
			return;
		}

		$.ajax({
			type :'POST'
		,	url  :'./bm_charge_save.php'
		,	data :{
				'year'	:opener.year
			,	'month'	:opener.month
			,	'acct'	:$('#lblAcctCd').attr('cd')
			,	'amt'	:$('#txtAmt').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					$('#lblAcctCd').attr('cd','').text('');
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
		,	url  :'./bm_charge_remove.php'
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
<div class="title title_border">일반경비 등록 및 수정</div><?
$colgroup = '
	<col width="40px">
	<col width="300px">
	<col width="100px">
	<col>';?>
<table class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">계정코드</th>
			<th class="head">금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center">-</td>
			<td class="center" style="background-color:#FAF4C0;">
				<div id="lblAcctCd" class="left" onclick="lfShowAcct();" cd=""></div>
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
			<td class="center"><div id="totamt" class="right">0</div></td>
			<td class="center"></td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>