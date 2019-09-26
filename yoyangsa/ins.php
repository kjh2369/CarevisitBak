<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include('../inc/_myFun.php');
	include('../inc/_ed.php');

	$mCode  = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind  = $_POST['mKind'];

	$mFromYear = ($_POST["mFromYear"] != "" ? $_POST["mFromYear"] : date("Y", mkTime()));
	$mFromMonth = ($_POST["mFromMonth"] != "" ? $_POST["mFromMonth"] : date("m", mkTime()));
	$mFromDay = ($_POST["mFromDay"] != "" ? $_POST["mFromDay"] : "01");

	$mToDate = explode("-", $myF->dateAdd("month", +1, $mFromDate, "Y-m-d"));

	$mToYear = ($_POST["mToYear"] != "" ? $_POST["mToYear"] : $mToDate[0]);
	$mToMonth = ($_POST["mToMonth"] != "" ? $_POST["mToMonth"] : $mToDate[1]);
	$mToDay = ($_POST["mToDay"] != "" ? $_POST["mToDay"] : $mToDate[2]);
	
	$setYear[0] = 2010;
	$setYear[1] = date('Y', mkTime());
	$setYear[1] = ($setYear[1] > $mFromYear ? $setYear[1] : $mFromYear);
	$setYear[1] = ($setYear[1] > $mToYear ? $setYear[1] : $mToYear);
?>
<script src="../js/salary.js" type="text/javascript"></script>
<form name="f" method="post">
<table style="width:100%;">
<tr>
	<td class="title">영업배상책임보험 가입내역</td>
</tr>
<tr>
	<td class="noborder" style="text-align:left;">
		<table>
		<tr>
			<td class="noborder" style="text-align:left;">
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
				<!--
				<select name="mFromYear" style="width:auto;">
				<?
					for($i=$setYear[0]; $i<=$setYear[1]; $i++){
						echo "<option value='$i' ".($i ==$mFromYear ? "selected" : "").">$i<span>년</span></option>";
					}
				?>
				</select>
				<select name="mFromMonth" style="width:auto;">
				<?
					for($i=1; $i<=12; $i++){
						echo '<option value="'.(($i<10?'0':'').$i).'" '.((($i<10?'0':'').$i)==$mFromMonth?'selected':'').'>'.$i.'월</option>';
					}
				?>
				</select>
				<select name="mFromDay" style="width:auto;">
				<?
					$lastDay = date("t", mkTime(0, 0, 1, $mFromMonth, $mFromDay, $mFromYear));
					for($i=1; $i<=$lastDay; $i++){
						echo "<option value='".(($i < 10 ? "0" : "").$i)."'>$i<span>일</span></option>";
					}
				?>
				</select> ~
				<select name="mToYear" style="width:auto;">
				<?
					for($i=$setYear[0]; $i<=$setYear[1]; $i++){
						echo "<option value='$i' ".($i ==$mToYear ? "selected" : "").">$i<span>년</span></option>";
					}
				?>
				</select>
				<select name="mToMonth" style="width:auto;">
				<?
					for($i=1; $i<=12; $i++){
						echo '<option value="'.(($i<10?'0':'').$i).'" '.((($i<10?'0':'').$i)==$mToMonth?'selected':'').'>'.$i.'월</option>';
					}
				?>
				</select>
				<select name="mToDay" style="width:auto;">
				<?
					$lastDay = date("t", mkTime(0, 0, 1, $mToMonth, $mToDay, $mToYear));
					for($i=1; $i<=$lastDay; $i++){
						echo "<option value='".(($i < 10 ? "0" : "").$i)."'>$i<span>일</span></option>";
					}
				?>
				</select>
				-->
				<span class="label">고용상태</span>
				<select name="stat" style="width:auto;">
				<option value="">전체</option>
				<option value="1">활동</option>
				<option value="2">휴직</option>
				<option value="9">퇴사</option>
				</select>
				<span class="btn_pack m icon"><span class="refresh"></span><button name="btnSearch" type="button" onFocus="this.blur();" onClick="_insJoinList();">검색</button></span>
			</td>
			<td class="noborder" style="text-align:right;">
				<span class="btn_pack m icon"><span class="excel"></span><button name="btnExcel" type="button" onFocus="this.blur();" onClick="alert('준비중입니다.');">엑셀</button></span>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="noborder" style="vertical-align:top;">
		<div id="myBody" style="margin-top:-5px;"></div>
	</td>
</tr>
</table>
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
	_insJoinList();
</script>