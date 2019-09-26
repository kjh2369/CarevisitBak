<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
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
			<td class="left">
				<span id="lvlNm"></span>
				<input id="mgmtLvl" name="mgmtLvl" type="hidden" value="">
				<input id="mgmtSeq" name="mgmtSeq" type="hidden" value="">
			</td>
			<th>한도금액</th>
			<td class="left">
				<span id="limitPay"></span>
				<input id="mgmtPay" name="mgmtPay" type="hidden" value="">
			</td>
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
			<th>적용기간</th>
			<td colspan="3">
				<input id="fromDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setLimitAmt();"> ~
				<input id="toDt" name="dt[]" type="text" value="" value1="" class="date" onchange="setLimitAmt();">
				<? if(!$IsClientInfo){ ?><input id="expenseModify" name="expenseModify" type="checkbox" class="checkbox expenseModify" onclick="setDtEnabled(this,true);"><label for="expenseModify" class="expenseModify">재등록</label>
				<? }else { ?>
					</br><font color="red">※ 재등록은 일자만 입력하시면 추가등록 됩니다.</font>
				<? }?>
			</td>
		</tr>
		<tr>
			<th>수급자구분</th>
			<td colspan="3">
				<label><input id="expenseKind3" name="expenseKind" type="radio" class="radio" value="3" value1="" onclick="setExpenseRate();">기초(0%)</label>
				<label style="display:none;"><input id="expenseKind2" name="expenseKind" type="radio" class="radio" value="2" value1="" tags="6" onclick="setExpenseRate();">의료(7.5%)</label>
				<label style="display:none;"><input id="expenseKind7" name="expenseKind" type="radio" class="radio" value="2" value1="" tags="4" onclick="setExpenseRate();">의료(6%)</label>
				<!--label style="display:none;"><input id="expenseKind8" name="expenseKind" type="radio" class="radio" value="2" value1="" tags="5" onclick="setExpenseRate();">의료(9%)</label-->
				<label style="display:none;"><input id="expenseKind4" name="expenseKind" type="radio" class="radio" value="4" value1="" tags="3" onclick="setExpenseRate();" >경감(7.5%)</label>
				<label style="display:none;"><input id="expenseKind5" name="expenseKind" type="radio" class="radio" value="4" value1="" tags="1" onclick="setExpenseRate();">경감(6%)</label>
				<label style="display:none;"><input id="expenseKind6" name="expenseKind" type="radio" class="radio" value="4" value1="" tags="2" onclick="setExpenseRate();">경감(9%)</label>
				<label ><input style="margin-left:0;" id="expenseKind1" name="expenseKind" type="radio" class="radio" value="1" value1="" onclick="setExpenseRate();">일반(15%)</label>
			</td>
		</tr>
		<tr>
			<th>본인부담율</th>
			<td><input id="expenseRate" name="expenseRate" type="text" value="" class="number" style="width:50px;" maxlength="3" onkeydown="__onlyNumber(this,'.');" onchange="setExpenseAmt();"></td>
			<th>본인부담금</th>
			<td class="left"><span id="expenseAmt"></span></td>
		</tr>
	</tbody>
</table>

<input id="seq" name="seq" type="hidden" value="0">

<div class="title title_border">계약내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="70px">
		<col width="40px">
		<col width="70px">
		<col width="50px">
		<col width="50px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">등급</th>
			<th class="head">한도금액</th>
			<th class="head">구분</th>
			<th class="head">부담율</th>
			<th class="head">부담금</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="8" class="center top">
				<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>