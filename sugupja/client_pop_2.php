<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
		<col width="70px">
	</colgroup>
	<tbody>
		<tr>
			<th>인정번호</th>
			<td>
				<input id="mgmtNo" name="mgmtNo" type="text" value="" style="ime-mode:inactive; width:85px;" maxlength="11" onkeyup="this.value=this.value.toUpperCase();">
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
			<th>유효기간</th>
			<td>
				<input id="fromDt" name="dt[]" type="text" value="" class="date" onchange="setLimitPay();"> ~
				<input id="toDt" name="dt[]" type="text" value="" class="date" onchange="setLimitPay();">
				<? if(!$IsClientInfo){ ?><input id="mgmtModify" name="mgmtModify" type="checkbox" class="checkbox mgmtModify" onclick="setDtEnabled(this,true);"><label for="mgmtModify" class="mgmtModify">재발급</label>
				<? }else { ?>
					</br><font color="red">※ 재발급은 일자만 입력하시면 추가등록 됩니다.</font>
				<? }?>
			</td>
		</tr>
		<tr>
			<th>등급</th>
			<td>
				<label><input id="lvl1" name="lvl" type="radio" class="radio" value="1" value1="" onclick="setLimitPay();">1등급</label>
				<label><input id="lvl2" name="lvl" type="radio" class="radio" value="2" value1="" onclick="setLimitPay();">2등급</label>
				<label><input id="lvl3" name="lvl" type="radio" class="radio" value="3" value1="" onclick="setLimitPay();">3등급</label>
				<label style="display:none;"><input id="lvl4" name="lvl" type="radio" class="radio" value="4" value1="" onclick="setLimitPay();">4등급</label>
				<label style="display:none;"><input id="lvl5" name="lvl" type="radio" class="radio" value="5" value1="" onclick="setLimitPay();">5등급</label>
				<label><input id="lvl9" name="lvl" type="radio" class="radio" value="9" value1="" onclick="setLimitPay();">일반</label>
				<label style="display:none;"><input id="lvlA" name="lvl" type="radio" class="radio" value="A" value1="" onclick="setLimitPay();">인지지원등급</label>
			</td>
		</tr>
		<tr>
			<th>한도금액</th>
			<td class="left">
				<span id="limitPay"></span>
			</td>
		</tr>
	</tbody>
</table>

<input id="seq" name="seq" type="hidden" value="0">

<div class="title title_border">계약내역</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">인정번호</th>
			<th class="head">적용일</th>
			<th class="head">종료일</th>
			<th class="head">등급</th>
			<th class="head">한도금액</th>
			<th class="head">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="6" class="center top">
				<div id="tblList" style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100px;"></div>
			</td>
		</tr>
	</tbody>
</table>
<!--div align="center" style="margin-top:5px;"><span class="btn_pack m"><button type="button" onclick="lfClose();">닫기</button></span></div-->
<?
	include_once('../inc/_db_close.php');
?>