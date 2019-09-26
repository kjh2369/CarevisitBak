<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<script type="text/javascript">
	if ('<?=$lbLimitSet;?>' == '1'){
		$(document).ready(function(){
			/*********************************************************
				현재 수급자 등급 및 한도금액
			*********************************************************/
			$.ajax({
				type: "POST",
				url : "./client_pop_fun.php",
				data: {
					code  : opener.code
				,	jumin : opener.jumin
				,	svcCd : opener.svcCd
				,	para  : 'from='+$('#fromDt').val()+'&to='+$('#toDt').val()
				,	mode  : 5
				},
				beforeSend: function (){
				},
				success: function (result){
					var val = __parseStr(result);

					if (val['seq'] != '') abVal = true;

					if (abVal){
						if (val['seq'] != ''){
							$('#mgmtLvl').text(__lvlNm(val['level']));
							$('#mgmtPay').text(__num2str(val['limit']));

							setKind();
						}else{
							$('#mgmtLvl').text(__lvlNm(opener.lvl));
							$('#mgmtPay').text(__num2str(opener.maxPay));
							$('#expenseKind').text(__kindNm(opener.kind));
							$('#expenseAmt').text(__num2str(cutOff(__str2num(opener.maxPay) * __str2num(opener.rate) * 0.01)));
							$('#expenseRate').text('['+opener.rate+'%]');
						}
					}else{
						$('#mgmtLvl').text(__lvlNm(opener.lvl));
						$('#mgmtPay').text(__num2str(opener.maxPay));
						$('#expenseKind').text(__kindNm(opener.kind));
						$('#expenseAmt').text(__num2str(cutOff(__str2num(opener.maxPay) * __str2num(opener.rate) * 0.01)));
						$('#expenseRate').text('['+opener.rate+'%]');

						$('#fromDt').val(opener.from).attr('value1',opener.from);
						$('#toDt').val(opener.to).attr('value1',opener.to);
						//$('#limitAmt').val(__num2str(opener.amt)).attr('value1',opener.amt);
					}
				},
				error: function (){
				}
			}).responseXML;
		});

		$('input:text[name="txtLimit"]').unbind('change').bind('change',function(){
			var amt = 0;

			amt += __str2num($('#txtLimitCare').val());
			amt += __str2num($('#txtLimitBath').val());
			amt += __str2num($('#txtLimitNurse').val());

			$('#lblLimitTot').text(__num2str(amt));
		});
	}
</script>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="130px">
		<col width="70px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>등급</th>
			<td class="left"><span id="mgmtLvl"></span></td>
			<th>한도금액</th>
			<td class="left"><span id="mgmtPay"></span></td>
			<td class="center" rowspan="4">
				<span class="btn_pack m"><button type="button" onclick="execApply();">적용</button></span>
				<?
					if ($debug){?>
						<span class="btn_pack m"><button type="button" onclick="document.f.submit();">Re</button></span><?
					}
				?>
			</td>
		</tr>
		<tr>
			<th>수급자구분</th>
			<td class="left"><span id="expenseKind"></span></td>
			<th>본인부담금</th>
			<td class="left">
				<span id="expenseAmt"></span>
				<span id="expenseRate"></span>
			</td>
		</tr>
		<tr>
			<th>적용기간</th>
			<td colspan="3">
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setLimit(true);"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setLimit(true);">
				<? if(!$IsClientInfo){ ?><input id="limitModify" name="limitModify" type="checkbox" class="checkbox limitModify" onclick="setDtEnabled(this,true);"><label for="limitModify" class="limitModify">재등록</label>
				<? }else { ?>
					</br><font color="red">※ 재등록은 일자만 입력하시면 추가등록 됩니다.</font>
				<? }?>
			</td>
		</tr>
		<tr><?
		if ($lbLimitSet){?>
			<th>청구설정</th>
			<td class="top" colspan="3">
				<table class="my_table" style="width:100%;">
					<colgroup>
						<col width="85px" span="3">
						<col>
					</colgroup>
					<thead>
						<tr>
							<th class="head">방문요양</th>
							<th class="head">방문목욕</th>
							<th class="head">방문간호</th>
							<th class="head last">한도총액</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="center bottom"><input id="txtLimitCare" name="txtLimit" type="text" value="0" value1="0" class="number" style="width:100%;"></td>
							<td class="center bottom"><input id="txtLimitBath" name="txtLimit" type="text" value="0" value1="0" class="number" style="width:100%;"></td>
							<td class="center bottom"><input id="txtLimitNurse" name="txtLimit" type="text" value="0" value1="0" class="number" style="width:100%;"></td>
							<td class="center bottom last"><div id="lblLimitTot" class="right">0</div></td>
						</tr>
					</tbody>
				</table>
			</td><?
		}else{?>
			<th>청구한도</th>
			<td colspan="3"><input id="limitAmt" name="limitAmt" type="text" value="0" value1="0" class="number" style="width:70px;"></td><?
		}?>
		</tr>
	</tbody>
</table>

<input id="seq" name="seq" type="hidden" value="0">

<div class="title title_border">계약내역</div>
<?
	if ($lbLimitSet){?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="70px" span="2">
				<col width="65px" span="4">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<th class="head">방문요양</th>
					<th class="head">방문목욕</th>
					<th class="head">방문간호</th>
					<th class="head">한도총액</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="7" class="center top">
						<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
					</td>
				</tr>
			</tbody>
		</table><?
	}else{?>
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="70px">
				<col width="70px">
				<col width="80px">
				<col>
			</colgroup>
			<thead>
				<tr>
					<th class="head">적용일</th>
					<th class="head">종료일</th>
					<th class="head">한도금액</th>
					<th class="head">비고</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="4" class="center top">
						<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
					</td>
				</tr>
			</tbody>
		</table><?
	} ?>

<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>