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
<div class="title"><?=$io_title;?>내역수정</div>
<?
	include('income_head.php');
?>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="30px">
		<col width="80px">
		<col>
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="110px">
		<col width="70px">
		<col width="70px">
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
			<th class="head last">업종</th>
		</tr>
	</thead>
	<tbody>
	<?
		$sql = "select *
				  from center_".$io_table."
				 where org_no = '$find_center_code'
				   and ".$io_table."_acct_dt like '".$year."-".$month."%'
				   and del_flag = 'N'
				 order by ".$io_table."_acct_dt desc";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$row = $conn->select_row($i);
				$seq = $i + 1;
			?>	<tr>
					<td class="center">
						<input name="check[]" type="checkbox" class="checkbox" value="<?=$seq;?>" onkeydown="__enterFocus();">
					</td>
					<td class="center">
						<input name="date[]" type="text" value="<?=$row[$io_table.'_acct_dt'];?>" temp="<?=$row[$io_table.'_acct_dt'];?>" maxlength="8" class="date" onKeyDown="return _income_check_date(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');" onClick="_carlendar(this);" alt="tag" tag="_item_focus(<?=$seq;?>, 'check[]');">
					</td>
					<td class="center">
						<input name="item[]" type="text" value="<?=$row[$io_table.'_item'];?>" temp="<?=$row[$io_table.'_item'];?>" style="width:100%;" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');">
					</td>
					<td class="center">
						<input name="vat_<?=$i;?>" type="radio" class="radio" style="margin:0;" value="Y" temp="<?=$row['vat_yn'];?>" onclick="_set_vat(<?=$seq;?>);" onkeydown="__enterFocus();" <? if($row['vat_yn'] == 'Y'){?>checked<?} ?>>유
						<input name="vat_<?=$i;?>" type="radio" class="radio" style="margin:0;" value="N" temp="<?=$row['vat_yn'];?>" onclick="_set_vat(<?=$seq;?>);" onkeydown="__enterFocus();" <? if($row['vat_yn'] == 'N'){?>checked<?} ?>>무
					</td>
					<td class="center">
						<input name="amount[]" type="text" value="<?=number_format($row[$io_table.'_amt']);?>" temp="<?=number_format($row[$io_table.'_amt']);?>" maxlength="15" class="number" style="width:100%;" onkeydown="return _income_check_amount(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');" alt="onblur" tag="_set_vat(<?=$seq;?>);">
					</td>
					<td class="center">
						<input name="vat[]" type="text" value="<?=number_format($row[$io_table.'_vat']);?>" temp="<?=number_format($row[$io_table.'_vat']);?>" maxlength="15" class="number" style="width:100%;" readonly>
					</td>
					<td class="center">
						<input name="tot_amt[]" type="text" value="<?=number_format($row[$io_table.'_amt']+$row[$io_table.'_vat']);?>" temp="<?=number_format($row[$io_table.'_amt']+$row[$io_table.'_vat']);?>" maxlength="15" class="number" style="width:100%;" readonly>
					</td>
					<td class="center">
						<input name="taxid[]" type="text" value="<?=$myF->bizStyle($row['taxid']);?>" style="width:100%;" temp="<?=$myF->bizStyle($row['taxid']);?>" maxlength="10" onkeydown="return _income_check_item(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');" alt="taxid">
					</td>
					<td class="center">
						<input name="biz_group[]" type="text" value="<?=$row['biz_group'];?>" style="width:100%;" temp="<?=$row['biz_group'];?>" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');">
					</td>
					<td class="center last">
						<input name="biz_type[]" type="text" value="<?=$row['biz_type'];?>" style="width:100%;" temp="<?=$row['biz_type'];?>" maxlength="20" onfocus="this.select();" onkeydown="return _income_check_item(this, <?=$seq;?>);" onchange="__checkRow(<?=$seq;?>, 'check[]');">
					</td>
				</tr>
				<input name="ent_dt[]"  type="hidden" value="<?=$row[$io_table.'_ent_dt'];?>">
				<input name="ent_seq[]" type="hidden" value="<?=$row[$io_table.'_seq'];?>"><?
			}
		}else{
		?>	<tr>
				<td class="center last" colspan="10">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}

		$conn->row_free();
	?>
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