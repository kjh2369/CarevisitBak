<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");

	/*
	 * 기능		: 기관등록 / 수정
	 * 작성자	: 김재용
	 * 일자		: 2011.03.21
	 */

	 include_once('income_var.php');
?>
<style>
.div1{
	width:33%;
	margin-top:3px;
	float:left;
}
</style>
<script language='javascript'>
<!--
window.onload = function(){
	__init_form(document.f);
}
//-->
</script>
<script type="text/javascript" src="../js/acct.js"></script>
<form name="f" method="post">
<div class="title">입금내역등록</div>
<?
	include('income_head.php');
?>
<table id="my_table" class="my_table my_border" style="margin-top:-1px;">
	<colgroup>
		<col width="30px">
		<col width="80px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head"><input name="check_all" type="checkbox" class="checkbox" onclick="__checkMyValue('check[]', this.checked);"></th>
			<th class="head">지출일자</th>
			<th class="head">지출내용</th>
			<th class="head">부가세구분</th>
			<th class="head">금액</th>
			<th class="head">부가세</th>
			<th class="head">합계</th>
			<th class="head">사업자등록번호</th>
			<th class="head">업태</th>
			<th class="head">업종</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="row_1">
		<tr>
			<td class="center">
				<input name="check[]" type="checkbox" class="checkbox" value="1">
			</td>
			<td class="center">
				<input name="date[]" type="text" value="" maxlength="8" class="date" onKeyDown="return _income_check_date(this, 1);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" alt="tag" tag="__checkRow(1, 'check[]');">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="amount[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onkeydown="return _income_check_amount(this, 1);" onfocus="__commaUnset(this);" onblur="__commaSet(this);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="add"></span><button type="button" onClick="_io_add_row('my_table', 'row_1');">추가</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include('income_head.php');
?>
</form>
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>