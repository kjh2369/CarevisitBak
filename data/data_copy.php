<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);

		$('input:radio[type="radio"]').unbind('click').bind('click',function(){
			$('#txt'+$(this).val()+'Dt')
				.attr('disabled',$('#opt'+$(this).val()+'Y').attr('checked')?false:true)
				.css('background-color',$('#opt'+$(this).val()+'Y').attr('checked')?'#ffffff':'#efefef');
		});

		$('#optQuitN').click();
		$('#optJoinN').click();
		$('#optEndN').click();
		$('#optUseN').click();
		$('#optNowCN').click();
		$('#optNewCN').click();
	});

	function lfFindCenter(obj){
		var objModal = new Object();
		var url      = '../find/_find_center.php';
		var style    = 'dialogWidth:800px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.mode = '99';
		objModal.code = '';
		objModal.name = '';

		window.showModalDialog(url, objModal, style);

		if (!objModal.code) return;

		$(obj).attr('code',objModal.code).text(objModal.name+'('+objModal.code+')');
	}

	function lfCopy(){
		if (!$('#lblCenter').attr('code')){
			alert('기관을 선택하여 주십시오.');
			lfFindCenter($("#lblCenter"));
			return;
		}

		if (!$('#lblTarget').attr('code')){
			alert('대상기관을 선택하여 주십시오.');
			lfFindCenter($("#lblTarget"));
			return;
		}

		if ($('#optQuitY').attr('checked') && !$('#txtQuitDt').val()){
			$('#txtQuitDt').focus();
			alert('퇴사일자를 입력하여 주십시오.');
			return;
		}

		if ($('#optJoinY').attr('checked') && !$('#txtJoinDt').val()){
			$('#txtJoinDt').focus();
			alert('입사일자를 입력하여 주십시오.');
			return;
		}

		$.ajax({
			type :'POST'
		,	async:false
		,	url  :'./data_copy_save.php'
		,	data :{
				'code1':$('#lblCenter').attr('code')
			,	'code2':$('#lblTarget').attr('code')
			,	'quitYn':$('#optQuitY').attr('checked') ? 'Y' : 'N'
			,	'quitDt':$('#txtQuitDt').val()
			,	'joinYn':$('#optJoinY').attr('checked') ? 'Y' : 'N'
			,	'joinDt':$('#txtJoinDt').val()
			,	'endYn':$('#optEndY').attr('checked') ? 'Y' : 'N'
			,	'endDt':$('#txtEndDt').val()
			,	'useYn':$('#optUseY').attr('checked') ? 'Y' : 'N'
			,	'useDt':$('#txtUseDt').val()
			,	'nowCYn':$('#optNowCY').attr('checked') ? 'Y' : 'N'
			,	'nowCDt':$('#txtNowCDt').val()
			,	'newCYn':$('#optNewCY').attr('checked') ? 'Y' : 'N'
			,	'newCDt':$('#txtNewCDt').val()
			}
		,	beforeSend: function(){
			}
		,	success: function(result){
				alert(result);
			}
		});
	}
</script>
<div class="title title_border">기관데이타관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="50px">
		<col width="55px">
		<col width="245px">
		<col width="55px">
		<col width="245px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head"></th>
			<th class="head" colspan="2">현재기관</th>
			<th class="head" colspan="2">대상기관</th>
			<th class="head last"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>기관명</th>
			<td class="left" style="padding-top:1px;" colspan="2">
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindCenter($('#lblCenter'));"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblCenter" code=""></span></div>
			</td>
			<td class="left" style="padding-top:1px;" colspan="2">
				<div style="float:left; width:auto; height:25px;"><span class="btn_pack m find" onclick="lfFindCenter($('#lblTarget'));"></span></div>
				<div style="float:left; width:auto; line-height:25px;"><span id="lblTarget" code=""></span></div>
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th rowspan="2">직원</th>
			<th>퇴사처리</th>
			<td>
				<input id="optQuitY" name="optQuit" type="radio" value="Quit" class="radio"><label for="optQuitY">예</label>
				<input id="optQuitN" name="optQuit" type="radio" value="Quit" class="radio" checked><label for="optQuitN">아니오</label>
			</td>
			<th>입사처리</th>
			<td>
				<input id="optJoinY" name="optJoin" type="radio" value="Join" class="radio"><label for="optJoinY">예</label>
				<input id="optJoinN" name="optJoin" type="radio" value="Join" class="radio" checked><label for="optJoinN">아니오</label>
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>퇴사일자</th>
			<td>
				<input id="txtQuitDt" name="txt" type="text" value="" class="date">로 퇴사처리
			</td>
			<th>입사일자</th>
			<td>
				<input id="txtJoinDt" name="txt" type="text" value="" class="date">로 입사처리
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th rowspan="2">고객</th>
			<th>중지적용</th>
			<td>
				<input id="optEndY" name="optEnd" type="radio" value="End" class="radio"><label for="optEndY">예</label>
				<input id="optEndN" name="optEnd" type="radio" value="End" class="radio" checked><label for="optEndN">아니오</label>
			</td>
			<th>이용적용</th>
			<td>
				<input id="optUseY" name="optUse" type="radio" value="Use" class="radio"><label for="optUseY">예</label>
				<input id="optUseN" name="optUse" type="radio" value="Use" class="radio" checked><label for="optUseN">아니오</label>
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>중지일자</th>
			<td>
				<input id="txtEndDt" name="txt" type="text" value="" class="date">로 중지
			</td>
			<th>이용일자</th>
			<td>
				<input id="txtUseDt" name="txt" type="text" value="" class="date">로 이용
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th rowspan="2">일정</th>
			<th>일정삭제</th>
			<td>
				<input id="optNowCY" name="optNowC" type="radio" value="NowC" class="radio"><label for="optNowCY">예</label>
				<input id="optNowCN" name="optNowC" type="radio" value="NowC" class="radio" checked><label for="optNowCN">아니오</label>
			</td>
			<th>일정복사</th>
			<td>
				<input id="optNewCY" name="optNewC" type="radio" value="NewC" class="radio"><label for="optNewCY">예</label>
				<input id="optNewCN" name="optNewC" type="radio" value="NewC" class="radio" checked><label for="optNewCN">아니오</label>
			</td>
			<td class="last"></td>
		</tr>
		<tr>
			<th>삭제일자</th>
			<td>
				<input id="txtNowCDt" name="txt" type="text" value="" class="date"> 이후 일정을 삭제합니다.
			</td>
			<th>이용일자</th>
			<td>
				<input id="txtNewCDt" name="txt" type="text" value="" class="date"> 이후 일정부터 복사합니다.
			</td>
			<td class="last"></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="6">
				<span class="btn_pack m"><button type="button" onclick="lfCopy();">복사시작</button></span>
			</td>
		</tr>
	</tfoot>
</table>