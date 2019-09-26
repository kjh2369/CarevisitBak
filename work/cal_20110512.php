<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include('../inc/_ed.php');
	include("../inc/_myFun.php");

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != "" ? $_POST['mKind'] : $_SESSION["userCenterKind"][0];
	$mYear = $_POST['mYear'] != '' ? $_POST['mYear'] : date('Y', mkTime());
	$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());
	$mType = $_GET["mType"];

	$setYear[0] = 2010;
	$setYear[1] = date('Y', mkTime());
?>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title" colspan="2">일정표출력(<?=$mType == "s" ? "수급자" : "요양보호사";?>)</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left;" colspan="2">
		<table style="width:100%;">
		<tr>
			<td class="noborder" style="text-align:left; padding-top:10px;">
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
				<input name="mType" type="hidden" value="<?=$mType;?>">
				<select name="mKind" style="width:auto;">
				<?
					for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
						echo "<option value='".$_SESSION["userCenterKind"][$r]."' ".($_SESSION["userCenterKind"][$r] == $mKind ? "selected" : "").">".$_SESSION["userCenterKindName"][$r]."</option>";
					}
				?>
				</select>
				<select name="mYear" style="width:auto;">
				<?
					for($i=$setYear[0]; $i<=$setYear[1]; $i++){
						echo '<option value="'.$i.'" '.($i == $mYear?'selected':'').'>'.$i.'년</option>';
					}
				?>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="serviceCalendarList();">조회</button></span>
			</td>
			<td class="noborder" style="text-align:right; padding-top:10px;">
				<select name="useType" style="width:auto;">
					<option value="n"><?=$mType=='s'?'수급자용':'요양보호사용';?></option>
					<option value="y">관리자용</option>
				</select>

				<select name="printType" style="width:auto;">
					<option value="pdf">PDF</option>
					<option value="html">HTML</option>
				</select>

				<select name="print_month" style="width:auto;">
				<?
					$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01
							,      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02
							,      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03
							,      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04
							,      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05
							,      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06
							,      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07
							,      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08
							,      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09
							,      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10
							,      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11
							,      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12
							  from t01iljung
							 where t01_ccode = '$mCode'
							   and t01_mkind = '$mKind'
							   and t01_sugup_date like '$mYear%'
							   and t01_del_yn = 'N'";

					$mon = $conn->get_array($sql);

					for($i=0; $i<12; $i++){
						if ($mon[$i] > 0){
						?>
							<option value="<?=(($i+1)<10?'0':'').($i+1);?>"><?=($i+1);?>월</option>
						<?
						}
					}
				?>
				</select>
				<select name="detail_yn" style="width:auto;">
					<option value="y">상세</option>
					<option value="n">간략</option>
				</select>
				<span class="btn_pack m icon"><span class="pdf"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="serviceCalendarShow('<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear;?>', document.getElementById('print_month').value, 'all', '<?=$mType;?>', document.getElementById('useType').value, 'pdf', document.getElementById('detail_yn').value);">월별출력</button></span>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left; vertical-align:top;" colspan="2">
		<div id="myBody"></div>
	</td>
</tr>
</table>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	serviceCalendarList();
</script>