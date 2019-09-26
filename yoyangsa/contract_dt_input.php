<?
	include("../inc/_db_open.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$ssn = $ed->de($_POST['m_cd']);
	$report_id = $_POST['report_id'];
	$dt = $_POST['dt'];
?>

<table class="my_table my_green" style="width:230px;">
	<colgroup>
		<col width="80px">
		<col width="80px">
		<col width="70px">
	</colgroup>
	<tr>
		<th>계약일자</th>
		<td><input class="date" name="contract_dt" type="text" value="<?=$myF->dateStyle($dt)?>" onFocus="__replace(this, '-', '');" onclick="_carlendar(this);" onBlur="__getDate(this);" onkeydown="__onlyNumber(this);" maxlength="8"/></td>
		<td style="text-align:center;"><span class="btn_pack m"><span class="word"></span><button type="button" onclick="_contract_report_show('<?=$report_id?>','<?=$myF->dateStyle($dt)?>','<?=$ed->en($ssn)?>');">출력</button></span></td>
	</tr>
</table>
<input name="report_id" type="hidden" value="<?=$report_id?>">
<input name="para_dt"   type="hidden" value="">
<input name="para_m_cd" type="hidden" value="">
