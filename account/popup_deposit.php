<?
	include('../inc/_header.php');
	
	$mCode  = $_GET['mCode'];
	$mKind  = $_GET['mKind'];
	$mKey   = $_GET['mKey'];
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);

	$sql = "select m03_name"
		 . ",      m81_name"
		 . ",      m03_jumin"
		 . ",      sum(t13_misu_amt - t13_misu_inamt) as misuAmt"
		 . "  from t13sugupja"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t13_ccode"
		 . "   and m03_mkind = t13_mkind"
		 . "   and m03_jumin = t13_jumin"
		 . " inner join m81gubun"
		 . "    on m81_gbn  = 'LVL'"
		 . "   and m81_code = m03_ylvl"
		 . " where t13_ccode = '".$mCode
		 . "'  and t13_mkind = '".$mKind
		 . "'  and t13_jumin = '".$mJumin
		 . "'  and t13_type  = '2'"
		 . " group by m03_name, m81_name, m03_jumin";
	$conn->query($sql);
	$row = $conn->fetch();
	$sugupjaName  = $row['m03_name'];
	$sugupjaLevel = $row['m81_name'];
	$sugupjaJumin = $row['m03_jumin'];
	$sugupjaAmt   = $row['misuAmt'];
	$conn->row_free();
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<script src="../js/account.js" type="text/javascript"></script>
<form name="f" method="post" action="deposit_save_ok.php">
<table class="view_type1" style="width:100%; height:48px; margin-top:-3px;">
<tr>
<th style="width:20%; height:24px; padding-left:0px; text-align:center;">수급자명</th>
<td style="width:30%; height:24px; padding-left:0px; text-align:center;"><?=$sugupjaName;?></td>
<th style="width:20%; height:24px; padding-left:0px; text-align:center;">등급</th>
<td style="width:30%; height:24px; padding-left:0px; text-align:center;"><?=$sugupjaLevel;?></td>
</tr>
<tr>
<th style="width:20%; height:24px; padding-left:0px; text-align:center;">주민번호</th>
<td style="width:30%; height:24px; padding-left:0px; text-align:center;"><?=getSSNStyle($sugupjaJumin);?></td>
<th style="width:20%; height:24px; padding-left:0px; text-align:center;">총미수액</th>
<td style="width:30%; height:24px; padding-left:0px; text-align:center;"><?=number_format($sugupjaAmt);?></td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:350px; margin-top:0px;">
<tr>
<th style="width:12%; padding-left:0px; text-align:center;">No</th>
<th style="width:20%; padding-left:0px; text-align:center;">미수년월</th>
<th style="width:20%; padding-left:0px; text-align:center;">구분&nbsp;&nbsp;</th>
<th style="width:20%; padding-left:0px; text-align:center;">미수금액&nbsp;&nbsp;</th>
<th style="width:28%; padding-left:0px; text-align:center;">입금액&nbsp;&nbsp;&nbsp;</th>
</tr>
<tr>
<td colspan="5" style="width:100%; height:250px; vertical-align:top; padding:0px;">
	<div style="overflow-x:hidden; overflow-y:scroll; width:100%; height:100%;">
	<table style="width:100%; margin:0px; padding:0px;">
	<?
		$sql = "select t13_pay_date"
			 . ",      t13_bonin_yul"
			 . ",     (select m92_cont"
			 . "         from m92boninyul"
			 . "        where t13_bonin_yul = m92_code"
			 . "          and t13_pay_date between left(m92_sdate, 6) and left(m92_edate, 6)"
			 . "        order by m92_sdate, m92_edate"
			 . "        limit 1) as bonin_cont"
			 . ",      t13_misu_amt - t13_misu_inamt as miAmt"
			 . "  from t13sugupja"
			 . " where t13_ccode = '".$mCode
			 . "'  and t13_mkind = '".$mKind
			 . "'  and t13_jumin = '".$mJumin
			 . "'  and t13_type  = '2'
			     having miAmt > 0
			   "
			 . " order by t13_pay_date";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			echo '<tr>';
			echo '<td style="width:12%; margin:0px; padding:0px; text-align:center;">'.($i+1).'</td>';
			echo '<td style="width:20%; margin:0px; padding:0px; text-align:center;">'.subStr($row['t13_pay_date'],0,4).'년'.subStr($row['t13_pay_date'],4,2).'월</td>';
			echo '<td style="width:20%; margin:0px; padding:0px; text-align:center;">'.$row['bonin_cont'].'</td>';
			echo '<td style="width:20%; margin:0px; padding:0px; text-align:center;">'.number_format($row['miAmt']).'</td>';
			echo '<td style="margin:0px; padding:0px; text-align:center;"><input name="amt[]" type="text" value="0" maxlength="8" class="number" style="width:75px; border:none;" onFocus="document.f.deposit.focus();" readOnly></td>';
			echo '</tr>';
			echo '<input name="mBoninYul[]" type="hidden" value="'.$row['t13_bonin_yul'].'">';
			echo '<input name="miAmt[]"     type="hidden" value="'.$row['miAmt'].'">';
			echo '<input name="mPayDate[]"  type="hidden" value="'.$row['t13_pay_date'].'">';
		}

		$conn->row_free();
	?>
	</table>
	</div>
</td>
</tr>
<tr>
<td colspan="5" style="height:30px; padding-left:8px; text-align:right;">
	<select name="depositType" style="width:82px;" onChange="setDepositAmount(document.f.deposit, '<?=$mCode;?>', '<?=$mKind;?>', '<?=$mKey;?>', this.value);">
	<?
		$depostList = getDepositList();
		
		for($i=0; $i<sizeOf($depostList); $i++){
			echo '<option value="'.$depostList[$i][0].'">'.$depostList[$i][1].'</option>';
		}
	?>
	</select>
	<input name="deposit" type="text" value="0" style="width:60px; height:21px;" maxlength="8" class="number" onKeyDown="if(this.value.substring(0,1) == '' || this.value.substring(0,1) == '0'){__onlyNumber(this, '-');}else{__onlyNumber(this);}" onFocus="__commaUnset(this);" onBlur="__commaSet(this);">
	<input type="button" value="입금" onClick="setDeposit();" style="width:59px; height:21px; /*height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;*/">
	<input type="button" value="저장" onClick="regDeposit();" style="width:59px; height:21px; /*height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;*/">
	<input type="button" value="닫기" onClick="window.close();" style="width:59px; height:21px; /*height:21px; border:0px; background:url('../image/btn8.gif') no-repeat; cursor:pointer;*/">
</td>
</tr>
</table>
<input name="mCode" type="hidden" value="<?=$mCode;?>">
<input name="mKind" type="hidden" value="<?=$mKind;?>">
<input name="mKey"  type="hidden" value="<?=$mKey;?>">
</form>
<?
	include("../inc/_footer.php");
?>
<script>self.focus();</script>