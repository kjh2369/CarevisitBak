<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');

	$svcCd = $_POST['svcCd'];
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>서비스구분</th>
			<td id="svcNm" class="left bold"></td>
			<td class="center" rowspan="5">
				<span class="btn_pack m"><button type="button" onclick="execApply();">적용</button></span><?
				if ($debug){?>
					<span class="btn_pack m"><button type="button" onclick="document.f.submit();">Re</button></span><?
				}?>
			</td>
		</tr>
		<tr>
			<th>이용상태</th>
			<td>
				<input id="use1" name="useYn" type="radio" class="radio" value="1" value1="" onclick="setReasonGbn();"><label for="use1">이용</label>
				<input id="use9" name="useYn" type="radio" class="radio useN" value="9" value1="" onclick="setReasonGbn(9);"><label for="use9" class="useN">중지</label>
			</td>
		</tr>
		<tr id="reasonTr">
			<th>중지사유</th>
			<td id="reason"></td>
		</tr>
		<tr>
			<th>계약기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="lfChkDt(this);"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="lfChkDt(this);">
				<? if(!$IsClientInfo){ ?>
					<input id="reCont" name="reCont" type="checkbox" class="checkbox reCont" onclick="setReCont();"><label for="reCont" class="reCont">재계약 </label>
				<? }else { ?>
					</br><font color="red">※ 재계약은 일자만 입력하시면 추가등록 됩니다.</font>
				<? }?>
			</td>
		</tr><?
		if ($svcCd == 'S' || $svcCd == 'R'){?>
			<tr>
				<th>관리구분</th>
				<td>
					<label><input id="optMPGbnY" name="optMPGbn" type="radio" class="radio" value="Y">중점관리</label>
					<label><input id="optMPGbnN" name="optMPGbn" type="radio" class="radio" value="N">일반</label>
				</td>
			</tr><?
		}?>
	</tbody>
</table>

<div class="title title_border">계약내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="215px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">이용상태</th>
			<th class="head">중지사유</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="center top">
				<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div>
<script type="text/javascript">
function lfChkDt(obj){
	var diffDt = 0;

	if (opener.svcCd == 6){
		//재가관리는 중복체크를 하지 않는다.
		return;
	}

	try{
		diffDt = diffDate('d',__getDate($('#fromDt').val()),__getDate($('#toDt').val()));
	}catch(e){
	}

	/*
	//바우처 계약 일자를 매달 1일과 말일로 강제 설정함.
	if (opener.svcCd >= '1' && opener.svcCd <= '4'){
		$(obj).val(__getDate($(obj).val()));

		if ($(obj).val().substr(0,7)+'-01' != $(obj).val() && $(obj).val().substr(0,7)+'-'+getLastDay($(obj).val().substr(0,7)+'-01') != $(obj).val()){
			if ($(obj).attr('id') == 'fromDt'){
				$(obj).val($(obj).val().substr(0,7)+'-01');
			}else if ($(obj).attr('id') == 'toDt'){
				$(obj).val($(obj).val().substr(0,7)+'-'+getLastDay($(obj).val().substr(0,7)+'-01'));
			}
		}
	}
	*/

	/*
	if (diffDt < 0){
		alert('계약시작일자가 계약종료일자보다 큽니다.\n확인하여 주십시오.');
		$(obj).attr('value', $(obj).attr('value1')).focus();
		return;
	}
	*/

	$.ajax({
		type: 'POST'
	,	url : './client_chk_period.php'
	,	data: {
			code  : opener.code
		,	jumin : opener.jumin
		,	svcCd : opener.svcCd
		,	seq   : opener.seq
		,	from  : __getDate($('#fromDt').val())
		,	to    : __getDate($('#toDt').val())
		,	reCont: $('#reCont').attr('checked') ? 'Y' : 'N'
		}
	,	success: function(result){
			if (!result){
			}else{
				alert(result);
				$(obj).attr('value', $(obj).attr('value1')).focus();
			}
		}
	}).responseXML;
}
</script>
<?
	include_once('../inc/_db_close.php');
?>