<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
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
<?
	include_once('income_var.php');
?>
<div class="title"><?=$io_title;?>내역등록</div>
<?
	include('income_head.php');
?>
<table id="my_table" class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="80px">
		<col width="130px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="110px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head"><input name="check_all" type="checkbox" class="checkbox" onclick="__checkMyValue('check[]', this.checked);"></th>
			<th class="head"><?=$io_title;?>일자</th>
			<th class="head"><?=$io_title;?>내용</th>
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
				<input name="date[]" type="text" value="" maxlength="8" class="date" onKeyDown="return _income_check_date(this, 1);" onFocus="__replace(this, '-', '');" onBlur="__getDate(this);" onClick="_carlendar(this);" alt="tag" tag="_item_focus(1, 'check[]');">
			</td>
			<td class="center">
				<input name="item[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="vat_0" type="radio" class="radio" style="margin:0;" value="Y" onclick="_set_vat(1);" onkeydown="__enterFocus();">유
				<input name="vat_0" type="radio" class="radio" style="margin:0;" value="N" onclick="_set_vat(1);" onkeydown="__enterFocus();" checked>무
			</td>
			<td class="center">
				<input name="amount[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" onkeydown="return _income_check_amount(this, 1);" alt="onblur" tag="_set_vat(1);">
			</td>
			<td class="center">
				<input name="vat[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" readonly>
			</td>
			<td class="center">
				<input name="tot_amt[]" type="text" value="0" maxlength="15" class="number" style="width:100%;" readonly>
			</td>
			<td class="center">
				<input name="taxid[]" type="text" value="" style="width:100%;" maxlength="10" onkeydown="return _income_check_item(this, 1);" alt="taxid">
			</td>
			<td class="center">
				<input name="biz_group[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, 1);">
			</td>
			<td class="center">
				<input name="biz_type[]" type="text" value="" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="if(event.keyCode==13){_income_next(1);}">
			</td>
			<td class="center last">
				<span class="btn_pack m"><button type="button" onClick="_income_add_row('my_table', 'row_1');">추가</button></span>
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